import { NextResponse } from 'next/server'
import { createClient } from '@supabase/supabase-js'

/**
 * POST /api/confirm-user
 * Body: { userId: string }
 *
 * Uses service-role to mark the user's email as confirmed in Supabase Auth.
 * Called after our own OTP has been validated, so the user doesn't get stuck
 * in an unconfirmed state waiting for Supabase's own confirmation email.
 */
export async function POST(request: Request) {
  try {
    const { userId } = await request.json()

    if (!userId) {
      return NextResponse.json({ error: 'userId is required' }, { status: 400 })
    }

    const adminClient = createClient(
      process.env.NEXT_PUBLIC_SUPABASE_URL!,
      process.env.SUPABASE_SERVICE_ROLE_KEY!
    )

    const { error } = await adminClient.auth.admin.updateUserById(userId, {
      email_confirm: true,
    })

    if (error) {
      console.error('confirm-user error:', error)
      return NextResponse.json({ error: error.message }, { status: 500 })
    }

    return NextResponse.json({ success: true })
  } catch (err: any) {
    console.error('confirm-user error:', err)
    return NextResponse.json({ error: err.message }, { status: 500 })
  }
}
