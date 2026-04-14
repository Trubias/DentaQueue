import { NextResponse } from 'next/server'
import { createClient } from '@supabase/supabase-js'

export async function POST(request: Request) {
  try {
    const { email, password, fullName, age, sex } = await request.json()

    if (!email || !password) {
      return NextResponse.json({ error: 'Email and password are required' }, { status: 400 })
    }

    // Service-role client to bypass RLS and Auth rate limits
    const adminClient = createClient(
      process.env.NEXT_PUBLIC_SUPABASE_URL!,
      process.env.SUPABASE_SERVICE_ROLE_KEY!
    )

    const { data: listData, error: listError } = await adminClient.auth.admin.listUsers()
    
    // Check if user already exists in auth
    const existingUser = listData?.users.find((u: any) => u.email === email)

    if (existingUser) {
      // Force confirm the existing user to get them unstuck!
      const { error: updateError } = await adminClient.auth.admin.updateUserById(
        existingUser.id,
        { email_confirm: true, user_metadata: { name: fullName, age, sex, role: 'client' } }
      )
      
      // Update their password if needed
      await adminClient.auth.admin.updateUserById(existingUser.id, { password })

      return NextResponse.json({ success: true, userId: existingUser.id })
    }

    const { data, error } = await adminClient.auth.admin.createUser({
      email,
      password,
      email_confirm: true,
      user_metadata: {
        name: fullName,
        age: age,
        sex: sex,
        role: 'client',
      },
    })

    if (error) {
      console.error('createUser error:', error)
      return NextResponse.json({ error: error.message }, { status: 400 })
    }

    return NextResponse.json({ success: true, userId: data.user.id })
  } catch (err: any) {
    console.error('create-account error:', err)
    return NextResponse.json({ error: err.message }, { status: 500 })
  }
}
