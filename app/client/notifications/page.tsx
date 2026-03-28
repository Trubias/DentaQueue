'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

type Notification = {
  id: number
  user_id: string | null
  title: string
  body: string
  sent_at: string
  read: boolean
}

export default function ClientNotificationsPage() {
  const [notifications, setNotifications] = useState<Notification[]>([])
  const [selected, setSelected] = useState<Notification | null>(null)
  const [loading, setLoading] = useState(true)
  const [userId, setUserId] = useState<string | null>(null)
  const router = useRouter()

  const load = async (uid: string) => {
    // Load both user-specific AND broadcast (user_id IS NULL) notifications that are UNREAD
    const { data } = await supabase
      .from('announcements')
      .select('*')
      .or(`user_id.eq.${uid},user_id.is.null`)
      .not('sent_at', 'is', null)
      .eq('read', false)
      .order('sent_at', { ascending: false })
    setNotifications(data ?? [])
    setLoading(false)
  }

  useEffect(() => {
    supabase.auth.getUser().then(({ data: { user } }) => {
      if (user) { setUserId(user.id); load(user.id) }
    })
  }, [])

  const openNotification = async (n: Notification) => {
    setSelected(n)
    if (!n.read) {
      // Only update in DB if it's a personal notification, not a global broadcast
      if (n.user_id !== null) {
        await supabase.from('announcements').update({ read: true }).eq('id', n.id)
      }
      // Remove it instantly from the list
      setNotifications(prev => prev.filter(x => x.id !== n.id))
      // Force Next.js layout to refetch unreadCount for the sidebar
      router.refresh()
    }
  }

  const clearAll = async () => {
    if (!userId) return
    // Mark personal notifications as read
    await supabase.from('announcements')
      .update({ read: true })
      .eq('user_id', userId)
      .eq('read', false)
    // Clear list
    setNotifications([])
    toast.success('All notifications cleared.')
    router.refresh()
  }

  const unreadCount = notifications.filter(n => !n.read).length

  return (
    <>
      <div className="topbar">
        <h2>🔔 Notifications {unreadCount > 0 && <span style={{ background: '#ef4444', color: '#fff', borderRadius: '20px', fontSize: '.7rem', padding: '2px 8px', marginLeft: '.5rem' }}>{unreadCount}</span>}</h2>
        {unreadCount > 0 && (
          <button className="btn btn-outline btn-sm" onClick={clearAll}>✓ Mark all read</button>
        )}
      </div>
      <div className="page-body">
        <div className="card">
          <div className="card-body" style={{ padding: 0 }}>
            {loading
              ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>Loading…</div>
              : notifications.length === 0
                ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>
                    No notifications yet.
                  </div>
                : notifications.map(n => (
                    <div key={n.id}
                      onClick={() => openNotification(n)}
                      style={{
                        padding: '1rem 1.5rem', borderBottom: '1px solid var(--border)',
                        cursor: 'pointer', background: n.read ? '#fff' : '#eff6ff',
                        display: 'flex', alignItems: 'flex-start', gap: '1rem',
                        transition: 'background .15s',
                      }}
                    >
                      <div style={{ fontSize: '1.4rem', marginTop: '.1rem' }}>
                        {n.user_id === null ? '📡' : n.read ? '📩' : '📬'}
                      </div>
                      <div style={{ flex: 1 }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '.5rem' }}>
                          <span style={{ fontWeight: n.read ? 400 : 700, fontSize: '.9rem' }}>{n.title}</span>
                          {n.user_id === null && (
                            <span style={{ fontSize: '.7rem', background: '#dbeafe', color: '#1d4ed8', borderRadius: '6px', padding: '1px 7px' }}>
                              📡 Broadcast
                            </span>
                          )}
                        </div>
                        <div style={{ fontSize: '.8rem', color: 'var(--text-muted)', marginTop: '.2rem' }}>
                          {n.body?.substring(0, 90)}{(n.body?.length ?? 0) > 90 ? '…' : ''}
                        </div>
                        <div style={{ fontSize: '.75rem', color: 'var(--text-muted)', marginTop: '.3rem' }}>
                          {new Date(n.sent_at).toLocaleString()}
                        </div>
                      </div>
                      <div style={{ width: 9, height: 9, background: 'var(--primary)', borderRadius: '50%', marginTop: '.4rem', flexShrink: 0 }} />
                    </div>
                  ))
            }
          </div>
        </div>
      </div>

      {selected && (
        <div className="modal-backdrop" onClick={() => setSelected(null)}>
          <div className="modal-box" onClick={e => e.stopPropagation()}>
            <div className="modal-title">
              {selected.user_id === null ? '📡 ' : '📬 '}
              {selected.title}
            </div>
            {selected.user_id === null && (
              <div style={{ background: '#dbeafe', color: '#1e40af', borderRadius: '6px', padding: '.5rem .75rem', fontSize: '.8rem', marginBottom: '1rem' }}>
                📡 This is a broadcast announcement sent to all patients.
              </div>
            )}
            <p style={{ color: 'var(--text-muted)', fontSize: '.8rem', marginBottom: '1rem' }}>
              {new Date(selected.sent_at).toLocaleString()}
            </p>
            <div style={{ lineHeight: 1.7, whiteSpace: 'pre-wrap' }}>{selected.body}</div>
            <button className="btn btn-outline" style={{ marginTop: '1.5rem' }} onClick={() => setSelected(null)}>Close</button>
          </div>
        </div>
      )}
    </>
  )
}
