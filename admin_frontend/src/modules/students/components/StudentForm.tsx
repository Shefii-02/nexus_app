import type { StudentFormPayload } from '../studentService'

interface Props {
  values: StudentFormPayload
  onChange: (field: keyof StudentFormPayload, value: string) => void
  onSubmit: () => void
  loading: boolean
  isEdit?: boolean
}

const StudentForm = ({ values, onChange, onSubmit, loading, isEdit }: Props) => {
  return (
    <div className="space-y-6">

      {/* USER */}
      <div className="border p-4 rounded-lg">
        <h3 className="font-semibold mb-3">User Info</h3>

        <input
          placeholder="Name"
          value={values.name}
          onChange={(e) => onChange('name', e.target.value)}
          className="input"
        />

        <input
          placeholder="Email"
          value={values.email}
          onChange={(e) => onChange('email', e.target.value)}
          className="input"
        />

        {!isEdit && (
          <input
            placeholder="Password"
            type="password"
            value={values.password || ''}
            onChange={(e) => onChange('password', e.target.value)}
            className="input"
          />
        )}
      </div>

      {/* STUDENT */}
      <div className="border p-4 rounded-lg">
        <h3 className="font-semibold mb-3">Student Info</h3>

        <input
          placeholder="Roll Number"
          value={values.roll_number}
          onChange={(e) => onChange('roll_number', e.target.value)}
          className="input"
        />

        <input
          placeholder="Phone"
          value={values.phone}
          onChange={(e) => onChange('phone', e.target.value)}
          className="input"
        />

        <input
          placeholder="Guardian Name"
          value={values.guardian_name}
          onChange={(e) => onChange('guardian_name', e.target.value)}
          className="input"
        />

        <input
          placeholder="Guardian Phone"
          value={values.guardian_phone}
          onChange={(e) => onChange('guardian_phone', e.target.value)}
          className="input"
        />

        <textarea
          placeholder="Address"
          value={values.address}
          onChange={(e) => onChange('address', e.target.value)}
          className="input"
        />
      </div>

      <button
        onClick={onSubmit}
        disabled={loading}
        className="w-full bg-black text-white p-3 rounded"
      >
        {isEdit ? 'Update Student' : 'Create Student'}
      </button>
    </div>
  )
}

export default StudentForm