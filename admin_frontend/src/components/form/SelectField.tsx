interface Props {
  label: string
  name: string
  register: any
  options: { label: string; value: string | number }[]
  error?: any
  disabled?: boolean
}

const SelectField = ({
  label,
  name,
  register,
  options,
  error,
  disabled = false,
}: Props) => {
  return (
    <div>
      <label className="text-sm mb-1 block ">{label}</label>

      <select
        {...register(name)}
        disabled={disabled}
        className={`w-full border pb-2 px-3 pt-4 rounded-xl ${
          disabled ? 'bg-gray-100 cursor-not-allowed' : ''
        }`}
      >
        <option value="">Select</option>

        {options.map((opt) => (
          <option key={opt.value} value={opt.value}>
            {opt.label}
          </option>
        ))}
      </select>

      {error && (
        <p className="text-red-500 text-xs">
          {error.message}
        </p>
      )}
    </div>
  )
}

export default SelectField