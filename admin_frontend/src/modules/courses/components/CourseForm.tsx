import type { CoursePayload } from '../courseService'

interface CourseFormProps {
  defaultValues: CoursePayload
  loading: boolean
  error?: string
  onChange: (field: keyof CoursePayload, value: string) => void
  onSubmit: () => void
}

const CourseForm = ({ defaultValues, loading, error, onChange, onSubmit }: CourseFormProps) => {
  return (
    <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
      <h2 className="mb-4 text-xl font-semibold text-slate-900">Course details</h2>
      {error ? <div className="mb-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{error}</div> : null}
      <div className="space-y-4">
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Title</span>
          <input
            value={defaultValues.title}
            onChange={(event) => onChange('title', event.target.value)}
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="Course title"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Description</span>
          <textarea
            value={defaultValues.description || ''}
            onChange={(event) => onChange('description', event.target.value)}
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            rows={4}
            placeholder="Course description"
          />
        </label>
      </div>
      <button
        type="button"
        onClick={onSubmit}
        disabled={loading}
        className="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
      >
        {loading ? 'Saving...' : 'Save course'}
      </button>
    </div>
  )
}

export default CourseForm
