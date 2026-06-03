import { useState, useRef } from 'react'
import Cropper from 'react-easy-crop'
import imageCompression from 'browser-image-compression'

interface Props {
  label: string
  name: string
  setValue: any
  watch: any
  error?: any
}

const ImageUploadWithCrop = ({ label, name, setValue, watch, error }: Props) => {
  const inputRef = useRef<HTMLInputElement | null>(null)
  const file = watch(name)

  const [crop, setCrop] = useState({ x: 0, y: 0 })
  const [zoom, setZoom] = useState(1)
  const [imageSrc, setImageSrc] = useState<string | null>(null)
  const [croppedAreaPixels, setCroppedAreaPixels] = useState<any>(null)

  const onCropComplete = (_: any, croppedAreaPixels: any) => {
    setCroppedAreaPixels(croppedAreaPixels)
  }

  const handleFile = async (file: File) => {
    const reader = new FileReader()
    reader.onload = () => setImageSrc(reader.result as string)
    reader.readAsDataURL(file)
  }

  const getCroppedImg = async () => {
    const image = new Image()
    image.src = imageSrc!

    await new Promise((resolve) => (image.onload = resolve))

    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')!

    canvas.width = croppedAreaPixels.width
    canvas.height = croppedAreaPixels.height

    ctx.drawImage(
      image,
      croppedAreaPixels.x,
      croppedAreaPixels.y,
      croppedAreaPixels.width,
      croppedAreaPixels.height,
      0,
      0,
      croppedAreaPixels.width,
      croppedAreaPixels.height
    )

    return new Promise<File>((resolve) => {
      canvas.toBlob(async (blob: any) => {
        // 🔥 compress after crop
        const compressed = await imageCompression(blob, {
          maxSizeMB: 0.5,
          maxWidthOrHeight: 1024,
        })

        const finalFile = new File([compressed], 'thumbnail.jpg', {
          type: 'image/jpeg',
        })

        resolve(finalFile)
      }, 'image/jpeg')
    })
  }

  const handleCropSave = async () => {
    const croppedFile = await getCroppedImg()
    setValue(name, croppedFile)
    setImageSrc(null)
  }

  const removeImage = () => {
    setValue(name, null)
  }

  return (
    <div className="col-span-2">
      <label className="block text-sm mb-2">{label}</label>

      {/* Upload Area */}
      <div
        onClick={() => inputRef.current?.click()}
        className="border-2 border-dashed p-6 text-center rounded cursor-pointer"
      >
        <input
          ref={inputRef}
          type="file"
          hidden
          accept="image/*"
          onChange={(e) => {
            const f = e.target.files?.[0]
            if (f) handleFile(f)
          }}
        />

        {!file ? (
          <p>Click or drag image</p>
        ) : (
          <img
            src={
              typeof file === 'string'
                ? file
                : URL.createObjectURL(file)
            }
            className="h-32 mx-auto"
          />
        )}
      </div>

      {/* Actions */}
      {file && (
        <div className="flex gap-2 mt-2">
          <button
            type="button"
            onClick={() => inputRef.current?.click()}
            className="px-3 py-1 bg-gray-200 rounded"
          >
            Change
          </button>

          <button
            type="button"
            onClick={removeImage}
            className="px-3 py-1 bg-red-500 text-white rounded"
          >
            Remove
          </button>
        </div>
      )}

      {/* Crop Modal */}
      {imageSrc && (
        <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
          <div className="bg-white p-4 rounded w-[400px]">
            <div className="relative h-64">
              <Cropper
                image={imageSrc}
                crop={crop}
                zoom={zoom}
                aspect={16 / 9}
                onCropChange={setCrop}
                onZoomChange={setZoom}
                onCropComplete={onCropComplete}
              />
            </div>

            <div className="flex justify-between mt-4">
              <button
                onClick={() => setImageSrc(null)}
                className="px-3 py-1 bg-gray-300 rounded"
              >
                Cancel
              </button>

              <button
                onClick={handleCropSave}
                className="px-3 py-1 bg-black text-white rounded"
              >
                Crop & Save
              </button>
            </div>
          </div>
        </div>
      )}

      {error && <p className="text-red-500 text-xs">{error.message}</p>}
    </div>
  )
}

export default ImageUploadWithCrop