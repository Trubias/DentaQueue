import { createClient } from '@supabase/supabase-js'

export async function GET(request: Request, { params }: { params: Promise<{ userId: string }> }) {
  try {
    const { userId } = await params
    const supabaseAdmin = createClient(
      process.env.NEXT_PUBLIC_SUPABASE_URL!,
      process.env.SUPABASE_SERVICE_ROLE_KEY!
    )
    const { data, error } = await supabaseAdmin.auth.admin.getUserById(userId)
    if (error) throw error
    return Response.json({ email: data.user?.email ?? null })
  } catch (err: any) {
    return Response.json({ error: err.message }, { status: 500 })
  }
}
