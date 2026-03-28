'use client'
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

export default function ClientSidebar({ profile, unreadCount = 0 }) {
  const pathname = usePathname()
  const router = useRouter()

  const handleLogout = async () => {
    await supabase.auth.signOut()
    toast.success('Logged out')
    router.push('/login')
  }

  const initials = profile?.name
    ? profile.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
    : 'PT'

  return (
    <aside className="sidebar">
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
          >
            <span className="nav-icon">{item.icon}</span>
            {item.label}
            {item.href === '/client/notifications' && unreadCount > 0 && (
              <span style={{
                marginLeft: 'auto', background: '#ef4444', color: '#fff',
                borderRadius: '20px', fontSize: '.7rem', padding: '1px 7px', fontWeight: 700
              }}>{unreadCount}</span>
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
  )
}
