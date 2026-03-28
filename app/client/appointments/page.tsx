'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import supabase from '@/lib/supabaseClient'

const TYPES = ['Cleaning','Extraction','Filling','Braces','Root Canal','Crown','Consultation','General Check-up','Whitening','Other']

export default function ClientAppointmentsPage() {
  const [appointments, setAppointments] = useState([])
  const [loading, setLoading] = useState(true)
  const [userId, setUserId] = useState(null)

  const load = async (uid) => {
    const { data } = await supabase
      .from('appointments')
      .select('*')
      .eq('user_id', uid)
      .order('created_at', { ascending: false })
    setAppointments(data ?? [])
    setLoading(false)
  }

  useEffect(() => {
    supabase.auth.getUser().then(({ data: { user } }) => {
      if (user) {
        setUserId(user.id)
        load(user.id)
      }
    })
  }, [])

  const statusBadge = (s) => <span className={`badge badge-${s}`}>{s}</span>

  return (
    <>
      <div className="topbar"><h2>📋 My Appointments</h2></div>
      <div className="page-body">
        <div className="card">
          <div className="card-body" style={{ padding: 0 }}>
            {loading ? (
              <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>Loading…</div>
            ) : appointments.length === 0 ? (
              <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>
                No appointments yet. <a href="/client/book" style={{ color: 'var(--primary)' }}>Book one now.</a>
              </div>
            ) : (
              <div className="table-wrapper">
                <table>
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Type</th>
                      <th>Name</th>
                      <th>Status</th>
                      <th>Scheduled</th>
                      <th>Created</th>
                    </tr>
                  </thead>
                  <tbody>
                    {appointments.map(a => (
                      <tr key={a.id}>
                        <td><strong>#{String(a.id).padStart(3,'0')}</strong></td>
                        <td>{a.type}</td>
                        <td>{a.fullname}</td>
                        <td>{statusBadge(a.status)}</td>
                        <td style={{ fontSize: '.8rem' }}>
                          {a.scheduled_at ? new Date(a.scheduled_at).toLocaleString() : '—'}
                        </td>
                        <td style={{ fontSize: '.8rem' }}>
                          {new Date(a.created_at).toLocaleDateString()}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      </div>
    </>
  )
}