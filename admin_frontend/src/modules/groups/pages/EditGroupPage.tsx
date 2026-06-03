import { useNavigate, useParams } from 'react-router-dom'

export default function EditGroupPage() {
  const navigate = useNavigate()
  const { id } = useParams()

  return (
    <div className="rounded-3xl bg-white p-6 shadow-sm shadow-slate-200/50">
      <div className="mb-6">
        <h1 className="text-2xl font-semibold text-slate-900">Edit Group</h1>
        <p className="mt-2 text-sm text-slate-500">Update the details for group {id}.</p>
      </div>
      <div className="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-6">
        <div>
          <label className="mb-1 block text-sm font-medium text-slate-700">Group name</label>
          <input className="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900" placeholder="Enter group name" defaultValue={`Group ${id}`} />
        </div>
        <div>
          <label className="mb-1 block text-sm font-medium text-slate-700">Description</label>
          <textarea className="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900" placeholder="Enter group description" rows={4} defaultValue="Group description" />
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
            Save Changes
          </button>
        </div>
      </div>
    </div>
  )
}
