import { useNavigate } from 'react-router-dom'

export default function EnrollAdmissionPage() {
  const navigate = useNavigate()

  return (
    <div className="rounded-3xl bg-white p-6 shadow-sm shadow-slate-200/50">
      <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-semibold text-slate-900">Enroll Admission</h1>
          <p className="mt-2 text-sm text-slate-500">Capture admission details by selecting a student and course.</p>
        </div>
        <button
          type="button"
          onClick={() => navigate('/payments/admissions')}
          className="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700"
        >
          View Admissions
        </button>
      </div>
      <div className="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-6">
        <div>
          <label className="mb-1 block text-sm font-medium text-slate-700">Student</label>
          <input className="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900" placeholder="Search student by name or ID" />
        </div>
        <div>
          <label className="mb-1 block text-sm font-medium text-slate-700">Course</label>
          <input className="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900" placeholder="Search course by name or code" />
        </div>
        <div>
          <label className="mb-1 block text-sm font-medium text-slate-700">Admission notes</label>
          <textarea className="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900" placeholder="Enter admission details" rows={4} />
        </div>
        <div className="flex justify-end gap-3">
          <button
            type="button"
            onClick={() => navigate('/payments')}
            className="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
          >
            Cancel
          </button>
          <button
            type="button"
            className="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700"
          >
            Enroll Admission
          </button>
        </div>
      </div>
    </div>
  )
}
