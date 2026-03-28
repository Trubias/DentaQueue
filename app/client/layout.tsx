import { redirect } from 'next/navigation'
import { createServerClient } from '@supabase/ssr'
import { cookies } from 'next/headers'
import ClientSidebar from '@/components/client/Sidebar'

export default async function ClientLayout({ children }) {
  const cookieStore = cookies()
  const supabase = createServerClient(
    process.env.NEXT_PUBLIC_SUPABASE_URL,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY,
    { cookies: { getAll() { return cookieStore.getAll() }, setAll() {} } }
  )
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) redirect('/login')

  const { data: profile } = await supabase
    .from('profiles').select('*').eq('id', user.id).single()

  if (profile?.role === 'admin') {
    redirect('/admin/dashboard')
  }

  const { count: unreadCount } = await supabase
    .from('announcements')
    .select('id', { count: 'exact' })
    .eq('user_id', user.id)
    .eq('read', false)

  return (
    <div className="dashboard-layout">
      <ClientSidebar profile={{ ...profile, email: user.email }} unreadCount={unreadCount ?? 0} />
      <div className="main-content">
        {children}
      </div>
    </div>
  )
}
