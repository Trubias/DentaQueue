import { NextResponse } from 'next/server'
import { createClient } from '@supabase/supabase-js'

/**
 * POST /api/verify-otp
 * Body: { email: string, code: string }
 *
 * Validates the 6-digit code against our otp_codes table ONLY.
 * Does NOT call supabase.auth.verifyOtp().
 * Returns { success: true } on match.
 * Account creation (signUp / signInWithPassword / DB inserts) is
 * handled client-side in /verify-email/page.tsx after this succeeds.
 */
export async function POST(request: Request) {
  try {
    const { email, code } = await request.json()

    if (!email || !code) {
      return NextResponse.json({ error: 'Email and code are required' }, { status: 400 })
    }

    const trimmedCode = String(code).trim()

    // Service-role client to bypass RLS on otp_codes
    const adminClient = createClient(
      process.env.NEXT_PUBLIC_SUPABASE_URL!,
      process.env.SUPABASE_SERVICE_ROLE_KEY!
    )

    // Find the latest valid record for this exact email and code
    const { data: otpRow, error: otpError } = await adminClient
      .from('otp_codes')
      .select('id, code, expires_at, used')
      .eq('email', email)
      .eq('code', trimmedCode)
      .eq('used', false)
      .gt('expires_at', new Date().toISOString())
      .order('created_at', { ascending: false })
      .limit(1)
      .single()

    if (otpError) {
      console.error('OTP lookup error:', otpError)
      // Since .single() throws if no rows found, we can catch it here as an invalid code scenario
      return NextResponse.json(
        { error: 'Code has expired or is invalid. Please request a new one.' },
        { status: 400 }
      )
    }

    if (!otpRow) {
      return NextResponse.json(
        { error: 'Code has expired or is invalid. Please request a new one.' },
        { status: 400 }
      )
    }

    // Mark OTP as used so it cannot be replayed
    await adminClient.from('otp_codes').update({ used: true }).eq('id', otpRow.id)

    return NextResponse.json({ success: true })
  } catch (err: any) {
    console.error('verify-otp error:', err)
    return NextResponse.json({ error: err.message }, { status: 500 })
  }
}
