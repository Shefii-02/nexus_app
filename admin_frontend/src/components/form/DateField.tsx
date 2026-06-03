import type { FieldError, UseFormRegister, UseFormWatch } from 'react-hook-form'

interface Props {
  label: string
  name: string
  register: UseFormRegister<any>
  watch: UseFormWatch<any>
  error?: FieldError
  disabled?: boolean
}

const DateField = ({
  label,
  name,
  register,
  watch,
  error,
  disabled = false,
}: Props) => {
  const value = watch(name)

  return (
    <div>
      {/* Label */}
      <label className="text-sm mb-1 block">{label}</label>

      {/* Input */}
      <input
        type="date"
        // {...register(name)}
        {...register(name, {
        validate: (value) => {
          if (!value) return true

          const date = new Date(value)

          if (isNaN(date.getTime())) {
            return 'Invalid date'
          }

          return true
        },
      })}
        value={value || ''} // ✅ IMPORTANT
        disabled={disabled}
        className={`w-full border rounded-xl px-3 pb-2 pt-4 
        ${error ? 'border-red-500' : 'border-gray-300'}`}
      />

      {/* Error */}
      {error && (
        <p className="text-red-500 text-xs mt-1">
          {error.message}
        </p>
      )}
    </div>
  )
}

export default DateField