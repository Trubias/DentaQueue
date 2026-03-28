import { createClient } from '@supabase/supabase-js'

export async function DELETE(request: Request, { params }: { params: Promise<{ userId: string }> }) {
  try {
    const supabaseAdmin = createClient(
      process.env.NEXT_PUBLIC_SUPABASE_URL || 'https://dummy.supabase.co',
      process.env.SUPABASE_SERVICE_ROLE_KEY || 'dummy'
    )
    
    const { userId } = await params

    // Only service role can delete auth users
    const { error } = await supabaseAdmin.auth.admin.deleteUser(userId)
    if (error) throw error

    return Response.json({ success: true })
  } catch (err) {
    console.error('Delete user error:', err)
    return Response.json({ error: err.message }, { status: 500 })
  }
}
