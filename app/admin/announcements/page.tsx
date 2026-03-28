'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

type Announcement = {
  id: number
  user_id: string | null
  title: string
  body: string
  sent_at: string | null
  created_at: string
  profiles?: { name: string; uid: string; email?: string } | null
}

type Profile = {
  id: string
  name: string
  uid: string
}

export default function AdminAnnouncementsPage() {
  const [items, setItems] = useState<Announcement[]>([])
  const [users, setUsers] = useState<Profile[]>([])
  const [inventory, setInventory] = useState<Record<number, Announcement[]>>({})
  const [view, setView] = useState<'unsent' | 'inventory'>('unsent')
  const [loading, setLoading] = useState(true)
  const [showCreate, setShowCreate] = useState(false)
  const [sending, setSending] = useState(false)
  const [form, setForm] = useState({ user_id: '', title: '', body: '' })
  const [mailTest, setMailTest] = useState({ to: '', subject: 'Message from DentaQueue', body: '' })
  const [showMailTest, setShowMailTest] = useState(false)

  const load = async () => {
    setLoading(true)
    try {
      if (view === 'unsent') {
        const { data, error } = await supabase
          .from('announcements')
          .select('*, profiles(name, uid)')
          .is('sent_at', null)
          .order('created_at', { ascending: false })
        if (error) throw error
        setItems(data ?? [])
      } else {
        const { data, error } = await supabase
          .from('announcements')
          .select('*, profiles(name, uid)')
          .not('sent_at', 'is', null)
          .order('sent_at', { ascending: false })
        if (error) throw error
        const grouped: Record<number, Announcement[]> = {}
          ; (data ?? []).forEach(item => {
            const year = new Date(item.sent_at!).getFullYear()
            if (!grouped[year]) grouped[year] = []
            grouped[year].push(item)
          })
        setInventory(grouped)
      }
    } catch (e: any) {
      toast.error('Failed to load: ' + e.message)
    } finally {
      setLoading(false)
    }
  }

  const loadUsers = async () => {
    const { data } = await supabase.from('profiles').select('id, name, uid').eq('role', 'client').order('name')
    setUsers(data ?? [])
  }

  useEffect(() => { load(); loadUsers() }, [view])

  // Helper: fetch email for a user id via server route
  const getUserEmail = async (userId: string): Promise<string | null> => {
    try {
      const res = await fetch(`/api/admin/get-user-email/${userId}`)
      const d = await res.json()
      return d.email ?? null
    } catch {
      return null
    }
  }

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault()
    setSending(true)
    try {
      const isBroadcast = !form.user_id

      if (isBroadcast) {
        // Create one announcement record per client user
        for (const user of users) {
          const { data: ann } = await supabase.from('announcements').insert({
            user_id: user.id,
            title: form.title,
            body: form.body,
            sent_at: new Date().toISOString(),
          }).select().single()

          // Get email from auth and send
          const email = await getUserEmail(user.id)
          if (email) {
            await fetch('/api/send-email', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                to: email,
                subject: form.title,
                html: `<div style="font-family:sans-serif;max-width:600px;margin:auto">
                  <h2 style="color:#0ea5e9">📢 DentaQueue Announcement</h2>
                  <p>Dear ${user.name},</p>
                  <div style="background:#f1f5f9;border-radius:8px;padding:1rem;margin:1rem 0">${form.body}</div>
                  <p style="color:#64748b;font-size:.85rem">— DentaQueue Clinic</p>
                </div>`,
              }),
            })
          }
        }
        toast.success(`Broadcast sent to ${users.length} patient(s).`)
      } else {
        // Single user announcement
        const { data: ann, error } = await supabase.from('announcements').insert({
          user_id: form.user_id,
          title: form.title,
          body: form.body,
          sent_at: new Date().toISOString(),
        }).select().single()
        if (error) throw error

        const email = await getUserEmail(form.user_id)
        if (email) {
          const user = users.find(u => u.id === form.user_id)
          await fetch('/api/send-email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              to: email,
              subject: form.title,
              html: `<div style="font-family:sans-serif;max-width:600px;margin:auto">
                <h2 style="color:#0ea5e9">📢 DentaQueue Message</h2>
                <p>Dear ${user?.name ?? 'Patient'},</p>
                <div style="background:#f1f5f9;border-radius:8px;padding:1rem;margin:1rem 0">${form.body}</div>
                <p style="color:#64748b;font-size:.85rem">— DentaQueue Clinic</p>
              </div>`,
            }),
          })
          toast.success('Announcement sent & email delivered.')
        } else {
          toast.success('Announcement created. (No email found for user.)')
        }
      }

      setShowCreate(false)
      setForm({ user_id: '', title: '', body: '' })
      load()
    } catch (e: any) {
      toast.error(e.message)
    } finally {
      setSending(false)
    }
  }

  const handleMarkSent = async (ann: Announcement) => {
    setSending(true)
    try {
      if (ann.user_id) {
        const email = await getUserEmail(ann.user_id)
        if (email) {
          await fetch('/api/send-email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              to: email,
              subject: ann.title,
              html: `<div style="font-family:sans-serif;max-width:600px;margin:auto">
                <h2 style="color:#0ea5e9">📢 DentaQueue Reminder</h2>
                <p>Dear ${ann.profiles?.name ?? 'Patient'},</p>
                <div style="background:#f1f5f9;border-radius:8px;padding:1rem;margin:1rem 0">${ann.body}</div>
                <p style="color:#64748b;font-size:.85rem">— DentaQueue Clinic</p>
              </div>`,
            }),
          })
          toast.success('Email sent to patient!')
        } else {
          toast.error('Patient has no email, but notification was marked sent.')
        }
      }
      
      await supabase.from('announcements').update({ sent_at: new Date().toISOString() }).eq('id', ann.id)
      toast.success('Moved to Sent Inventory.')
      load()
    } catch (e: any) { 
      toast.error(e.message) 
    } finally {
      setSending(false)
    }
  }

  const handleDelete = async (id: number) => {
    if (!confirm('Delete this announcement?')) return
    try {
      await supabase.from('announcements').delete().eq('id', id)
      toast.success('Deleted.'); load()
    } catch (e: any) { toast.error(e.message) }
  }

  const sendMailTest = async (e: React.FormEvent) => {
    e.preventDefault()
    try {
      const r = await fetch('/api/send-email', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ to: mailTest.to, subject: mailTest.subject, html: `<p>${mailTest.body}</p>` }),
      })
      const d = await r.json()
      if (d.success) { toast.success('Test email sent!'); setShowMailTest(false) }
      else toast.error(d.message)
    } catch (e: any) { toast.error(e.message) }
  }

  return (
    <>
      <div className="topbar">
        <h2>📢 Announcements</h2>
        <div className="actions-row">
          <button className="btn btn-outline btn-sm" onClick={() => setShowMailTest(true)}>📧 Send Mail</button>
          <button className="btn btn-primary btn-sm" onClick={() => setShowCreate(true)}>+ Create</button>
        </div>
      </div>
      <div className="page-body">
        <div style={{ display: 'flex', gap: '.5rem', marginBottom: '1rem' }}>
          <button
            className={`btn btn-sm ${view === 'unsent' ? 'btn-primary' : 'btn-outline'}`}
            onClick={() => setView('unsent')}
          >📋 Pending</button>
          <button
            className={`btn btn-sm ${view === 'inventory' ? 'btn-primary' : 'btn-outline'}`}
            onClick={() => setView('inventory')}
          >📁 Sent Inventory</button>
        </div>

        {view === 'unsent' && (
          <div className="card">
            <div className="card-body" style={{ padding: 0 }}>
              {loading
                ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>Loading…</div>
                : items.length === 0
                  ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>No pending announcements.</div>
                  : <table>
                    <thead><tr><th>Title</th><th>Recipient</th><th>Created</th><th>Actions</th></tr></thead>
                    <tbody>
                      {items.map(a => (
                        <tr key={a.id}>
                          <td>
                            <strong>{a.title}</strong>
                            <div style={{ fontSize: '.8rem', color: 'var(--text-muted)' }}>{a.body?.substring(0, 60)}…</div>
                          </td>
                          <td>
                            {a.profiles
                              ? <>{a.profiles.name} <small style={{ color: 'var(--text-muted)' }}>{a.profiles.uid}</small></>
                              : <span style={{ color: 'var(--text-muted)' }}>📡 Broadcast (All Users)</span>}
                          </td>
                          <td style={{ fontSize: '.8rem' }}>{new Date(a.created_at).toLocaleDateString()}</td>
                          <td>
                            <div className="actions-row">
                              <button className="btn btn-success btn-sm" onClick={() => handleMarkSent(a)}>📤 Send</button>
                              <button className="btn btn-danger btn-sm" onClick={() => handleDelete(a.id)}>🗑</button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
              }
            </div>
          </div>
        )}

        {view === 'inventory' && (
          <div>
            {loading
              ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>Loading…</div>
              : Object.keys(inventory).length === 0
                ? <div className="card"><div className="card-body" style={{ color: 'var(--text-muted)' }}>No sent announcements yet.</div></div>
                : Object.keys(inventory).sort((a, b) => Number(b) - Number(a)).map(year => (
                  <div key={year} className="card" style={{ marginBottom: '1.25rem' }}>
                    <div className="card-header"><h3>📁 {year}</h3></div>
                    <div className="card-body" style={{ padding: 0 }}>
                      <table>
                        <thead><tr><th>Title</th><th>Recipient</th><th>Sent At</th><th>Actions</th></tr></thead>
                        <tbody>
                          {inventory[Number(year)].map(a => (
                            <tr key={a.id}>
                              <td><strong>{a.title}</strong></td>
                              <td>{a.profiles?.name ?? <span style={{ color: 'var(--text-muted)' }}>📡 Broadcast</span>}</td>
                              <td style={{ fontSize: '.8rem' }}>{new Date(a.sent_at!).toLocaleString()}</td>
                              <td><button className="btn btn-danger btn-sm" onClick={() => handleDelete(a.id)}>🗑</button></td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </div>
                  </div>
                ))
            }
          </div>
        )}
      </div>

      {/* Create Announcement Modal */}
      {showCreate && (
        <div className="modal-backdrop" onClick={() => setShowCreate(false)}>
          <div className="modal-box" onClick={e => e.stopPropagation()}>
            <div className="modal-title">📢 Create Announcement</div>
            <form onSubmit={handleCreate}>
              <div className="form-group">
                <label className="form-label">Recipient</label>
                <select className="form-control" value={form.user_id}
                  onChange={e => setForm(f => ({ ...f, user_id: e.target.value }))}>
                  <option value="">📡 All Patients (Broadcast)</option>
                  {users.map(u => <option key={u.id} value={u.id}>{u.name} ({u.uid})</option>)}
                </select>
                {!form.user_id && (
                  <small style={{ color: 'var(--text-muted)', marginTop: '.35rem', display: 'block' }}>
                    ℹ️ Broadcast will send a notification AND email to all {users.length} registered patient(s).
                  </small>
                )}
              </div>
              <div className="form-group">
                <label className="form-label">Title *</label>
                <input className="form-control" required value={form.title}
                  onChange={e => setForm(f => ({ ...f, title: e.target.value }))} />
              </div>
              <div className="form-group">
                <label className="form-label">Message *</label>
                <textarea className="form-control" required rows={4} value={form.body}
                  onChange={e => setForm(f => ({ ...f, body: e.target.value }))} />
              </div>
              <div className="actions-row">
                <button type="submit" className="btn btn-primary" disabled={sending}>
                  {sending ? 'Sending…' : '📤 Send Now'}
                </button>
                <button type="button" className="btn btn-outline" onClick={() => setShowCreate(false)}>Cancel</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Mail Test Modal */}
      {showMailTest && (
        <div className="modal-backdrop" onClick={() => setShowMailTest(false)}>
          <div className="modal-box" onClick={e => e.stopPropagation()}>
            <div className="modal-title">📧 Mail Test</div>
            <form onSubmit={sendMailTest}>
              <div className="form-group">
                <label className="form-label">Send To</label>
                <input type="email" className="form-control" required value={mailTest.to}
                  onChange={e => setMailTest(m => ({ ...m, to: e.target.value }))} />
              </div>
              <div className="form-group">
                <label className="form-label">Subject</label>
                <input className="form-control" required value={mailTest.subject}
                  onChange={e => setMailTest(m => ({ ...m, subject: e.target.value }))} />
              </div>
              <div className="form-group">
                <label className="form-label">Body</label>
                <textarea className="form-control" rows={3} value={mailTest.body}
                  onChange={e => setMailTest(m => ({ ...m, body: e.target.value }))} />
              </div>
              <div className="actions-row">
                <button type="submit" className="btn btn-primary">Send Test</button>
                <button type="button" className="btn btn-outline" onClick={() => setShowMailTest(false)}>Cancel</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  )
}
