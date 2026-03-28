import { redirect } from 'next/navigation'
import { createServerClient } from '@supabase/ssr'
import { cookies } from 'next/headers'
import AdminSidebar from '@/components/admin/Sidebar'

export default async function AdminLayout({ children }) {
  const cookieStore = await cookies()
  const supabase = createServerClient(
    process.env.NEXT_PUBLIC_SUPABASE_URL,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY,
    { cookies: { getAll() { return cookieStore.getAll() }, setAll() { } } }
  )
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) redirect('/login')

  const { data: profile } = await supabase
    .from('profiles')
    .select('*')
    .eq('id', user.id)
    .single()

  // If profile fails to load or role is not admin, send to client dashboard
  // (null profile could mean RLS issue — don't redirect to /login as that causes loops)
  if (profile?.role !== 'admin') redirect('/client/dashboard')

  return (
    <div className="dashboard-layout">
      <AdminSidebar profile={{ ...profile, email: user.email }} />
      <div className="main-content">
        {children}
      </div>
    </div>
  )
}
