interface Props {
  label: string
  name: string
  register: any
  disabled?: boolean
}

const ToggleField = ({
  label,
  name,
  register,
  disabled = false,
}: Props) => {
  return (
    <label
      className={`flex mt-10 items-center justify-between w-full ${
        disabled
          ? 'opacity-60 cursor-not-allowed'
          : 'cursor-pointer'
      }`}
    >
      <span className="text-sm font-medium">
        {label}
      </span>

      <div className="relative">
        <input
          type="checkbox"
          {...register(name)}
          disabled={disabled}
          className="peer sr-only"
        />

        <div
          className="
            w-11
            h-6
            bg-gray-300
            rounded-full
            transition-all
            peer-checked:bg-green-500
          "
        />

        <div
          className="
            absolute
            top-0.5
            left-0.5
            w-5
            h-5
            bg-white
            rounded-full
            shadow
            transition-all
            peer-checked:translate-x-5
          "
        />
      </div>
    </label>
  )
}

export default ToggleField