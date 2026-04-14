'use client'
import { useState, useEffect } from 'react'
import Link from 'next/link'
import { usePathname, useRouter } from 'next/navigation'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

const navItems = [
  { href: '/client/dashboard', icon: '🏠', label: 'Dashboard' },
  { href: '/client/book', icon: '📝', label: 'Book Appointment' },
  { href: '/client/appointments', icon: '📋', label: 'My Appointments' },
  { href: '/client/notifications', icon: '🔔', label: 'Notifications' },
  { href: '/client/settings', icon: '⚙️', label: 'Settings' },
]

export default function ClientSidebar({ profile, unreadCount = 0 }: any) {
  const pathname = usePathname()
  const router = useRouter()
  const [isOpen, setIsOpen] = useState(false)
  const [badge, setBadge] = useState(unreadCount)

  // Keep badge perfectly in sync with server layout props
  useEffect(() => {
    setBadge(unreadCount)
  }, [unreadCount])

  // Initial client side fetch just to be perfectly sure it's accurate on first mount
  useEffect(() => {
    supabase.auth.getSession().then(({ data: { session } }) => {
      const uid = session?.user?.id
      if (!uid) return
      supabase
        .from('announcements')
        .select('id', { count: 'exact' })
        .eq('user_id', uid)
        .eq('read', false)
        .not('sent_at', 'is', null)
        .then(({ count }) => {
          setBadge(count ?? 0)
        })
    })
  }, [])

  const handleLogout = async () => {
    await supabase.auth.signOut()
    toast.success('Logged out')
    router.push('/login')
  }

  const initials = profile?.name
    ? profile.name.split(' ').map((n: string) => n[0]).join('').substring(0, 2).toUpperCase()
    : 'PT'

  return (
    <>
      <button className="hamburger-btn" onClick={() => setIsOpen(true)}>☰</button>
      <div className={`sidebar-overlay ${isOpen ? 'open' : ''}`} onClick={() => setIsOpen(false)} />

      <aside className={`sidebar ${isOpen ? 'sidebar--open' : ''}`}>
        <div className="sidebar-brand">
          <div className="brand-icon" style={{ fontSize: '1.3rem' }}>🦷</div>
          <div>
            <span>DentaQueue</span>
            <small>Patient Portal</small>
          </div>
        </div>

        <nav className="sidebar-nav">
          <div className="nav-section-label">Navigation</div>
          {navItems.map(item => (
            <Link
              key={item.href}
              href={item.href}
              className={`nav-item ${pathname === item.href ? 'active' : ''}`}
              onClick={() => setIsOpen(false)}
            >
              <span className="nav-icon">{item.icon}</span>
              {item.label}
              {item.href === '/client/notifications' && badge > 0 && (
                <span style={{
                  marginLeft: 'auto', background: '#ef4444', color: '#fff',
                  borderRadius: '20px', fontSize: '.7rem', padding: '1px 7px', fontWeight: 700
                }}>{badge}</span>
              )}
            </Link>
          ))}
        </nav>

        <div className="sidebar-footer">
          <div className="sidebar-user">
            <div className="sidebar-avatar">{initials}</div>
            <div className="sidebar-user-info">
              <span>{profile?.name ?? 'Patient'}</span>
              <small>{profile?.uid ?? ''}</small>
            </div>
          </div>
          <button
            onClick={handleLogout}
            className="btn btn-outline btn-full btn-sm"
            style={{ color: '#fff', borderColor: 'rgba(255,255,255,.2)' }}
          >
            🚪 Log Out
          </button>
        </div>
      </aside>
    </>
  )
}
