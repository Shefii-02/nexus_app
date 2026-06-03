import { useNavigate } from 'react-router-dom'

export default function CreateGroupPage() {
  const navigate = useNavigate()

  return (
    <div className="rounded-3xl bg-white p-6 shadow-sm shadow-slate-200/50">
      <div className="mb-6">
        <h1 className="text-2xl font-semibold text-slate-900">Create Group</h1>
        <p className="mt-2 text-sm text-slate-500">Set up a new study group, cohort, or class team.</p>
      </div>
      <div className="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-6">
        <div>
          <label className="mb-1 block text-sm font-medium text-slate-700">Group name</label>
          <input className="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900" placeholder="Enter group name" />
        </div>
        <div>
          <label className="mb-1 block text-sm font-medium text-slate-700">Description</label>
          <textarea className="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900" placeholder="Enter group description" rows={4} />
        </div>
        <div className="flex justify-end gap-3">
          <button
            type="button"
            onClick={() => navigate('/groups')}
            className="rounded-2xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
          >
            Cancel
          </button>
          <button
            type="button"
            className="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700"
          >
            Save Group
          </button>
        </div>
      </div>
    </div>
  )
}
