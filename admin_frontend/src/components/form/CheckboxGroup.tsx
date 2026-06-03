const CheckboxGroup = ({
  label,
  name,
  options,
  watch,
  setValue,
  error,
  disabled = false, // ✅ fix + default
}: Props) => {
  const selectedValues: string[] = watch(name) || []

  const handleChange = (value: string) => {
    if (disabled) return // 🔥 extra safety

    if (selectedValues.includes(value)) {
      setValue(
        name,
        selectedValues.filter((v) => v !== value)
      )
    } else {
      setValue(name, [...selectedValues, value])
    }
  }

  return (
    <div className="col-span-2">
      <label className="block text-sm font-medium mb-2">
        {label}
      </label>

      <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
        {options.map((opt) => (
          <label
            key={opt.value}
            className={`flex items-center gap-2 ${
              disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
            }`}
          >
            <input
              type="checkbox"
              checked={selectedValues.includes(opt.value)}
              onChange={() => handleChange(opt.value)}
              disabled={disabled}
              className="accent-black"
            />
            <span className="text-sm capitalize">
              {opt.label}
            </span>
          </label>
        ))}
      </div>

      {error && (
        <p className="text-red-500 text-sm mt-1">
          {error.message}
        </p>
      )}
    </div>
  )
}
export default CheckboxGroup