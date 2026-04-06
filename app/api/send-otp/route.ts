import { NextResponse } from 'next/server'
import { createClient } from '@supabase/supabase-js'
import nodemailer from 'nodemailer'

export async function POST(request: Request) {
  try {
    const { email } = await request.json()

    if (!email) {
      return NextResponse.json({ error: 'Email is required' }, { status: 400 })
    }

    // Generate an 8-digit numeric OTP
    const code = Math.floor(10000000 + Math.random() * 90000000).toString()
    const expiresAt = new Date(Date.now() + 10 * 60 * 1000).toISOString() // 10 minutes

    // Use service-role client to bypass RLS
    const adminClient = createClient(
      process.env.NEXT_PUBLIC_SUPABASE_URL!,
      process.env.SUPABASE_SERVICE_ROLE_KEY!
    )

    // Delete any existing codes for this email so there's always only one active record
    await adminClient
      .from('otp_codes')
      .delete()
      .eq('email', email)

    // Insert new OTP
    const { error: insertError } = await adminClient.from('otp_codes').insert({
      email,
      code,
      expires_at: expiresAt,
      used: false,
    })

    if (insertError) {
      console.error('OTP insert error:', insertError)
      return NextResponse.json({ error: 'Failed to create OTP' }, { status: 500 })
    }

    // Send email via nodemailer
    const transporter = nodemailer.createTransport({
      host: process.env.MAIL_HOST,
      port: parseInt(process.env.MAIL_PORT || '587'),
      secure: false,
      auth: {
        user: process.env.MAIL_USERNAME,
        pass: process.env.MAIL_PASSWORD,
      },
    })

    await transporter.sendMail({
      from: `"${process.env.MAIL_FROM_NAME || 'DentaQueue'}" <${process.env.MAIL_FROM_ADDRESS}>`,
      to: email,
      subject: 'Your DentaQueue Verification Code',
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 480px; margin: 0 auto;">
          <h2 style="color: #2563eb;">DentaQueue Email Verification</h2>
          <p>Your verification code is:</p>
          <div style="font-size: 2.5rem; font-weight: bold; letter-spacing: 0.3em; 
                      text-align: center; padding: 1rem; background: #f0f4ff; 
                      border-radius: 8px; color: #1e40af;">
            ${code}
          </div>
          <p style="margin-top: 1rem; color: #6b7280; font-size: 0.875rem;">
            This code expires in <strong>10 minutes</strong>. Do not share it with anyone.
          </p>
        </div>
      `,
      text: `Your DentaQueue verification code is: ${code}\nThis code expires in 10 minutes.`,
    })

    return NextResponse.json({ success: true })
  } catch (err: any) {
    console.error('send-otp error:', err)
    return NextResponse.json({ error: err.message }, { status: 500 })
  }
}
