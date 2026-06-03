interface Option {
  label: string
  value: string | number
  disabled?: boolean
}

interface Props {
  label: string
  name: string
  options: Option[]
  register: any
  watch: any
  error?: any
  disabled?: boolean // ✅ FIX
}

const RadioField = ({
  label,
  name,
  options,
  register,
  watch,
  error,
  disabled = false,
}: Props) => {
  const selectedValue = watch(name)

  return (
    <div>
      {/* Label */}
      <label className="text-sm mb-1 block">{label}</label>

      {/* Radio Options */}
      <div className="flex flex-wrap gap-4">
        {options.map((opt) => (
          <label
            key={opt.value}
            className={`flex items-center gap-2 ${
              opt.disabled || disabled
                ? 'opacity-50 cursor-not-allowed'
                : 'cursor-pointer'
            }`}
          >
            <input
              type="radio"
              value={opt.value}
              {...register(name)}
              checked={selectedValue == opt.value}
              disabled={opt.disabled || disabled} // ✅ FIX
              className="accent-black"
            />

            <span className="text-sm">{opt.label}</span>
          </label>
        ))}
      </div>

      {/* Error */}
      {error && (
        <p className="text-red-500 text-xs mt-1">
          {error.message}
        </p>
      )}
    </div>
  )
}

export default RadioField