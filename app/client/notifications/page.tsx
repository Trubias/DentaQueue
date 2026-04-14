'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import supabase from '@/lib/supabaseClient'

type Notification = {
  id: number
  user_id: string | null
  title: string
  body: string
  sent_at: string
  read: boolean
}

type TabType = 'unread' | 'read'

export default function ClientNotificationsPage() {
  const [notifications, setNotifications] = useState<Notification[]>([])
  const [selected, setSelected] = useState<Notification | null>(null)
  const [loading, setLoading] = useState(true)
  const [activeTab, setActiveTab] = useState<TabType>('unread')
  const router = useRouter()

  useEffect(() => {
    async function load() {
      const { data: { session } } = await supabase.auth.getSession()
      const uid = session?.user?.id
      if (!uid) return

      // Load all notifications for this user that have been sent
      const { data } = await supabase
        .from('announcements')
        .select('*')
        .eq('user_id', uid)
        .not('sent_at', 'is', null)
        .order('sent_at', { ascending: false })

      setNotifications(data ?? [])
      setLoading(false)


    }
    load()
  }, [])

  const openNotification = async (n: Notification) => {
    setSelected(n)
    if (!n.read) {
      await supabase.from('announcements').update({ read: true }).eq('id', n.id)
      setNotifications(prev => prev.map(x => x.id === n.id ? { ...x, read: true } : x))
      router.refresh()
    }
  }

  const unreadCount = notifications.filter(n => !n.read).length
  const displayedNotifications = notifications.filter(n => activeTab === 'unread' ? !n.read : n.read)

  return (
    <>
      <div className="topbar">
        <h2>🔔 Notifications</h2>
      </div>
      <div className="page-body">
        
        <div style={{ display: 'flex', gap: '.75rem', marginBottom: '1.25rem' }}>
          <button 
            className={`btn btn-sm ${activeTab === 'unread' ? 'btn-primary' : 'btn-outline'}`} 
            onClick={() => setActiveTab('unread')}
            style={{ borderRadius: '6px' }}
          >
            Unread {unreadCount > 0 && <span style={{ marginLeft: '.4rem', background: activeTab === 'unread' ? 'rgba(255,255,255,0.3)' : '#ef4444', color: '#fff', borderRadius: '10px', padding: '1px 6px', fontSize: '.7rem' }}>{unreadCount}</span>}
          </button>
          <button 
            className={`btn btn-sm ${activeTab === 'read' ? 'btn-primary' : 'btn-outline'}`} 
            onClick={() => setActiveTab('read')}
            style={{ borderRadius: '6px' }}
          >
            Read
          </button>
        </div>

        <div className="card">
          <div className="card-body" style={{ padding: 0 }}>
            {loading
              ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>Loading…</div>
              : displayedNotifications.length === 0
                ? <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--text-muted)' }}>
                    {activeTab === 'unread' ? 'No unread notifications.' : 'No read notifications.'}
                  </div>
                : displayedNotifications.map(n => (
                    <div key={n.id}
                      onClick={() => openNotification(n)}
                      style={{
                        padding: '1rem 1.5rem',
                        borderBottom: '1px solid var(--border)',
                        cursor: 'pointer',
                        background: '#fff',
                        display: 'flex',
                        alignItems: 'flex-start',
                        gap: '1rem',
                        transition: 'background .15s',
                      }}
                      onMouseEnter={e => (e.currentTarget.style.background = '#f8fafc')}
                      onMouseLeave={e => (e.currentTarget.style.background = '#fff')}
                    >
                      <div style={{ fontSize: '1.4rem', marginTop: '.1rem' }}>{n.read ? '📩' : '📬'}</div>
                      <div style={{ flex: 1 }}>
                        <div style={{ fontWeight: n.read ? 500 : 700, fontSize: '.9rem' }}>{n.title}</div>
                        <div style={{ fontSize: '.8rem', color: 'var(--text-muted)', marginTop: '.2rem' }}>
                          {n.body?.substring(0, 90)}{(n.body?.length ?? 0) > 90 ? '…' : ''}
                        </div>
                        <div style={{ fontSize: '.75rem', color: 'var(--text-muted)', marginTop: '.3rem' }}>
                          {new Date(n.sent_at).toLocaleString()}
                        </div>
                      </div>
                    </div>
                  ))
            }
          </div>
        </div>
      </div>

      {selected && (
        <div className="modal-backdrop" onClick={() => setSelected(null)}>
          <div className="modal-box" onClick={e => e.stopPropagation()}>
            <div className="modal-title">{selected.read ? '📩' : '📬'} {selected.title}</div>
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
