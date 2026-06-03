import type { FieldError, UseFormRegister } from 'react-hook-form'

interface Props {
  label: string
  name: string
  register: UseFormRegister<any>
  error?: FieldError
  disabled?: boolean
  readOnly?: boolean
}

const TextareaField = ({
  label,
  name,
  register,
  error,
  disabled = false,
  readOnly = false,
}: Props) => {
  return (
    <div>
      <label className="text-sm mb-1 block">{label}</label>

      <textarea
        {...register(name)}
        rows={3}
        disabled={disabled}
        readOnly={readOnly}
        className={`w-full border p-2 rounded-xl resize-none
          ${error ? 'border-red-500' : 'border-gray-300'}
          ${disabled || readOnly ? 'bg-gray-100 cursor-not-allowed' : ''}
        `}
      />

      {error && (
        <p className="text-red-500 text-xs mt-1">
          {error.message}
        </p>
      )}
    </div>
  )
}

export default TextareaField