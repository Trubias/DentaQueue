'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

export default function AdminSettingsPage() {
  const [profile, setProfile] = useState({ name: '', age: '', sex: '' })
  const [passwords, setPasswords] = useState({ current: '', new: '', confirm: '' })
  const [loading, setLoading] = useState(false)
  const [userId, setUserId] = useState(null)

  useEffect(() => {
    supabase.auth.getUser().then(({ data: { user } }) => {
      setUserId(user?.id)
      supabase.from('profiles').select('*').eq('id', user.id).single().then(({ data }) => {
        if (data) setProfile({ name: data.name ?? '', age: data.age ?? '', sex: data.sex ?? '' })
      })
    })
  }, [])

  const handleProfile = async (e) => {
    e.preventDefault()
    setLoading(true)
    const { error } = await supabase.from('profiles').update({
      name: profile.name, age: profile.age || null, sex: profile.sex || null
    }).eq('id', userId)
    setLoading(false)
    if (error) toast.error(error.message)
    else toast.success('Profile updated.')
  }

  const handlePassword = async (e) => {
    e.preventDefault()
    if (passwords.new !== passwords.confirm) { toast.error('Passwords do not match.'); return }
    setLoading(true)
    const { error } = await supabase.auth.updateUser({ password: passwords.new })
    setLoading(false)
    if (error) toast.error(error.message)
    else { toast.success('Password updated.'); setPasswords({ current: '', new: '', confirm: '' }) }
  }

  return (
    <>
      <div className="topbar"><h2>⚙️ Settings</h2></div>
      <div className="page-body">
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.25rem', maxWidth: '860px' }}>
          <div className="card">
            <div className="card-header"><h3>👤 Profile</h3></div>
            <div className="card-body">
              <form onSubmit={handleProfile}>
                <div className="form-group">
                  <label className="form-label">Full Name</label>
                  <input className="form-control" value={profile.name}
                    onChange={e => setProfile(p => ({ ...p, name: e.target.value }))} />
                </div>
                <div className="form-group">
                  <label className="form-label">Age</label>
                  <input type="number" className="form-control" value={profile.age}
                    onChange={e => setProfile(p => ({ ...p, age: e.target.value }))} />
                </div>
                <div className="form-group">
                  <label className="form-label">Sex</label>
                  <select className="form-control" value={profile.sex} onChange={e => setProfile(p => ({ ...p, sex: e.target.value }))}>
                    <option value="">Select</option>
                    <option>Male</option><option>Female</option><option>Other</option>
                  </select>
                </div>
                <button type="submit" className="btn btn-primary" disabled={loading}>Save Profile</button>
              </form>
            </div>
          </div>

          <div className="card">
            <div className="card-header"><h3>🔒 Change Password</h3></div>
            <div className="card-body">
              <form onSubmit={handlePassword}>
                <div className="form-group">
                  <label className="form-label">New Password</label>
                  <input type="password" className="form-control" minLength={8} required
                    value={passwords.new} onChange={e => setPasswords(p => ({ ...p, new: e.target.value }))} />
                </div>
                <div className="form-group">
                  <label className="form-label">Confirm New Password</label>
                  <input type="password" className="form-control" required
                    value={passwords.confirm} onChange={e => setPasswords(p => ({ ...p, confirm: e.target.value }))} />
                </div>
                <button type="submit" className="btn btn-primary" disabled={loading}>Update Password</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </>
  )
}
