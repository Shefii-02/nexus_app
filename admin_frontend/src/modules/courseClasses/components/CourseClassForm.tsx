interface CourseClassFormValues {
  course_title: string
  instructor_name: string
  start_date: string
  end_date: string
}

interface CourseClassFormProps {
  defaultValues: CourseClassFormValues
  loading: boolean
  error?: string
  onChange: (field: keyof CourseClassFormValues, value: string) => void
  onSubmit: () => void
}

const CourseClassForm = ({ defaultValues, loading, error, onChange, onSubmit }: CourseClassFormProps) => {
  return (
    <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
      <h2 className="mb-4 text-xl font-semibold text-slate-900">Class details</h2>
      {error ? <div className="mb-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{error}</div> : null}
      <div className="space-y-4">
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Course title</span>
          <input
            value={defaultValues.course_title}
            onChange={(event) => onChange('course_title', event.target.value)}
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="Course title"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Instructor</span>
          <input
            value={defaultValues.instructor_name}
            onChange={(event) => onChange('instructor_name', event.target.value)}
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="Instructor name"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Start date</span>
          <input
            value={defaultValues.start_date}
            onChange={(event) => onChange('start_date', event.target.value)}
            type="date"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">End date</span>
          <input
            value={defaultValues.end_date}
            onChange={(event) => onChange('end_date', event.target.value)}
            type="date"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
          />
        </label>
      </div>
      <button
        type="button"
        onClick={onSubmit}
        disabled={loading}
        className="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
      >
        {loading ? 'Saving...' : 'Save class'}
      </button>
    </div>
  )
}

export default CourseClassForm
