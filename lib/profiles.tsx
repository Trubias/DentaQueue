import supabase from './supabaseClient'

// Get all users (admin)
export async function getAllUsers(search = '') {
  let query = supabase
    .from('profiles')
    .select('id, name, email:id, uid, role, age, sex, avatar, created_at')
    .order('created_at', { ascending: true })

  // Note: email is in auth.users — for display we join via profiles
  if (search) {
    query = query.or(`name.ilike.%${search}%,uid.ilike.%${search}%`)
  }

  const { data, error } = await query
  if (error) throw error
  return data
}

// Get profile for a given user id
export async function getProfile(userId) {
  const { data, error } = await supabase
    .from('profiles')
    .select('*')
    .eq('id', userId)
    .single()
  if (error) throw error
  return data
}

// Update profile
export async function updateProfile(userId, updates) {
  const { data, error } = await supabase
    .from('profiles')
    .update(updates)
    .eq('id', userId)
    .select()
    .single()
  if (error) throw error
  return data
}

// Delete a user (admin only — calls server API route for auth deletion)
export async function deleteUserById(userId) {
  const { error } = await supabase
    .from('profiles')
    .delete()
    .eq('id', userId)
  if (error) throw error
}
