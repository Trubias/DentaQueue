'use client'
export const dynamic = 'force-dynamic'
import { useEffect, useState } from 'react'
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, PieChart, Pie, Cell, Legend } from 'recharts'
import supabase from '@/lib/supabaseClient'
import toast from 'react-hot-toast'

const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
const COLORS = ['#1a7fd4', '#00c896', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']

export default function AdminReportsPage() {
  const year = new Date().getFullYear()
  const [selYear, setSelYear] = useState(year)
  const [monthly, setMonthly] = useState([])
  const [byType, setByType] = useState([])
  const [loading, setLoading] = useState(true)

  const load = async () => {
    setLoading(true)
    try {
      const { data: appts } = await supabase
        .from('appointments')
        .select('id, type, status, scheduled_at')
        .gte('scheduled_at', `${selYear}-01-01`)
        .lte('scheduled_at', `${selYear}-12-31`)

      // Monthly counts
      const monthlyCounts = Array.from({ length: 12 }, (_, i) => ({ month: MONTHS[i], total: 0 }))
      const typeCounts = {}
      for (const a of (appts ?? [])) {
        if (a.scheduled_at) {
          const m = new Date(a.scheduled_at).getMonth()
          monthlyCounts[m].total++
        }
        typeCounts[a.type] = (typeCounts[a.type] ?? 0) + 1
      }
      setMonthly(monthlyCounts)
      setByType(Object.entries(typeCounts).map(([name, value]) => ({ name, value })))
    } catch (e) { toast.error('Failed to load reports') }
    finally { setLoading(false) }
  }

  useEffect(() => { load() }, [selYear])

  const handleExportCsv = async () => {
    const { data } = await supabase
      .from('appointments')
      .select('*')
      .gte('scheduled_at', `${selYear}-01-01`)
      .lte('scheduled_at', `${selYear}-12-31`)
      .order('scheduled_at')

    if (!data?.length) { toast('No data to export.'); return }
    const cols = Object.keys(data[0])
    const rows = [cols.join(','), ...data.map(r => cols.map(c => `"${String(r[c] ?? '').replace(/"/g, '""')}"`).join(','))]
    const blob = new Blob([rows.join('\n')], { type: 'text/csv' })
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob)
    a.download = `appointments_${selYear}.csv`; a.click()
  }

  const years = Array.from({ length: 5 }, (_, i) => year - i)

  return (
    <>
      <div className="topbar">
        <h2>📈 Reports</h2>
        <div className="actions-row">
          <select className="form-control" style={{ width: '100px' }} value={selYear} onChange={e => setSelYear(+e.target.value)}>
            {years.map(y => <option key={y}>{y}</option>)}
          </select>
          <button className="btn btn-success btn-sm" onClick={handleExportCsv}>⬇ Export CSV</button>
        </div>
      </div>
      <div className="page-body">
        {loading ? <div style={{ textAlign: 'center', color: 'var(--text-muted)', marginTop: '3rem' }}>Loading reports…</div>
          : (
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.25rem' }}>
              <div className="card">
                <div className="card-header"><h3>📊 Monthly Appointments ({selYear})</h3></div>
                <div className="card-body">
                  <ResponsiveContainer width="100%" height={280}>
                    <BarChart data={monthly}>
                      <XAxis dataKey="month" fontSize={12} />
                      <YAxis allowDecimals={false} fontSize={12} />
                      <Tooltip />
                      <Bar dataKey="total" fill="var(--primary)" radius={[4,4,0,0]} />
                    </BarChart>
                  </ResponsiveContainer>
                </div>
              </div>

              <div className="card">
                <div className="card-header"><h3>🦷 Appointments by Type</h3></div>
                <div className="card-body">
                  {byType.length === 0
                    ? <div style={{ color: 'var(--text-muted)', textAlign: 'center', padding: '3rem' }}>No data for {selYear}</div>
                    : <ResponsiveContainer width="100%" height={280}>
                        <PieChart>
                          <Pie data={byType} cx="50%" cy="50%" outerRadius={100} dataKey="value" label={({ name, percent }) => `${name} (${(percent*100).toFixed(0)}%)`}>
                            {byType.map((_, i) => <Cell key={i} fill={COLORS[i % COLORS.length]} />)}
                          </Pie>
                          <Tooltip />
                        </PieChart>
                      </ResponsiveContainer>
                  }
                </div>
              </div>

              <div className="card" style={{ gridColumn: '1 / -1' }}>
                <div className="card-header"><h3>📋 Summary Table ({selYear})</h3></div>
                <div className="card-body" style={{ padding: 0 }}>
                  <table>
                    <thead><tr><th>Month</th><th>Appointments</th></tr></thead>
                    <tbody>
                      {monthly.map((m, i) => (
                        <tr key={i}><td>{m.month}</td><td>{m.total}</td></tr>
                      ))}
                      <tr style={{ fontWeight: 700 }}>
                        <td>Total</td>
                        <td>{monthly.reduce((sum, m) => sum + m.total, 0)}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )
        }
      </div>
    </>
  )
}
