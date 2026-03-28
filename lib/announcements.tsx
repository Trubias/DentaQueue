import supabase from './supabaseClient'

// Get unsent announcements for admin list
export async function getUnsentAnnouncements() {
  const { data, error } = await supabase
    .from('announcements')
    .select('*, profiles(name, email, uid)')
    .is('sent_at', null)
    .order('created_at', { ascending: false })
  if (error) throw error
  return data
}

// Get sent announcements (inventory) — grouped by year
export async function getSentAnnouncements() {
  const { data, error } = await supabase
    .from('announcements')
    .select('*, profiles(name, email, uid)')
    .not('sent_at', 'is', null)
    .order('sent_at', { ascending: false })
  if (error) throw error
  // Group by year
  return data.reduce((groups, item) => {
    const year = new Date(item.sent_at).getFullYear()
    if (!groups[year]) groups[year] = []
    groups[year].push(item)
    return groups
  }, {})
}

// Get notifications for a client user
export async function getMyNotifications(userId) {
  const { data, error } = await supabase
    .from('announcements')
    .select('*')
    .eq('user_id', userId)
    .not('sent_at', 'is', null)
    .order('sent_at', { ascending: false })
  if (error) throw error
  return data
}

// Get unread count for a user
export async function getUnreadCount(userId) {
  const { count, error } = await supabase
    .from('announcements')
    .select('id', { count: 'exact' })
    .eq('user_id', userId)
    .eq('read', false)
  if (error) throw error
  return count ?? 0
}

// Mark announcement as read
export async function markAnnouncementRead(id) {
  const { error } = await supabase
    .from('announcements')
    .update({ read: true })
    .eq('id', id)
  if (error) throw error
}

// Mark all announcements for a user as read
export async function clearAllNotifications(userId) {
  const { error } = await supabase
    .from('announcements')
    .update({ read: true })
    .eq('user_id', userId)
  if (error) throw error
}

// Create announcement
export async function createAnnouncement(payload) {
  const { data, error } = await supabase
    .from('announcements')
    .insert(payload)
    .select()
    .single()
  if (error) throw error
  return data
}

// Mark announcement as sent
export async function markAnnouncementSent(id) {
  const { data, error } = await supabase
    .from('announcements')
    .update({ sent_at: new Date().toISOString() })
    .eq('id', id)
    .select()
    .single()
  if (error) throw error
  return data
}

// Delete announcement
export async function deleteAnnouncement(id) {
  const { error } = await supabase
    .from('announcements')
    .delete()
    .eq('id', id)
  if (error) throw error
}
