import type { FieldError } from 'react-hook-form'

interface Props {
  label: string
  name: string
  register: any
  rules?: any
  type?: string
  error?: FieldError
  readOnly?: boolean
}

const FloatingInput = ({
  label,
  name,
  register,
  rules,
  error,
  type = 'text',
  readOnly = false,
}: Props) => {
  return (
    <div className="relative">
      <input
        id={name} // ✅ important for label
        type={type}
        {...register(name, rules)}
        placeholder=" "
        autoComplete="new-password"
        readOnly={readOnly}
        className={`peer w-full border rounded-xl px-3 pt-4 pb-2  mt-6
        ${error ? 'border-red-500' : 'border-gray-300'}
        ${readOnly ? 'bg-gray-100 cursor-not-allowed' : ''}`}
      />
{/* peer-placeholder-shown:top-3.5 */}
      <label
        htmlFor={name}
        className="absolute left-3 top-2 text-sm text-gray-500 bg-white
        transition-all
        peer-placeholder-shown:top-4
        peer-placeholder-shown:text-base 
        peer-focus:top-2 
        peer-focus:text-sm"
      >
        {label}
      </label>

      {error && (
        <p className="text-red-500 text-xs mt-1">
          {error.message}
        </p>
      )}
    </div>
  )
}

export default FloatingInput