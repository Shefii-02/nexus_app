import type { TeacherFormPayload } from '../teacherService'

interface TeacherFormProps {
  defaultValues: TeacherFormPayload
  loading: boolean
  error?: string
  isEdit?: boolean
  onChange: (field: keyof TeacherFormPayload, value: string | number) => void
  onSubmit: () => void
}

const TeacherForm = ({ defaultValues, onChange, onSubmit, loading, error, isEdit = false }: TeacherFormProps) => {
  return (
    <div className="space-y-6">

      {/* 👤 USER DETAILS */}
      <div className="rounded-2xl border p-5 bg-white shadow-sm">
        <h3 className="text-lg font-semibold mb-4">User Details</h3>
        <div className="mb-4 text-sm text-slate-500">These details are used for logging in and contacting the teacher.</div>
        <div className=''>
          <label className="block text-sm font-medium text-slate-700 mb-1">Name</label>
          <input
            placeholder="Name"
            value={defaultValues.name}
            onChange={(e) => onChange('name', e.target.value)}
            className="w-full border p-2 rounded mb-3"
          />

        </div>
        <div className=''>
          <label className="block text-sm font-medium text-slate-700 mb-1">Email</label>
          <input
            placeholder="Email"
            value={defaultValues.email}
            onChange={(e) => onChange('email', e.target.value)}
            className="w-full border p-2 rounded mb-3"
          />
        </div>
        <div className=''>
          <label className="block text-sm font-medium text-slate-700 mb-1">Phone</label>
          <input
            placeholder="Phone"
            value={defaultValues.phone}
            onChange={(e) => onChange('phone', e.target.value)}
            className="w-full border p-2 rounded mb-3"
          />
        </div>
        {!isEdit && (
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input
              type="password"
              value={defaultValues.password || ''}
              autoComplete='new-password'
              onChange={(e) => onChange('password', e.target.value)}
              className="w-full border p-2 rounded"
            />
          </div>
        )}
      </div>

      {/* 🎓 TEACHER DETAILS */}
      <div className="rounded-2xl border p-5 bg-white shadow-sm">
        <h3 className="text-lg font-semibold mb-4">Teacher Details</h3>
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-1">Subject</label>

          <input
            placeholder="Subject"
            value={defaultValues.subject}
            onChange={(e) => onChange('subject', e.target.value)}
            className="w-full border p-2 rounded mb-3"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-1">Qualification</label>

          <input
            placeholder="Qualification"
            value={defaultValues.qualification}
            onChange={(e) => onChange('qualification', e.target.value)}
            className="w-full border p-2 rounded mb-3"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-1">Experience (years)</label>

          <input
            type="number"
            placeholder="Experience (years)"
            value={defaultValues.experience_years}
            onChange={(e) => onChange('experience_years', Number(e.target.value))}
            className="w-full border p-2 rounded mb-3"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-1">Address</label>

          <textarea
            placeholder="Address"
            value={defaultValues.address}
            onChange={(e) => onChange('address', e.target.value)}
            className="w-full border p-2 rounded"
          />
        </div>
        <div >
                    <label className="block text-sm font-medium text-slate-700 mb-1">Status</label>
        
        </div>
      </div>

      {/* ERROR */}
      {error && <div className="text-red-500">{error}</div>}

      {/* SUBMIT */}
      <button
        onClick={onSubmit}
        disabled={loading}
        className="w-full bg-black text-white p-3 rounded"
      >
        {loading ? 'Saving...' : isEdit ? 'Update Teacher' : 'Create Teacher'}
      </button>
    </div>
  )
}

export default TeacherForm
