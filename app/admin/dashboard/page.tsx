'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import Link from 'next/link'
import supabase from '@/lib/supabaseClient'
import { getDashboardStats, getUpcomingAssigned, getAllAppointments } from '@/lib/appointments'

export default function AdminDashboard() {
  const [stats, setStats] = useState({ today_appointments: 0, queue_length: 0 })
  const [upcoming, setUpcoming] = useState([])
  const [recent, setRecent] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    async function load() {
      try {
        const [s, up, all] = await Promise.all([
          getDashboardStats(),
          getUpcomingAssigned(5),
          getAllAppointments({ status: 'pending' }),
        ])
        setStats(s)
        setUpcoming(up)
        setRecent(all.slice(0, 10))
      } catch (e) { console.error(e) }
      finally { setLoading(false) }
    }
    load()
  }, [])

  const statusBadge = (s) => <span className={`badge badge-${s}`}>{s}</span>

  return (
    <>
      <div className="topbar">
        <h2>📊 Admin Dashboard</h2>
        <span style={{ fontSize: '.8rem', color: 'var(--text-muted)' }}>
          {new Date().toLocaleDateString('en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
        </span>
      </div>
      <div className="page-body">
        {/* Stats */}
        <div className="stats-grid">
          <div className="stat-card">
            <div className="stat-icon blue">📋</div>
            <div>
              <div className="stat-value">{stats.today_appointments}</div>
              <div className="stat-label">Today's Appointments</div>
            </div>
          </div>
          <div className="stat-card">
            <div className="stat-icon orange">🪑</div>
            <div>
              <div className="stat-value">{stats.queue_length}</div>
              <div className="stat-label">In Queue (Pending)</div>
            </div>
          </div>
          <div className="stat-card">
            <div className="stat-icon green">📅</div>
            <div>
              <div className="stat-value">{upcoming.length}</div>
              <div className="stat-label">Upcoming Scheduled</div>
            </div>
          </div>
        </div>

        <div className="dashboard-bottom-grid">
          {/* Upcoming */}
          <div className="card">
            <div className="card-header">
              <h3>📅 Upcoming Appointments</h3>
              <Link href="/admin/schedule" className="btn btn-outline">View Schedule</Link>
            </div>
            <div className="card-body" style={{ padding: 0 }}>
              {loading ? <div style={{ padding: '1.5rem', color: 'var(--text-muted)' }}>Loading…</div>
                : upcoming.length === 0
                  ? <div style={{ padding: '1.5rem', color: 'var(--text-muted)' }}>No upcoming appointments.</div>
                  : <table>
                      <thead><tr><th>Patient</th><th>Type</th><th>Scheduled</th></tr></thead>
                      <tbody>
                        {upcoming.map(a => (
                          <tr key={a.id}>
                            <td><strong>#{String(a.id).padStart(3, '0')}</strong> {a.fullname}</td>
                            <td>{a.type}</td>
                            <td style={{ fontSize: '.8rem' }}>{a.scheduled_at ? new Date(a.scheduled_at).toLocaleString() : '—'}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
              }
            </div>
          </div>

          {/* Pending Queue */}
          <div className="card">
            <div className="card-header">
              <h3>🪑 Pending Queue</h3>
              <Link href="/admin/queue" className="btn btn-primary">Manage Queue</Link>
            </div>
            <div className="card-body" style={{ padding: 0 }}>
              {loading ? <div style={{ padding: '1.5rem', color: 'var(--text-muted)' }}>Loading…</div>
                : recent.length === 0
                  ? <div style={{ padding: '1.5rem', color: 'var(--text-muted)' }}>Queue is empty.</div>
                  : <table>
                      <thead><tr><th>#</th><th>Patient</th><th>Type</th><th>Status</th></tr></thead>
                      <tbody>
                        {recent.map((a, i) => (
                          <tr key={a.id}>
                            <td>{i + 1}</td>
                            <td>{a.fullname}</td>
                            <td>{a.type}</td>
                            <td>{statusBadge(a.status)}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
              }
            </div>
          </div>
        </div>
      </div>
    </>
  )
}
