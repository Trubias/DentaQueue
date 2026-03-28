'use client'
export const dynamic = 'force-dynamic'
import { useState } from 'react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

export default function LoginPage() {
  const router = useRouter()
  const [form, setForm] = useState({ email: '', password: '' })
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)

    const email = form.email.toLowerCase().trim()

    const { data, error } = await supabase.auth.signInWithPassword({
      email,
      password: form.password,
    })
    if (error) {
      toast.error(error.message)
      setLoading(false)
      return
    }

    const { data: profile, error: profileError } = await supabase
      .from('profiles')
      .select('role')
      .eq('id', data.user.id)
      .single()

    if (profileError || !profile) {
      await supabase.auth.signOut()
      toast.error('Account no longer exists. Please contact the admin.')
      setLoading(false)
      return
    }

    const role = profile.role || 'client'

    // Force a full router refresh to ensure layouts get the new session cookie
    router.refresh()

    if (role === 'admin') {
      if (email !== 'admin@gmail.com') {
        await supabase.auth.signOut()
        toast.error('Access denied. Admins only.')
        setLoading(false)
        return
      }
      router.push('/admin/dashboard')
    } else if (role === 'client') {
      router.push('/client/dashboard')
    }
  }

  return (
    <div className="auth-page">
      <div className="auth-card">
        <div className="auth-logo">
          {/* Tooth icon — matching Admin & Patient Portal sidebars */}
          <div className="logo-icon" style={{ fontSize: '1.5rem', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            🦷
          </div>
          <h1>DentaQueue</h1>
          <p>Dental Clinic Queue Management System</p>
        </div>

        <form className="auth-form" onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Email Address</label>
            <input
              type="email" required
              value={form.email}
              onChange={e => setForm(f => ({ ...f, email: e.target.value }))}
              placeholder="your@email.com"
            />
          </div>
          <div className="form-group">
            <label>Password</label>
            <input
              type="password" required
              value={form.password}
              onChange={e => setForm(f => ({ ...f, password: e.target.value }))}
              placeholder="••••••••"
            />
          </div>
          <button className="btn btn-primary btn-full" type="submit" disabled={loading}>
            {loading ? 'Signing in…' : 'Sign In'}
          </button>
        </form>

        <div style={{ marginTop: '1.5rem', textAlign: 'center', fontSize: '.875rem', color: 'var(--text-muted)' }}>
          <Link href="/forgot-password" style={{ color: 'var(--primary)' }}>Forgot password?</Link>
          <span style={{ margin: '0 .5rem' }}>·</span>
          <Link href="/register" style={{ color: 'var(--primary)' }}>Create account</Link>
        </div>
      </div>
    </div>
  )
}
