import type { StaffPayload } from '../staffService'

interface StaffFormProps {
  defaultValues: StaffPayload
  loading: boolean
  error?: string
  onChange: (field: keyof StaffPayload, value: string) => void
  onSubmit: () => void
}

const StaffForm = ({ defaultValues, loading, error, onChange, onSubmit }: StaffFormProps) => {
  return (
    <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
      <h2 className="mb-4 text-xl font-semibold text-slate-900">Staff details</h2>
      {error ? <div className="mb-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{error}</div> : null}
      <div className="space-y-4">
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Name</span>
          <input
            value={defaultValues.name}
            onChange={(event) => onChange('name', event.target.value)}
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="Staff Name"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Email</span>
          <input
            value={defaultValues.email}
            onChange={(event) => onChange('email', event.target.value)}
            type="email"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="staff@example.com"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Department</span>
          <input
            value={defaultValues.department || ''}
            onChange={(event) => onChange('department', event.target.value)}
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="Admissions, Support, etc."
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Phone</span>
          <input
            value={defaultValues.phone || ''}
            onChange={(event) => onChange('phone', event.target.value)}
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="(555) 123-4567"
          />
        </label>
      </div>
      <button
        type="button"
        onClick={onSubmit}
        disabled={loading}
        className="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
      >
        {loading ? 'Saving...' : 'Save staff'}
      </button>
    </div>
  )
}

export default StaffForm
