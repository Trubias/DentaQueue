import { NextResponse } from 'next/server'
import { createClient } from '@supabase/supabase-js'

export async function POST(request: Request) {
  try {
    const { email } = await request.json()

    if (!email) {
      return NextResponse.json({ error: 'Email is required' }, { status: 400 })
    }

    const adminClient = createClient(
      process.env.NEXT_PUBLIC_SUPABASE_URL!,
      process.env.SUPABASE_SERVICE_ROLE_KEY!
    )

    // Find the user by email using listUsers
    const { data: { users }, error: listError } = await adminClient.auth.admin.listUsers()

    if (listError) {
      return NextResponse.json({ error: listError.message }, { status: 500 })
    }

    const user = users.find(u => u.email?.toLowerCase() === email.toLowerCase())

    if (user) {
      const { error: deleteError } = await adminClient.auth.admin.deleteUser(user.id)
      if (deleteError) {
        return NextResponse.json({ error: deleteError.message }, { status: 500 })
      }
    }

    return NextResponse.json({ success: true })
  } catch (err: any) {
    return NextResponse.json({ error: err.message }, { status: 500 })
  }
}
