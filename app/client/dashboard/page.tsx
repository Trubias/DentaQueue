'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import Link from 'next/link'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

export default function ClientDashboard() {
  const [user, setUser] = useState(null)
  const [profile, setProfile] = useState(null)
  const [next, setNext] = useState(null)
  const [position, setPosition] = useState(null)
  const [appointments, setAppointments] = useState([])
  const [announcements, setAnnouncements] = useState([])
  const [unread, setUnread] = useState(0)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    async function load() {
      const { data: { user: u } } = await supabase.auth.getUser()
      if (!u) return
      setUser(u)

      const { data: prof } = await supabase.from('profiles').select('*').eq('id', u.id).single()
      setProfile(prof)

      // Auto-miss past assigned appointments
      await supabase.from('appointments')
        .update({ status: 'missed' })
        .eq('user_id', u.id).eq('status', 'assigned')
        .not('scheduled_at', 'is', null)
        .lt('scheduled_at', new Date().toISOString())

      // Active appointment (pending or assigned)
      const { data: appts } = await supabase.from('appointments')
        .select('*').eq('user_id', u.id).order('created_at')
      const active = (appts ?? []).find(a => ['pending','assigned'].includes(a.status)) ?? null
      setNext(active)
      setAppointments(appts ?? [])

      // Queue position
      if (active?.status === 'pending') {
        const { count } = await supabase.from('appointments')
          .select('id', { count: 'exact' })
          .eq('status', 'pending')
          .lte('created_at', active.created_at)
        setPosition(count ?? 1)
      }

      // Notifications — include user-specific AND broadcasts (user_id IS NULL)
      const { data: ann } = await supabase.from('announcements')
        .select('*')
        .or(`user_id.eq.${u.id},user_id.is.null`)
        .not('sent_at', 'is', null)
        .order('sent_at', { ascending: false }).limit(3)
      setAnnouncements(ann ?? [])
      const { count: unreadCount } = await supabase.from('announcements')
        .select('id', { count: 'exact' })
        .or(`user_id.eq.${u.id},user_id.is.null`)
        .eq('read', false)
      setUnread(unreadCount ?? 0)

      setLoading(false)
    }
    load()
  }, [])

  const handleCancel = async (id) => {
    if (!confirm('Cancel this appointment?')) return
    await supabase.from('appointments').delete().eq('id', id)
    toast.success('Appointment cancelled.')
    window.location.reload()
  }

  if (loading) return (
    <>
      <div className="topbar"><h2>🏠 Dashboard</h2></div>
      <div className="page-body" style={{ color: 'var(--text-muted)', textAlign: 'center', marginTop: '3rem' }}>Loading…</div>
    </>
  )

  const statusBadge = (s) => <span className={`badge badge-${s}`}>{s}</span>

  return (
    <>
      <div className="topbar">
        <h2>🏠 Welcome, {profile?.name ?? 'Patient'}!</h2>
        <span style={{ fontSize: '.8rem', color: 'var(--text-muted)' }}>{profile?.uid}</span>
      </div>
      <div className="page-body">
        {/* Active Appointment Status Card */}
        <div className="card" style={{ marginBottom: '1.25rem', background: next ? 'linear-gradient(135deg, var(--primary) 0%, #0369a1 100%)' : undefined, color: next ? '#fff' : undefined }}>
          <div className="card-body">
            {!next ? (
              <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                <div>
                  <div style={{ fontSize: '1rem', fontWeight: 600 }}>No active appointment</div>
                  <div style={{ fontSize: '.875rem', opacity: .7, marginTop: '.25rem' }}>Book one to get started.</div>
                </div>
                <Link href="/client/book" className="btn btn-primary">📝 Book Now</Link>
              </div>
            ) : (
              <div>
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '.75rem' }}>
                  <div>
                    <div style={{ fontSize: '1.1rem', fontWeight: 700 }}>
                      {next.status === 'pending' ? `🪑 Queue Position #${position}` : '📅 Appointment Confirmed'}
                    </div>
                    <div style={{ opacity: .8, fontSize: '.9rem' }}>{next.type} — {next.fullname}</div>
                  </div>
                  <span style={{ background: 'rgba(255,255,255,.2)', color: '#fff', padding: '.3rem .9rem', borderRadius: '20px', fontSize: '.8rem', fontWeight: 600 }}>
                    {next.status}
                  </span>
                </div>
                {next.status === 'assigned' && next.scheduled_at && (
                  <div style={{ background: 'rgba(255,255,255,.15)', borderRadius: '8px', padding: '.65rem 1rem', fontSize: '.9rem' }}>
                    📅 Scheduled: <strong>{new Date(next.scheduled_at).toLocaleString()}</strong>
                  </div>
                )}
                <div style={{ marginTop: '.75rem', display: 'flex', gap: '.5rem' }}>
                  <Link href={`/client/appointments`} className="btn btn-sm" style={{ background: 'rgba(255,255,255,.2)', color: '#fff' }}>✏️ Edit</Link>
                  <button className="btn btn-sm" style={{ background: 'rgba(255,0,0,.3)', color: '#fff' }} onClick={() => handleCancel(next.id)}>✕ Cancel</button>
                </div>
              </div>
            )}
          </div>
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.25rem' }}>
          {/* Recent Appointments */}
          <div className="card">
            <div className="card-header">
              <h3>📋 My Appointments</h3>
              <Link href="/client/appointments" className="btn btn-outline btn-sm">View All</Link>
            </div>
            <div className="card-body" style={{ padding: 0 }}>
              {appointments.length === 0
                ? <div style={{ padding: '1.5rem', color: 'var(--text-muted)' }}>No appointments yet.</div>
                : <table>
                    <thead><tr><th>ID</th><th>Type</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                      {appointments.slice(0, 5).map(a => (
                        <tr key={a.id}>
                          <td>#{String(a.id).padStart(3, '0')}</td>
                          <td>{a.type}</td>
                          <td>{statusBadge(a.status)}</td>
                          <td style={{ fontSize: '.8rem' }}>{a.scheduled_at ? new Date(a.scheduled_at).toLocaleDateString() : '—'}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
              }
            </div>
          </div>

          {/* Notifications */}
          <div className="card">
            <div className="card-header">
              <h3>🔔 Notifications {unread > 0 && <span style={{ background: '#ef4444', color: '#fff', borderRadius: '20px', fontSize: '.7rem', padding: '1px 8px', marginLeft: '.5rem' }}>{unread}</span>}</h3>
              <Link href="/client/notifications" className="btn btn-outline btn-sm">View All</Link>
            </div>
            <div className="card-body">
              {announcements.length === 0
                ? <div style={{ color: 'var(--text-muted)' }}>No notifications yet.</div>
                : announcements.map(a => (
                    <div key={a.id} style={{ padding: '.75rem 0', borderBottom: '1px solid var(--border)' }}>
                      <div style={{ fontWeight: 600, fontSize: '.9rem' }}>{a.title}</div>
                      <div style={{ color: 'var(--text-muted)', fontSize: '.8rem', marginTop: '.2rem' }}>{a.body?.substring(0, 80)}…</div>
                      <div style={{ fontSize: '.75rem', color: 'var(--text-muted)', marginTop: '.25rem' }}>{new Date(a.sent_at).toLocaleString()}</div>
                    </div>
                  ))
              }
            </div>
          </div>
        </div>
      </div>
    </>
  )
}
