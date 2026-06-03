interface Props {
  label: string
  name: string
  register: any
  disabled?: boolean
}

const CheckboxField = ({ label, name, register, disabled = false }: Props) => {
  return (
    <label
      className={`flex items-center gap-2 ${
        disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
      }`}
    >
      <input
        type="checkbox"
        {...register(name)}
        disabled={disabled}
        className="accent-black"
      />
      {label}
    </label>
  )
}

export default CheckboxField