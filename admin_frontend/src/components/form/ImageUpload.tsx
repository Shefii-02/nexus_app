import { useRef } from 'react'
import imageCompression from 'browser-image-compression'

interface Props {
  label: string
  name: string
  setValue: any
  watch: any
  error?: any
}

const ImageUpload = ({ label, name, setValue, watch, error }: Props) => {
  const inputRef = useRef<HTMLInputElement | null>(null)
  const file = watch(name)

  const handleFile = async (file: File) => {
    try {
      // 🔥 Compress image
      const compressed = await imageCompression(file, {
        maxSizeMB: 0.5,
        maxWidthOrHeight: 1024,
        useWebWorker: true,
      })

      setValue(name, compressed)
    } catch (err) {
      console.error('Compression failed', err)
    }
  }

  const handleDrop = async (e: React.DragEvent) => {
    e.preventDefault()
    const droppedFile = e.dataTransfer.files?.[0]
    if (droppedFile) handleFile(droppedFile)
  }

  const handleChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const selected = e.target.files?.[0]
    if (selected) handleFile(selected)
  }

  const removeImage = () => {
    setValue(name, null)
  }

  return (
    <div className="col-span-2">
      <label className="block text-sm mb-2 font-medium">{label}</label>

      {/* DROP AREA */}
      <div
        onDrop={handleDrop}
        onDragOver={(e) => e.preventDefault()}
        onClick={() => inputRef.current?.click()}
        className="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer hover:border-black transition"
      >
        <input
          ref={inputRef}
          type="file"
          accept="image/*"
          hidden
          onChange={handleChange}
        />

        {!file ? (
          <p className="text-gray-500">
            Drag & drop image here or click to upload
          </p>
        ) : (
          <div className="space-y-3">
            <img
              src={
                typeof file === 'string'
                  ? file
                  : URL.createObjectURL(file)
              }
              className="h-32 mx-auto rounded"
            />

            <div className="flex justify-center gap-2">
              <button
                type="button"
                onClick={(e) => {
                  e.stopPropagation()
                  inputRef.current?.click()
                }}
                className="px-3 py-1 bg-gray-200 rounded"
              >
                Change
              </button>

              <button
                type="button"
                onClick={(e) => {
                  e.stopPropagation()
                  removeImage()
                }}
                className="px-3 py-1 bg-red-500 text-white rounded"
              >
                Remove
              </button>
            </div>
          </div>
        )}
      </div>

      {error && (
        <p className="text-red-500 text-xs mt-1">{error.message}</p>
      )}
    </div>
  )
}

export default ImageUpload