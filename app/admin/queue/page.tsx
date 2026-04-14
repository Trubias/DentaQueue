'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import supabase from '@/lib/supabaseClient'
import { getAllAppointments, updateAppointment, deleteAppointment } from '@/lib/appointments'
import { createAnnouncement } from '@/lib/announcements'
import toast from 'react-hot-toast'

type Appointment = {
  id: number
  fullname: string
  age: number | null
  sex: string | null
  type: string
  status: string
  note: string | null
  scheduled_at: string | null
  user_id: string | null
  profiles?: { name: string; uid: string; email?: string } | null
}

// Fetch patient email via secure server route
async function getPatientEmail(userId: string): Promise<string | null> {
  try {
    const res = await fetch(`/api/admin/get-user-email/${userId}`)
    const d = await res.json()
    return d.email ?? null
  } catch {
    return null
  }
}

export default function AdminQueuePage() {
  const [appointments, setAppointments] = useState<Appointment[]>([])
  const [loading, setLoading] = useState(true)
  const [showAll, setShowAll] = useState(false)
  const [assigning, setAssigning] = useState<Appointment | null>(null)
  const [scheduledAt, setScheduledAt] = useState('')

  const load = async () => {
    setLoading(true)
    try {
      const data = await getAllAppointments(showAll ? {} : { status: 'pending' })
      setAppointments(data)
    } catch (e: any) {
      toast.error('Failed to load queue: ' + e.message)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { load() }, [showAll])

  const [conflictWarning, setConflictWarning] = useState(false)

  useEffect(() => {
    async function checkConflict() {
      if (!scheduledAt) {
        setConflictWarning(false)
        return
      }
      const localDate = new Date(scheduledAt)
      const tzOffsetMs = localDate.getTimezoneOffset() * 60000
      const localISO = new Date(localDate.getTime() - tzOffsetMs).toISOString().slice(0, 19)
      const tzSign = localDate.getTimezoneOffset() <= 0 ? '+' : '-'
      const tzAbs = Math.abs(localDate.getTimezoneOffset())
      const tzHH = String(Math.floor(tzAbs / 60)).padStart(2, '0')
      const tzMM = String(tzAbs % 60).padStart(2, '0')
      const scheduledAtWithTZ = `${localISO}${tzSign}${tzHH}:${tzMM}`

      const { data } = await supabase
        .from('appointments')
        .select('id')
        .eq('scheduled_at', scheduledAtWithTZ)
        .in('status', ['assigned', 'completed'])
        .limit(1)

      setConflictWarning(!!data && data.length > 0)
    }
    const timer = setTimeout(checkConflict, 300)
    return () => clearTimeout(timer)
  }, [scheduledAt])

  const handleAssign = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!scheduledAt || !assigning || conflictWarning) return
    const appt = assigning
    try {
      // Convert the local datetime-local string (e.g. "2026-04-14T17:39") to a
    // timezone-aware ISO string (e.g. "2026-04-14T17:39:00+08:00") so that
    // Postgres stores the correct UTC moment and the calendar always shows
    // exactly the date/time the admin selected — no timezone shift.
    const localDate = new Date(scheduledAt)
    const tzOffsetMs = localDate.getTimezoneOffset() * 60000
    const localISO = new Date(localDate.getTime() - tzOffsetMs).toISOString().slice(0, 19)
    const tzSign = localDate.getTimezoneOffset() <= 0 ? '+' : '-'
    const tzAbs = Math.abs(localDate.getTimezoneOffset())
    const tzHH = String(Math.floor(tzAbs / 60)).padStart(2, '0')
    const tzMM = String(tzAbs % 60).padStart(2, '0')
    const scheduledAtWithTZ = `${localISO}${tzSign}${tzHH}:${tzMM}`
    await updateAppointment(appt.id, { status: 'assigned', scheduled_at: scheduledAtWithTZ })

      // Create in-app notification for the patient
      if (appt.user_id) {
        await createAnnouncement({
          user_id: appt.user_id,
          appointment_id: appt.id,
          title: 'Your appointment has been scheduled!',
          body: `Dear ${appt.fullname}, your appointment for ${appt.type} has been confirmed and scheduled for ${new Date(scheduledAt).toLocaleString('en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}. Please arrive 10 minutes early.`,
          sent_at: new Date().toISOString(),
        })

        // Add a Pending Reminder Announcement that admin can send later
        await createAnnouncement({
          user_id: appt.user_id,
          appointment_id: appt.id,
          title: '🗓 Upcoming Appointment Reminder',
          body: `Dear ${appt.fullname}, this is a reminder for your upcoming appointment for ${appt.type} on ${new Date(scheduledAt).toLocaleString('en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}. We look forward to seeing you at DentaQueue Clinic!`,
        })

        // Send email notification
        const email = await getPatientEmail(appt.user_id)
        if (email) {
          await fetch('/api/send-email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              to: email,
              subject: `✅ Appointment Confirmed — DentaQueue`,
              html: `
                <div style="font-family:sans-serif;max-width:600px;margin:auto;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden">
                  <div style="background:#0ea5e9;padding:1.5rem;color:#fff">
                    <h2 style="margin:0">🦷 DentaQueue Clinic</h2>
                    <p style="margin:.5rem 0 0;opacity:.9">Appointment Confirmation</p>
                  </div>
                  <div style="padding:1.5rem">
                    <p>Dear <strong>${appt.fullname}</strong>,</p>
                    <p>Your appointment has been confirmed! Here are the details:</p>
                    <div style="background:#f1f5f9;border-radius:8px;padding:1rem;margin:1rem 0">
                       <p style="margin:.3rem 0">📋 <strong>Service:</strong> ${appt.type}</p>
                       <p style="margin:.3rem 0">📅 <strong>Schedule:</strong> ${new Date(scheduledAt).toLocaleString('en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                       <p style="margin:.3rem 0">🪪 <strong>Appointment #:</strong> ${String(appt.id).padStart(3, '0')}</p>
                    </div>
                    <p style="color:#64748b;font-size:.875rem">Please arrive at least 10 minutes before your scheduled time. If you need to reschedule, please contact us or cancel via the Patient Portal.</p>
                    <p>Thank you for choosing DentaQueue Clinic!</p>
                  </div>
                  <div style="background:#f8fafc;padding:1rem 1.5rem;font-size:.8rem;color:#94a3b8;border-top:1px solid #e2e8f0">
                    DentaQueue Clinic · This is an automated notification.
                  </div>
                </div>
              `,
            }),
          })
        }
      }

      toast.success('Appointment assigned & patient notified.')
      setAssigning(null)
      load()
    } catch (err: any) {
      toast.error(err.message)
    }
  }

  const handleRequeue = async (id: number) => {
    try {
      await updateAppointment(id, { status: 'pending', scheduled_at: null })
      toast.success('Requeued'); load()
    } catch (e: any) { toast.error(e.message) }
  }

  const handleComplete = async (appt: Appointment) => {
    try {
      await updateAppointment(appt.id, { status: 'completed' })
      if (appt.user_id) {
        await createAnnouncement({
          user_id: appt.user_id,
          title: 'Appointment Completed',
          body: `Your appointment for ${appt.type} (#${String(appt.id).padStart(3, '0')}) has been marked as completed. Thank you for visiting DentaQueue Clinic!`,
          sent_at: new Date().toISOString(),
        })
      }
      toast.success('Marked as completed.'); load()
    } catch (e: any) { toast.error(e.message) }
  }

  const handleDelete = async (appt: Appointment) => {
    if (!confirm(`Delete appointment #${String(appt.id).padStart(3, '0')} for ${appt.fullname}?`)) return
    try {
      if (appt.user_id) {
        await createAnnouncement({
          user_id: appt.user_id,
          title: 'Appointment Cancelled',
          body: `Your appointment for ${appt.type} (#${String(appt.id).padStart(3, '0')}) has been cancelled by the clinic. Please contact us or book a new appointment.`,
          sent_at: new Date().toISOString(),
        })
      }
      await deleteAppointment(appt.id)
      toast.success('Appointment deleted.'); load()
    } catch (e: any) { toast.error(e.message) }
  }

  const statusBadge = (s: string) => <span className={`badge badge-${s}`}>{s}</span>

  return (
    <>
      <div className="topbar">
        <h2>🪑 Queue Management</h2>
        <button
          className={`btn btn-sm ${showAll ? 'btn-primary' : 'btn-outline'}`}
          onClick={() => setShowAll(v => !v)}
        >
          {showAll ? '👁 Showing All' : '⏳ Pending Only'}
        </button>
      </div>
      <div className="page-body">
        <div className="card">
          <div className="card-body" style={{ padding: 0 }}>
            {loading
              ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>Loading queue…</div>
              : appointments.length === 0
                ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>
                  {showAll ? 'No appointments found.' : 'Queue is empty — no pending appointments.'}
                </div>
                : (
                  <div className="table-wrapper">
                    <table>
                      <thead>
                        <tr>
                          <th>#</th><th>Patient</th><th>Age</th><th>Sex</th>
                          <th>Type</th><th>Note</th><th>Status</th><th>Scheduled</th><th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        {appointments.map((a, i) => (
                          <tr key={a.id}>
                            <td><strong>{i + 1}</strong></td>
                            <td>
                              <div><strong>{a.fullname}</strong></div>
                              {a.profiles?.uid && <small style={{ color: 'var(--text-muted)' }}>@{a.profiles.uid}</small>}
                            </td>
                            <td>{a.age ?? '—'}</td>
                            <td>{a.sex ?? '—'}</td>
                            <td>{a.type}</td>
                            <td style={{ fontSize: '.8rem', color: 'var(--text-muted)', maxWidth: '120px' }}>
                              {a.note ? a.note.substring(0, 40) + (a.note.length > 40 ? '…' : '') : '—'}
                            </td>
                            <td>{statusBadge(a.status)}</td>
                            <td style={{ fontSize: '.8rem' }}>
                              {a.scheduled_at ? new Date(a.scheduled_at).toLocaleString() : '—'}
                            </td>
                            <td>
                              <div className="actions-row">
                                {a.status === 'pending' && (
                                  <button className="btn btn-primary btn-sm"
                                    onClick={() => { setAssigning(a); setScheduledAt('') }}>
                                    📅 Assign
                                  </button>
                                )}
                                {a.status === 'assigned' && (
                                  <>
                                    <button className="btn btn-success btn-sm" onClick={() => handleComplete(a)}>
                                      ✅ Done
                                    </button>
                                    <button className="btn btn-warning btn-sm" onClick={() => handleRequeue(a.id)}>
                                      🔄 Requeue
                                    </button>
                                  </>
                                )}
                                <button className="btn btn-danger btn-sm" onClick={() => handleDelete(a)}>🗑</button>
                              </div>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                )
            }
          </div>
        </div>
      </div>

      {/* Assign Modal */}
      {assigning && (
        <div className="modal-backdrop" onClick={() => setAssigning(null)}>
          <div className="modal-box" onClick={e => e.stopPropagation()}>
            <div className="modal-title">📅 Assign Appointment</div>
            <p style={{ marginBottom: '1rem', color: 'var(--text-muted)', fontSize: '.9rem' }}>
              Patient: <strong>{assigning.fullname}</strong> — {assigning.type}
            </p>
            <form onSubmit={handleAssign}>
              <div className="form-group">
                <label className="form-label">Schedule Date & Time</label>
                <input
                  type="datetime-local" className="form-control"
                  value={scheduledAt}
                  onChange={(e) => {
                    const newVal = e.target.value;
                    if (!newVal) {
                      setScheduledAt('');
                      return;
                    }
                    if (scheduledAt) {
                      const oldDatePart = scheduledAt.split('T')[0];
                      const newDatePart = newVal.split('T')[0];
                      const oldTimePart = scheduledAt.split('T')[1];
                      const newTimePart = newVal.split('T')[1];
                      
                      const tzOffset = new Date().getTimezoneOffset() * 60000;
                      const nowLocalStr = new Date(Date.now() - tzOffset).toISOString().slice(0, 16);
                      const currentTodayDatePart = nowLocalStr.split('T')[0];

                      // 1. Did the native picker date shift to 'Today'? 
                      // 2. Did the time remain identical? (Which means Chrome's 'Today' button bypassed touching the time)
                      // If so, aggressively override to live current system time.
                      if (newDatePart === currentTodayDatePart && oldDatePart !== currentTodayDatePart && oldTimePart === newTimePart) {
                         setScheduledAt(nowLocalStr);
                         return;
                      }
                    }
                    setScheduledAt(newVal);
                  }}
                  required
                  min={new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().split('T')[0] + 'T00:00'}
                />
                {conflictWarning && (
                  <div style={{ marginTop: '0.75rem', fontSize: '.85rem', color: '#dc2626', background: '#fef2f2', padding: '.75rem', borderRadius: '6px', border: '1px solid #fecaca', display: 'flex', gap: '.5rem', alignItems: 'flex-start' }}>
                    <span style={{ fontSize: '1rem' }}>⚠️</span>
                    <div>
                      <strong>Time Slot Conflict!</strong><br />
                      This exact time slot on this date is already occupied or has already been served by another appointment. You must choose a different date or time before you can proceed.
                    </div>
                  </div>
                )}
              </div>
              <div style={{ background: '#eff6ff', borderRadius: '8px', padding: '.75rem 1rem', fontSize: '.85rem', color: '#1d4ed8', marginBottom: '1rem' }}>
                📧 A confirmation email and in-app notification will be sent to the patient automatically.
              </div>
              <div className="actions-row">
                <button type="submit" className="btn btn-primary" disabled={conflictWarning}>
                  ✅ Confirm Assignment
                </button>
                <button type="button" className="btn btn-outline" onClick={() => setAssigning(null)}>Cancel</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  )
}
