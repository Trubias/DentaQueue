'use client'
import { useState } from 'react'
import Link from 'next/link'
import { usePathname, useRouter } from 'next/navigation'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

const navItems = [
  { href: '/admin/dashboard', icon: '📊', label: 'Dashboard' },
  { href: '/admin/queue', icon: '🪑', label: 'Queue' },
  { href: '/admin/schedule', icon: '📅', label: 'Schedule' },
  { href: '/admin/users', icon: '👥', label: 'Users' },
  { href: '/admin/announcements', icon: '📢', label: 'Announcements' },
  { href: '/admin/reports', icon: '📈', label: 'Reports' },
  { href: '/admin/settings', icon: '⚙️', label: 'Settings' },
]

export default function AdminSidebar({ profile }: any) {
  const pathname = usePathname()
  const router = useRouter()
  const [isOpen, setIsOpen] = useState(false)

  const handleLogout = async () => {
    await supabase.auth.signOut()
    toast.success('Logged out')
    router.push('/login')
  }

  const initials = profile?.name
    ? profile.name.split(' ').map((n: string) => n[0]).join('').substring(0, 2).toUpperCase()
    : 'AD'

  return (
    <>
      <button className="hamburger-btn" onClick={() => setIsOpen(true)}>☰</button>
      <div className={`sidebar-overlay ${isOpen ? 'open' : ''}`} onClick={() => setIsOpen(false)} />
      
      <aside className={`sidebar ${isOpen ? 'sidebar--open' : ''}`}>
        <div className="sidebar-brand">
          <div className="brand-icon" style={{ fontSize: '1.3rem' }}>🦷</div>
          <div>
            <span>DentaQueue</span>
            <small>Admin Portal</small>
          </div>
        </div>

        <nav className="sidebar-nav">
          <div className="nav-section-label">Main Menu</div>
          {navItems.map(item => (
            <Link
              key={item.href}
              href={item.href}
              className={`nav-item ${pathname === item.href ? 'active' : ''}`}
              onClick={() => setIsOpen(false)}
            >
              <span className="nav-icon">{item.icon}</span>
              {item.label}
            </Link>
          ))}
        </nav>

        <div className="sidebar-footer">
          <div className="sidebar-user">
            <div className="sidebar-avatar">{initials}</div>
            <div className="sidebar-user-info">
              <span>{profile?.name ?? 'Admin'}</span>
              <small>Administrator · {profile?.uid ?? ''}</small>
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
