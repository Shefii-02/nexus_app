import type { UseFormRegister } from 'react-hook-form'

interface Props {
  label: string
  name: string
  register: UseFormRegister<any>
  disabled?: boolean
}

const ToggleField = ({ label, name, register, disabled = false }: Props) => {
  return (
    <label className="flex items-center gap-3 cursor-pointer">
      {/* Hidden checkbox (peer) */}
      <input
        type="checkbox"
        {...register(name)}
        disabled={disabled}
        className="peer sr-only"
      />

      {/* Toggle Track */}
      <div
        className={`w-10 h-5 rounded-full transition-colors
        bg-gray-300 peer-checked:bg-black
        ${disabled ? 'opacity-50 cursor-not-allowed' : ''}
      `}
      >
        {/* Toggle Thumb */}
        <div
          className="w-4 h-4 bg-white rounded-full shadow absolute top-[2px] left-[2px]
          transition-all peer-checked:translate-x-5"
        />
      </div>

      {/* Label */}
      <span className="text-sm">{label}</span>
    </label>
  )
}

export default ToggleField