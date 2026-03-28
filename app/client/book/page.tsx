'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

const TYPES = ['Cleaning','Extraction','Filling','Braces','Root Canal','Crown','Consultation','General Check-up','Whitening','Other']

export default function BookPage() {
  const router = useRouter()
  const [form, setForm] = useState({ fullname: '', age: '', sex: '', type: '', note: '' })
  const [active, setActive] = useState(null)
  const [userId, setUserId] = useState(null)
  const [loading, setLoading] = useState(false)
  const [checking, setChecking] = useState(true)

  useEffect(() => {
    supabase.auth.getUser().then(async ({ data: { user } }) => {
      if (!user) return
      setUserId(user.id)
      const { data: prof } = await supabase.from('profiles').select('name, age, sex').eq('id', user.id).single()
      if (prof) setForm(f => ({ ...f, fullname: prof.name ?? '', age: prof.age ?? '', sex: prof.sex ?? '' }))

      const { data } = await supabase.from('appointments')
        .select('*').eq('user_id', user.id).in('status', ['pending','assigned']).limit(1)
      setActive(data?.[0] ?? null)
      setChecking(false)
    })
  }, [])

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (active) { toast.error('You already have an active booking. Edit or cancel it first.'); return }
    setLoading(true)
    const { data, error } = await supabase.from('appointments').insert({
      user_id: userId,
      fullname: form.fullname,
      age: form.age || null,
      sex: form.sex || null,
      type: form.type,
      note: form.note || null,
      status: 'pending',
    }).select().single()

    setLoading(false)
    if (error) { toast.error(error.message); return }
    toast.success(`Appointment queued! Your ID: #${String(data.id).padStart(3, '0')}`)
    router.push('/client/dashboard')
  }

  if (checking) return (
    <>
      <div className="topbar"><h2>📝 Book Appointment</h2></div>
      <div className="page-body" style={{ color: 'var(--text-muted)', textAlign: 'center', marginTop: '3rem' }}>Checking your current bookings…</div>
    </>
  )

  return (
    <>
      <div className="topbar"><h2>📝 Book Appointment</h2></div>
      <div className="page-body">
        {active && (
          <div className="alert alert-warning" style={{ marginBottom: '1.25rem' }}>
            ⚠️ You already have an active appointment (<strong>#{String(active.id).padStart(3,'0')}</strong> — {active.type}, status: <strong>{active.status}</strong>).
            <br/>Edit or cancel it in <a href="/client/appointments" style={{ color: 'var(--primary)' }}>My Appointments</a> before booking a new one.
          </div>
        )}

        <div className="card" style={{ maxWidth: '580px' }}>
          <div className="card-header"><h3>New Appointment Request</h3></div>
          <div className="card-body">
            <form onSubmit={handleSubmit}>
              <div className="form-group">
                <label className="form-label">Full Name *</label>
                <input className="form-control" required value={form.fullname}
                  onChange={e => setForm(f => ({ ...f, fullname: e.target.value }))} />
              </div>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
                <div className="form-group">
                  <label className="form-label">Age</label>
                  <input type="number" className="form-control" min="1" value={form.age}
                    onChange={e => setForm(f => ({ ...f, age: e.target.value }))} />
                </div>
                <div className="form-group">
                  <label className="form-label">Sex</label>
                  <select className="form-control" value={form.sex} onChange={e => setForm(f => ({ ...f, sex: e.target.value }))}>
                    <option value="">Select</option>
                    <option>Male</option><option>Female</option><option>Other</option>
                  </select>
                </div>
              </div>
              <div className="form-group">
                <label className="form-label">Service Type *</label>
                <select className="form-control" required value={form.type} onChange={e => setForm(f => ({ ...f, type: e.target.value }))}>
                  <option value="">Select service…</option>
                  {TYPES.map(t => <option key={t}>{t}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label className="form-label">Notes (optional)</label>
                <textarea className="form-control" rows={3} placeholder="Any concerns or additional information…"
                  value={form.note} onChange={e => setForm(f => ({ ...f, note: e.target.value }))} />
              </div>
              <button type="submit" className="btn btn-primary btn-full" disabled={loading || !!active}>
                {loading ? 'Submitting…' : '📬 Submit Request'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </>
  )
}
