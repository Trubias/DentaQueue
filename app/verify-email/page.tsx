'use client'

import { useState, useEffect, useRef } from 'react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

const COUNTDOWN_SECONDS = 60

export default function VerifyEmailPage() {
  const router = useRouter()
  const [email, setEmail] = useState('')
  const [otpCode, setOtpCode] = useState('')
  const [loading, setLoading] = useState(false)
  const [resending, setResending] = useState(false)
  const [countdown, setCountdown] = useState(COUNTDOWN_SECONDS)
  const timerRef = useRef<NodeJS.Timeout | null>(null)

  // ── Load email from sessionStorage; redirect if missing ─────────────────
  useEffect(() => {
    const storedEmail = sessionStorage.getItem('pendingVerifyEmail')
    if (storedEmail) {
      setEmail(storedEmail)
    } else {
      router.push('/register')
    }
  }, [router])

  // ── Countdown timer (starts on mount) ────────────────────────────────────
  useEffect(() => {
    startCountdown()
    return () => clearInterval(timerRef.current!)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  const startCountdown = () => {
    clearInterval(timerRef.current!)
    setCountdown(COUNTDOWN_SECONDS)
    timerRef.current = setInterval(() => {
      setCountdown((prev) => {
        if (prev <= 1) {
          clearInterval(timerRef.current!)
          return 0
        }
        return prev - 1
      })
    }, 1000)
  }

  // ── Verify handler ────────────────────────────────────────────────────────
  const handleVerify = async (e: React.FormEvent) => {
    e.preventDefault()
    const trimmed = otpCode.trim()
    if (!trimmed) {
      toast.error('Please enter the verification code.')
      return
    }

    setLoading(true)

    // STEP 1 — Validate our custom OTP (otp_codes table only, no supabase auth OTP)
    const verifyRes = await fetch('/api/verify-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, code: trimmed }),
    })
    const verifyResult = await verifyRes.json()

    if (!verifyRes.ok || !verifyResult.success) {
      toast.error(verifyResult.error || 'Invalid or expired code. Please try again.')
      setLoading(false)
      return
    }

    // STEP 2 — Pull pending registration data
    const emailToUse = sessionStorage.getItem('pendingVerifyEmail') || email
    const rawData = sessionStorage.getItem('pendingUserData') || '{}'
    const { fullName, age, sex, password } = JSON.parse(rawData)

    if (!password) {
      toast.error('Session expired. Please register again.')
      setLoading(false)
      router.push('/register')
      return
    }

    // STEP 3 — Create account securely on the server to bypass email rate limits
    let userId: string | null = null

    const createReq = await fetch('/api/create-account', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: emailToUse, password, fullName, age, sex })
    })

    const createRes = await createReq.json()

    if (!createReq.ok || !createRes.success) {
      toast.error(createRes.error || 'Failed to sync account. Please try again.')
      setLoading(false)
      return
    }

    // Now sign them in on the client
    const { data: signInData, error: signInError } = await supabase.auth.signInWithPassword({
      email: emailToUse,
      password: password
    })

    if (signInError) {
      toast.error(signInError.message || 'Verification successful, but sign in failed. Please login manually.')
      sessionStorage.clear()
      router.push('/login')
      return
    }

    userId = signInData.user?.id ?? null

    if (!userId) {
      toast.error('Could not retrieve secure session. Please login manually.')
      router.push('/login')
      return
    }

    // STEP 4 — Upsert profiles table
    const uid = 'DQ-' + Math.random().toString(36).substring(2, 8).toUpperCase()

    const { error: profileError } = await supabase.from('profiles').upsert(
      {
        id: userId,
        name: fullName,
        email: emailToUse,
        uid: uid,
        age: parseInt(age),
        sex: sex,
      },
      { onConflict: 'id' }
    )
    if (profileError) console.error('Profile upsert error:', profileError)

    // STEP 5 — Upsert patients table
    const { error: patientError } = await supabase.from('patients').upsert(
      {
        id: userId,
        full_name: fullName,
        age: parseInt(age),
        sex: sex,
        role: 'client',
      },
      { onConflict: 'id' }
    )
    if (patientError) console.error('Patient upsert error:', patientError)

    // STEP 6 — Clear sessionStorage
    sessionStorage.removeItem('pendingVerifyEmail')
    sessionStorage.removeItem('pendingUserData')

    // STEP 7 — Redirect
    toast.success('Email verified! Welcome to DentaQueue 🦷')
    router.refresh()
    router.push('/client/dashboard')
    setLoading(false)
  }

  // ── Resend handler — calls our /api/send-otp (nodemailer, NOT supabase.auth.resend) ──
  const handleResend = async () => {
    if (countdown > 0 || resending) return
    setResending(true)

    const res = await fetch('/api/send-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email }),
    })

    if (res.ok) {
      toast.success('A new code has been sent to your Gmail')
      startCountdown()
    } else {
      const result = await res.json()
      toast.error('Failed to resend: ' + (result.error || 'Unknown error'))
    }
    setResending(false)
  }

  const resendDisabled = countdown > 0 || resending

  return (
    <div className="auth-page">
      <div className="auth-card">
        <div className="auth-logo">
          <div
            className="logo-icon"
            style={{ fontSize: '1.5rem', display: 'flex', alignItems: 'center', justifyContent: 'center' }}
          >
            🦷
          </div>
          <h1>Verify Email</h1>
          <p>We sent a verification code to {email || 'your email'}</p>
        </div>

        <form className="auth-form" onSubmit={handleVerify}>
          <div className="form-group">
            <label>Verification Code</label>
            <input
              type="text"
              inputMode="numeric"
              required
              value={otpCode}
              onChange={(e) => setOtpCode(e.target.value.replace(/\D/g, ''))}
              placeholder="Enter 8-digit code"
              style={{ textAlign: 'center', letterSpacing: '0.2em' }}
            />
            <p
              style={{
                marginTop: '0.5rem',
                fontSize: '0.75rem',
                color: 'var(--text-muted)',
              }}
            >
              Code expires in 10 minutes. Request a new one if expired.
            </p>
          </div>

          <button className="btn btn-primary btn-full" type="submit" disabled={loading}>
            {loading ? 'Verifying…' : 'Verify Account'}
          </button>
        </form>

        <div style={{ marginTop: '1.5rem', textAlign: 'center', fontSize: '.875rem' }}>
          <button
            type="button"
            onClick={handleResend}
            disabled={resendDisabled}
            style={{
              background: 'none',
              border: 'none',
              color: resendDisabled ? 'var(--text-muted)' : 'var(--primary)',
              cursor: resendDisabled ? 'not-allowed' : 'pointer',
              textDecoration: resendDisabled ? 'none' : 'underline',
              opacity: resendDisabled ? 0.5 : 1,
              transition: 'opacity 0.2s, color 0.2s',
              fontWeight: resendDisabled ? 'normal' : '600',
            }}
          >
            {resending
              ? 'Sending…'
              : countdown > 0
              ? `Resend Code (${countdown}s)`
              : 'Resend Code'}
          </button>
        </div>

        <div
          style={{
            marginTop: '1rem',
            textAlign: 'center',
            fontSize: '.875rem',
            color: 'var(--text-muted)',
          }}
        >
          <Link href="/register" style={{ color: 'var(--primary)' }}>
            Back to Register
          </Link>
        </div>
      </div>
    </div>
  )
}
