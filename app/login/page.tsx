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
    const { data, error } = await supabase.auth.signInWithPassword({
      email: form.email,
      password: form.password,
    })
    if (error) {
      toast.error(error.message)
      setLoading(false)
      return
    }

    const { data: profileCheck } = await supabase
      .from('profiles')
      .select('id')
      .eq('id', data.user.id)
      .maybeSingle()

    const { data: patientCheck } = await supabase
      .from('patients')
      .select('id')
      .eq('id', data.user.id)
      .maybeSingle()

    if (!profileCheck && !patientCheck) {
      await supabase.auth.signOut()
      toast.error('Account no longer exists. Please contact the admin.')
      setLoading(false)
      return
    }

    let role = 'client'

    // Attempt 1: Get role from user metadata (works if set during auth creation)
    if (data.user.user_metadata?.role) {
      role = data.user.user_metadata.role
    }

    // Attempt 2: Fetch from profiles (now works because RLS allows self-read)
    const { data: profile } = await supabase
      .from('profiles')
      .select('role')
      .eq('id', data.user.id)
      .single()

    if (profile?.role) {
      role = profile.role
    }

    // Force a full router refresh to ensure layouts get the new session cookie
    router.refresh()

    // Instead of using router.push, we use window.location.href 
    // to bypass Next.js client-side caches that hold onto the old session state
    if (role === 'admin') {
      if (data.user.email !== 'admin@gmail.com') {
        await supabase.auth.signOut()
        toast.error('Access denied. Admins only.')
        setLoading(false)
        return
      }
      window.location.href = '/admin/dashboard'
    } else {
      window.location.href = '/client/dashboard'
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
