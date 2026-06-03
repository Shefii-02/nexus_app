interface InputFieldProps {
  label: string
  value: string | number
  onChange: (value: string) => void
  placeholder?: string
  type?: 'text' | 'email' | 'number' | 'password'
  error?: string
  readOnly?: boolean // optional
}

const InputField = ({
  label,
  value,
  onChange,
  placeholder,
  type = 'text',
  error,
  readOnly = false, // ✅ default
}: InputFieldProps) => {
  return (
    <div>
      <label className="block text-sm font-medium text-slate-700 mb-1">
        {label}
      </label>

      <input
        type={type}
        value={value}
        placeholder={placeholder}
        onChange={(e) => onChange(e.target.value)}
        readOnly={readOnly}
        className={`w-full border p-2 rounded ${
          error ? 'border-red-500' : 'border-slate-300'
        } ${readOnly ? 'bg-gray-100 cursor-not-allowed' : ''}`}
      />

      {error && <p className="text-red-500 text-xs mt-1">{error}</p>}
    </div>
  )
}

export default InputField