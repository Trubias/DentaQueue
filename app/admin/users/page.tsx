'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

type Profile = {
  id: string
  uid: string
  name: string
  role: string
  age: number | null
  sex: string | null
  created_at: string
}

type Appointment = {
  id: number
  type: string
  status: string
  scheduled_at: string | null
}

export default function AdminUsersPage() {
  const [users, setUsers] = useState<Profile[]>([])
  const [search, setSearch] = useState('')
  const [loading, setLoading] = useState(true)
  const [viewUser, setViewUser] = useState<Profile | null>(null)
  const [userAppts, setUserAppts] = useState<Appointment[]>([])
  const [roleFilter, setRoleFilter] = useState<'client' | 'admin' | 'all'>('client')

  const load = async () => {
    setLoading(true)
    try {
      let q = supabase.from('profiles').select('*').order('created_at')
      if (roleFilter !== 'all') {
        q = q.eq('role', roleFilter)
      }
      if (search) {
        q = q.or(`name.ilike.%${search}%,uid.ilike.%${search}%`)
      }
      const { data, error } = await q
      if (error) {
        console.error("Profiles error:", error)
        throw error
      }
      setUsers(data ?? [])
    } catch (e: any) {
      toast.error('Failed to load users: ' + (e.message || 'Unknown error'))
      console.error(e)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    const t = setTimeout(load, 300)
    return () => clearTimeout(t)
  }, [search, roleFilter])

  const handleDelete = async (user: Profile) => {
    if (user.role === 'admin') { toast.error('Cannot delete admin accounts.'); return }
    if (!confirm(`Delete user ${user.name}? This cannot be undone.`)) return
    try {
      const r = await fetch(`/api/admin/users/${user.id}`, { method: 'DELETE' })
      if (!r.ok) throw new Error((await r.json()).error)
      toast.success('User deleted.'); load()
    } catch (e: any) { toast.error(e.message) }
  }

  const openUserAppts = async (user: Profile) => {
    setViewUser(user)
    const { data } = await supabase
      .from('appointments')
      .select('*')
      .eq('user_id', user.id)
      .neq('status', 'cancelled')
      .order('created_at', { ascending: false })
    setUserAppts(data ?? [])
  }

  const markDone = async (appt: Appointment) => {
    await supabase.from('appointments').update({ status: 'completed' }).eq('id', appt.id)
    setUserAppts(a => a.map(x => x.id === appt.id ? { ...x, status: 'completed' } : x))
    
    if (viewUser?.id) {
      const scheduledStr = appt.scheduled_at 
        ? new Date(appt.scheduled_at).toLocaleString('en-US', { dateStyle: 'full', timeStyle: 'short' }) 
        : 'an open date'
      await supabase.from('announcements').insert({
        user_id: viewUser.id,
        title: '✅ Appointment Completed',
        body: `Dear ${viewUser.name}, your ${appt.type} appointment scheduled for ${scheduledStr} has been successfully completed!`,
        sent_at: new Date().toISOString(),
        read: false
      })
    }
    
    toast.success('Marked as completed & patient notified.')
  }

  const statusBadge = (s: string) => <span className={`badge badge-${s}`}>{s}</span>
  const roleBadge = (r: string) => <span className={`badge badge-${r}`}>{r}</span>

  return (
    <>
      <div className="topbar">
        <h2>👥 User Management</h2>
      </div>
      <div className="page-body">
        {/* Role Filter Tabs */}
        <div style={{ display: 'flex', gap: '.5rem', marginBottom: '1rem', flexWrap: 'wrap', alignItems: 'center' }}>
          {(['client', 'admin', 'all'] as const).map(role => (
            <button
              key={role}
              className={`btn btn-sm ${roleFilter === role ? 'btn-primary' : 'btn-outline'}`}
              onClick={() => setRoleFilter(role)}
            >
              {role === 'client' ? '🧑‍⚕️ Patients/Clients' : role === 'admin' ? '🔐 Admins' : '👥 All Users'}
            </button>
          ))}
        </div>

        <div className="card">
          <div className="card-header">
            <h3>
              {roleFilter === 'client' ? 'Registered Patients' : roleFilter === 'admin' ? 'Admin Accounts' : 'All Registered Users'}
            </h3>
            <input
              className="form-control" style={{ width: '250px' }}
              placeholder="Search by name or UID…"
              value={search} onChange={e => setSearch(e.target.value)}
            />
          </div>
          <div className="card-body" style={{ padding: 0 }}>
            {loading ? (
              <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>Loading…</div>
            ) : users.length === 0 ? (
              <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>No users found.</div>
            ) : (
              <div className="table-wrapper">
                <table>
                  <thead>
                    <tr>
                      <th>UID</th>
                      <th>Name</th>
                      <th>Role</th>
                      <th>Age</th>
                      <th>Sex</th>
                      <th>Joined</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {users.map(u => (
                      <tr key={u.id}>
                        <td><code style={{ fontSize: '.8rem' }}>{u.uid}</code></td>
                        <td><strong>{u.name}</strong></td>
                        <td>{roleBadge(u.role)}</td>
                        <td>{u.age ?? '—'}</td>
                        <td>{u.sex ?? '—'}</td>
                        <td style={{ fontSize: '.8rem' }}>{u.created_at ? new Date(u.created_at).toLocaleDateString() : '—'}</td>
                        <td>
                          <div className="actions-row">
                            {u.role !== 'admin' && (
                              <>
                                <button className="btn btn-outline btn-sm" onClick={() => openUserAppts(u)}>📋 Appointments</button>
                                <button className="btn btn-danger btn-sm" onClick={() => handleDelete(u)}>🗑</button>
                              </>
                            )}
                          </div>
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

      {viewUser && (
        <div className="modal-backdrop" onClick={() => setViewUser(null)}>
          <div className="modal-box" style={{ maxWidth: '700px' }} onClick={e => e.stopPropagation()}>
            <div className="modal-title">📋 {viewUser.name}'s Appointments</div>
            {userAppts.length === 0 ? (
              <p style={{ color: 'var(--text-muted)' }}>No appointments found for this user.</p>
            ) : (
              <div className="table-wrapper" style={{ maxHeight: '360px', overflowY: 'auto' }}>
                <table>
                  <thead>
                    <tr><th>ID</th><th>Type</th><th>Status</th><th>Scheduled</th><th>Action</th></tr>
                  </thead>
                  <tbody>
                    {userAppts.map(a => (
                      <tr key={a.id}>
                        <td>#{String(a.id).padStart(3, '0')}</td>
                        <td>{a.type}</td>
                        <td>{statusBadge(a.status)}</td>
                        <td style={{ fontSize: '.8rem' }}>{a.scheduled_at ? new Date(a.scheduled_at).toLocaleString() : '—'}</td>
                        <td>
                          {a.status === 'assigned' && (
                            <button className="btn btn-success btn-sm" onClick={() => markDone(a)}>✅ Done</button>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
            <button className="btn btn-outline" style={{ marginTop: '1.25rem' }} onClick={() => setViewUser(null)}>Close</button>
          </div>
        </div>
      )}
    </>
  )
}
