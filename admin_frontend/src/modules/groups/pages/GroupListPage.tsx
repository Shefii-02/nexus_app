import { useNavigate } from 'react-router-dom'

export default function GroupListPage() {
  const navigate = useNavigate()

  return (
    <div className="rounded-3xl bg-white p-6 shadow-sm shadow-slate-200/50">
      <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-semibold text-slate-900">Groups</h1>
          <p className="mt-2 text-sm text-slate-500">Manage student groups, teams, and course cohorts.</p>
        </div>
        <button
          onClick={() => navigate('/groups/create')}
          className="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700"
        >
          Create Group
        </button>
      </div>

      <div className="rounded-2xl border border-slate-200 bg-slate-50 p-6">
        <p className="text-slate-600">This page is a placeholder for group management. Implement listing, editing, and deletion of groups here.</p>
      </div>
    </div>
  )
}
