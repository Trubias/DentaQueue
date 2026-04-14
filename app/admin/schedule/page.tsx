'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState, useCallback } from 'react'
import { Calendar, dateFnsLocalizer } from 'react-big-calendar'
import { format, parse, startOfWeek, getDay } from 'date-fns'
import { enUS } from 'date-fns/locale'
import 'react-big-calendar/lib/css/react-big-calendar.css'
import { getCalendarEvents, updateAppointment, deleteAppointment } from '@/lib/appointments'
import { createAnnouncement } from '@/lib/announcements'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

const locales = { 'en-US': enUS }
const localizer = dateFnsLocalizer({ format, parse, startOfWeek, getDay, locales })

export default function AdminSchedulePage() {
  const [events, setEvents] = useState([])
  const [loading, setLoading] = useState(true)
  const [showCreate, setShowCreate] = useState(false)
  const [newEvent, setNewEvent] = useState({ title: '', start: '', type: 'General' })
  const [selected, setSelected] = useState(null)
  const [currentDate, setCurrentDate] = useState(new Date())
  const [currentView, setCurrentView] = useState('month')

  const load = async () => {
    setLoading(true)
    try { setEvents(await getCalendarEvents()) }
    catch (e) { toast.error('Failed to load events') }
    finally { setLoading(false) }
  }

  useEffect(() => { load() }, [])

  const handleEventDrop = async ({ event, start }) => {
    const dt = new Date(start)
    if (dt.getDay() === 0) { toast.error('Sundays are unavailable'); return }
    const h = dt.getHours()
    if (h < 9 || h >= 18) { toast.error('Outside business hours (09:00-17:59)'); return }
    try {
      await updateAppointment(event.id, { scheduled_at: start.toISOString(), status: 'assigned' })
      toast.success('Appointment rescheduled')
      load()
    } catch (e) { toast.error(e.message) }
  }

  const handleCreate = async (e) => {
    e.preventDefault()
    const dt = new Date(newEvent.start)
    if (dt.getDay() === 0) { toast.error('Sundays are unavailable'); return }
    const h = dt.getHours()
    if (h < 9 || h >= 18) { toast.error('Outside business hours (09:00-17:59)'); return }
    try {
      const { data, error } = await supabase.from('appointments').insert({
        fullname: newEvent.title, type: newEvent.type,
        status: 'assigned', scheduled_at: dt.toISOString(),
      }).select().single()
      if (error) throw error
      toast.success('Event created'); setShowCreate(false); load()
    } catch (e) { toast.error(e.message) }
  }

  const handleDeleteEvent = async () => {
    if (!selected) return
    const appt = selected.resource
    try {
      await updateAppointment(appt.id, { status: 'cancelled' })

      // Send in-app notification to the patient if they have an account
      if (appt.user_id) {
        const scheduledLabel = appt.scheduled_at
          ? new Date(appt.scheduled_at).toLocaleString('en-PH', {
              weekday: 'long', year: 'numeric', month: 'long',
              day: 'numeric', hour: '2-digit', minute: '2-digit',
            })
          : 'a previously scheduled time'

        await createAnnouncement({
          user_id: appt.user_id,
          appointment_id: appt.id,
          title: '❌ Appointment Cancelled',
          body: `Dear ${appt.fullname}, your ${appt.type} appointment scheduled for ${scheduledLabel} (Appointment #${String(appt.id).padStart(3, '0')}) has been cancelled by the clinic. Please book a new appointment or contact us for more information.`,
          sent_at: new Date().toISOString(),
          read: false,
        })
      }

      toast.success('Appointment cancelled & patient notified.')
      setSelected(null)
      load()
    } catch (e) { toast.error(e.message) }
  }

  return (
    <>
      <div className="topbar">
        <h2>📅 Schedule / Calendar</h2>
      </div>
      <div className="page-body">
        <div className="card">
          <div className="card-body">
            {loading ? <div style={{ textAlign: 'center', color: 'var(--text-muted)' }}>Loading calendar…</div>
              : (
                <div style={{ height: 620 }}>
                  <Calendar
                    localizer={localizer}
                    events={events}
                    date={currentDate}
                    onNavigate={newDate => setCurrentDate(newDate)}
                    startAccessor="start"
                    endAccessor={e => new Date(e.start.getTime() + 30 * 60000)}
                    views={['month', 'week', 'day']}
                    view={currentView}
                    onView={newView => setCurrentView(newView)}
                    draggableAccessor={() => true}
                    onEventDrop={handleEventDrop}
                    onSelectEvent={e => setSelected(e)}
                    eventPropGetter={e => ({
                      style: {
                        background: e.resource?.status === 'assigned' ? 'var(--primary)' : '#94a3b8',
                        border: 'none', borderRadius: '6px', fontSize: '.78rem', padding: '2px 6px'
                      }
                    })}
                  />
                </div>
              )
            }
          </div>
        </div>
      </div>

      {showCreate && (
        <div className="modal-backdrop" onClick={() => setShowCreate(false)}>
          <div className="modal-box" onClick={e => e.stopPropagation()}>
            <div className="modal-title">+ New Schedule Event</div>
            <form onSubmit={handleCreate}>
              <div className="form-group">
                <label className="form-label">Patient Name / Title</label>
                <input className="form-control" required
                  value={newEvent.title} onChange={e => setNewEvent(n => ({ ...n, title: e.target.value }))} />
              </div>
              <div className="form-group">
                <label className="form-label">Date & Time</label>
                <input type="datetime-local" className="form-control" required
                  value={newEvent.start} onChange={e => setNewEvent(n => ({ ...n, start: e.target.value }))} />
              </div>
              <div className="form-group">
                <label className="form-label">Type</label>
                <select className="form-control" value={newEvent.type}
                  onChange={e => setNewEvent(n => ({ ...n, type: e.target.value }))}>
                  {['General', 'Cleaning', 'Extraction', 'Filling', 'Braces', 'Root Canal', 'Crown', 'Consultation'].map(t =>
                    <option key={t}>{t}</option>
                  )}
                </select>
              </div>
              <div className="actions-row">
                <button type="submit" className="btn btn-primary">Create</button>
                <button type="button" className="btn btn-outline" onClick={() => setShowCreate(false)}>Cancel</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {selected && (
        <div className="modal-backdrop" onClick={() => setSelected(null)}>
          <div className="modal-box" onClick={e => e.stopPropagation()}>
            <div className="modal-title">📋 Appointment Details</div>
            <p><strong>Patient:</strong> {selected.resource?.fullname}</p>
            <p><strong>Type:</strong> {selected.resource?.type}</p>
            <p><strong>Status:</strong> <span className={`badge badge-${selected.resource?.status}`}>{selected.resource?.status}</span></p>
            <p><strong>Scheduled:</strong> {new Date(selected.start).toLocaleString()}</p>
            <div className="actions-row" style={{ marginTop: '1.5rem' }}>
              {selected.resource?.status !== 'completed' && (
                <button className="btn btn-danger" onClick={handleDeleteEvent}>Cancel Appointment</button>
              )}
              <button className="btn btn-outline" onClick={() => setSelected(null)}>Close</button>
            </div>
          </div>
        </div>
      )}
    </>
  )
}
