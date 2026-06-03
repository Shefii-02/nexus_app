import { useEffect, useState } from 'react'
import type {
  UseFormSetValue,
  UseFormWatch,
  FieldError,
} from 'react-hook-form'

interface Props {
  label: string
  name: string
  setValue: UseFormSetValue<any>
  watch: UseFormWatch<any>
  error?: FieldError
  disabled?: boolean
}

const Thumbnail = ({
  label,
  name,
  setValue,
  watch,
  error,
  disabled = false,
}: Props) => {
  const file = watch(name)
  const [preview, setPreview] = useState<string | null>(null)

  // 🔥 handle preview safely
  useEffect(() => {
    if (!file) {
      setPreview(null)
      return
    }

    if (typeof file === 'string') {
      setPreview(file)
      return
    }

    const objectUrl = URL.createObjectURL(file)
    setPreview(objectUrl)

    return () => URL.revokeObjectURL(objectUrl)
  }, [file])

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const selectedFile = e.target.files?.[0]
    if (selectedFile) {
      setValue(name, selectedFile, {
        shouldValidate: true,
        shouldDirty: true,
      })
    }
  }

  const removeImage = () => {
    setValue(name, null, {
      shouldValidate: true,
      shouldDirty: true,
    })
  }

  return (
    <div className="col-span-2">
      {/* Label */}
      <label className="block text-sm mb-1 font-medium">
        {label}
      </label>

      {/* Input */}
      <input
        type="file"
        accept="image/*"
        disabled={disabled}
        onChange={handleChange}
        className={`w-full border rounded px-3 py-2 ${
          disabled ? 'bg-gray-100 cursor-not-allowed' : ''
        }`}
      />

      {/* Preview */}
      {preview && (
        <div className="mt-3 space-y-2">
          <img
            src={preview}
            alt="thumbnail preview"
            className="h-24 rounded border"
          />

          {/* Actions */}
          {!disabled && (
            <button
              type="button"
              onClick={removeImage}
              className="text-xs text-red-500"
            >
              Remove
            </button>
          )}
        </div>
      )}

      {/* Error */}
      {error && (
        <p className="text-red-500 text-xs mt-1">
          {error.message}
        </p>
      )}
    </div>
  )
}

export default Thumbnail