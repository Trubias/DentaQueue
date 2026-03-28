import supabase from './supabaseClient'

// Get all appointments (admin)
export async function getAllAppointments(filters = {}) {
  let query = supabase
    .from('appointments')
    .select('*, profiles(name, email, uid)')
    .order('created_at', { ascending: true })

  if (filters.status) query = query.eq('status', filters.status)
  if (filters.year) query = query.filter('scheduled_at', 'gte', `${filters.year}-01-01`).filter('scheduled_at', 'lte', `${filters.year}-12-31`)

  const { data, error } = await query
  if (error) throw error
  return data
}

// Get appointments for a specific user (client)
export async function getMyAppointments(userId) {
  const { data, error } = await supabase
    .from('appointments')
    .select('*')
    .eq('user_id', userId)
    .order('created_at', { ascending: false })
  if (error) throw error
  return data
}

// Create a new appointment
export async function createAppointment(payload) {
  const { data, error } = await supabase
    .from('appointments')
    .insert(payload)
    .select()
    .single()
  if (error) throw error
  return data
}

// Update an appointment
export async function updateAppointment(id, updates) {
  const { data, error } = await supabase
    .from('appointments')
    .update({ ...updates, updated_at: new Date().toISOString() })
    .eq('id', id)
    .select()
    .single()
  if (error) throw error
  return data
}

// Delete an appointment
export async function deleteAppointment(id) {
  const { error } = await supabase
    .from('appointments')
    .delete()
    .eq('id', id)
  if (error) throw error
}

// Get upcoming scheduled appointments (for admin dashboard)
export async function getUpcomingAssigned(limit = 5) {
  const { data, error } = await supabase
    .from('appointments')
    .select('*')
    .not('scheduled_at', 'is', null)
    .gte('scheduled_at', new Date().toISOString())
    .eq('status', 'assigned')
    .order('scheduled_at', { ascending: true })
    .limit(limit)
  if (error) throw error
  return data
}

// Get dashboard stats
export async function getDashboardStats() {
  const today = new Date().toISOString().split('T')[0]
  const [todayRes, pendingRes] = await Promise.all([
    supabase.from('appointments').select('id', { count: 'exact' }).gte('created_at', `${today}T00:00:00`).lte('created_at', `${today}T23:59:59`),
    supabase.from('appointments').select('id', { count: 'exact' }).eq('status', 'pending'),
  ])
  return {
    today_appointments: todayRes.count ?? 0,
    queue_length: pendingRes.count ?? 0,
  }
}

// Get calendar events (all with scheduled_at)
export async function getCalendarEvents() {
  const { data, error } = await supabase
    .from('appointments')
    .select('id, fullname, scheduled_at, status, type')
    .not('scheduled_at', 'is', null)
  if (error) throw error
  return data.map(a => ({
    id: a.id,
    title: `#${String(a.id).padStart(3, '0')} — ${a.fullname}`,
    start: new Date(a.scheduled_at),
    allDay: false,
    resource: a,
  }))
}

// Check if user has active appointment (pending/assigned)
export async function getActiveAppointment(userId) {
  const { data, error } = await supabase
    .from('appointments')
    .select('*')
    .eq('user_id', userId)
    .in('status', ['pending', 'assigned'])
    .single()
  if (error && error.code !== 'PGRST116') throw error
  return data ?? null
}

// Get queue position for a pending appointment
export async function getQueuePosition(appointmentId, createdAt) {
  const { count, error } = await supabase
    .from('appointments')
    .select('id', { count: 'exact' })
    .eq('status', 'pending')
    .lte('created_at', createdAt)
  if (error) throw error
  return count ?? 0
}
