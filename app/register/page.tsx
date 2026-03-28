'use client'
export const dynamic = 'force-dynamic'
import { useState } from 'react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
import supabase from '@/lib/supabaseClient'   // assuming this is client-side createClient()
import toast from 'react-hot-toast'

export default function RegisterPage() {
  const router = useRouter()
  const [form, setForm] = useState({
    name: '',
    email: '',
    password: '',
    confirmPassword: '',
    age: '',
    sex: ''
  })
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (form.password !== form.confirmPassword) {
      toast.error('Passwords do not match.')
      return
    }

    if (!form.age || isNaN(Number(form.age)) || Number(form.age) < 1) {
      toast.error('Please enter a valid age.')
      return
    }

    setLoading(true)

    const { data, error } = await supabase.auth.signUp({
      email: form.email.trim(),
      password: form.password,
      options: {
        data: {
          name: form.name.trim(),
          age: form.age,           // will be cast to integer in trigger
          sex: form.sex,
          role: 'client'
        },
        emailRedirectTo: `${window.location.origin}/login`  // optional – better verification redirect
      }
    })

    if (error) {
      toast.error(error.message || 'Failed to create account. Try again.')
      console.error(error)
    } else if (data.user?.identities?.length === 0) {
      // This usually means email already exists
      toast.error('This email is already registered.')
    } else {
      if (data.user?.id) {
        const generatedUid = `DQ-${data.user.id.substring(0, 6).toUpperCase()}`

        const { error: profileError } = await supabase
          .from('profiles')
          .insert({
            id: data.user.id,
            name: form.name.trim(),
            email: form.email.trim(),
            uid: generatedUid
          })
        if (profileError) console.error('Error inserting profile:', profileError)

        const { error: patientError } = await supabase
          .from('patients')
          .insert({
            id: data.user.id,
            full_name: form.name.trim(),
            age: Number(form.age),
            sex: form.sex,
            role: 'client'
          })
        if (patientError) console.error('Error inserting patient:', patientError)
      }

      toast.success('Account created! Check your email to verify, then log in.')
      router.push('/login')
    }

    setLoading(false)
  }

  const update = (field: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) =>
    setForm(f => ({ ...f, [field]: e.target.value }))

  return (
    <div className="auth-page">
      <div className="auth-card">
        <div className="auth-logo">
          <div className="logo-icon" style={{ fontSize: '1.5rem', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            🦷
          </div>
          <h1>DentaQueue</h1>
          <p>Create your patient account</p>
        </div>

        <form className="auth-form" onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Full Name</label>
            <input type="text" required value={form.name} onChange={update('name')} placeholder="Juan Dela Cruz" />
          </div>
          <div className="form-group">
            <label>Email Address</label>
            <input type="email" required value={form.email} onChange={update('email')} placeholder="your@email.com" />
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
            <div className="form-group">
              <label>Age</label>
              <input type="number" min="1" required value={form.age} onChange={update('age')} placeholder="25" />
            </div>
            <div className="form-group">
              <label>Sex</label>
              <select required value={form.sex} onChange={update('sex')}>
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <div className="form-group">
            <label>Password</label>
            <input type="password" required minLength={8} value={form.password} onChange={update('password')} placeholder="At least 8 characters" />
          </div>
          <div className="form-group">
            <label>Confirm Password</label>
            <input type="password" required value={form.confirmPassword} onChange={update('confirmPassword')} placeholder="Repeat password" />
          </div>
          <button className="btn btn-primary btn-full" type="submit" disabled={loading}>
            {loading ? 'Creating account…' : 'Create Account'}
          </button>
        </form>

        <div style={{ marginTop: '1.5rem', textAlign: 'center', fontSize: '.875rem', color: 'var(--text-muted)' }}>
          Already have an account? <Link href="/login" style={{ color: 'var(--primary)' }}>Sign in</Link>
        </div>
      </div>
    </div>
  )
}