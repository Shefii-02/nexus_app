import InputField from "./InputField"

export const NumberField = ({
  label,
  value,
  onChange,
}: {
  label: string
  value: number | '' // ✅ allow empty
  onChange: (value: number | '') => void
}) => {
  return (
    <InputField
      label={label}
      value={value}
      type="number"
      onChange={(val) => {
        if (val === '') return onChange('') // ✅ allow clear

        const num = Number(val)
        if (!isNaN(num)) {
          onChange(num)
        }
      }}
    />
  )
}