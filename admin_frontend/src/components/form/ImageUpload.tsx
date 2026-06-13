import { useRef, useState } from 'react'
import imageCompression from 'browser-image-compression'

interface Props {
  label: string
  name: string
  setValue: any
  watch: any
  error?: any
  disabled?: boolean
}

const MAX_FILE_SIZE_MB = 10

const ImageUpload = ({
  label,
  name,
  setValue,
  watch,
  error,
  disabled = false,
}: Props) => {
  const inputRef = useRef<HTMLInputElement | null>(null)

  const file = watch(name)

  const [preview, setPreview] = useState<string | null>(null)
  const [isProcessing, setIsProcessing] = useState(false)
  const [progress, setProgress] = useState(0)

  const handleFile = async (selectedFile: File) => {
    try {
      // Size validation
      if (
        selectedFile.size >
        MAX_FILE_SIZE_MB * 1024 * 1024
      ) {
        alert(
          `File size cannot exceed ${MAX_FILE_SIZE_MB} MB`
        )
        return
      }


      // Show preview instantly
      const localPreview =
        URL.createObjectURL(selectedFile)

      setPreview(localPreview)
      setIsProcessing(true)

      const compressed =
        await imageCompression(selectedFile, {
          maxSizeMB: 0.5,
          maxWidthOrHeight: 1200,
          useWebWorker: true,

          onProgress: (p) => {
            setProgress(p)
          },
        })

      setValue(name, compressed, {
        shouldValidate: true,
      })
    } catch (err) {
      console.error(
        'Image compression failed',
        err
      )

      alert('Failed to process image')
    } finally {
      setIsProcessing(false)
      setProgress(0)
    }


  }

  const handleDrop = async (
    e: React.DragEvent
  ) => {
    e.preventDefault()


    if (disabled) return

    const dropped =
      e.dataTransfer.files?.[0]

    if (dropped) {
      await handleFile(dropped)
    }


  }

  const handleChange = async (
    e: React.ChangeEvent<HTMLInputElement>
  ) => {
    const selected =
      e.target.files?.[0]


    if (selected) {
      await handleFile(selected)
    }


  }

  const removeImage = () => {
    setPreview(null)


    setValue(name, null, {
      shouldValidate: true,
    })

    if (inputRef.current) {
      inputRef.current.value = ''
    }


  }

  const imageSrc =
    preview ||
    (typeof file === 'string'
      ? file
      : file
        ? URL.createObjectURL(file)
        : null)

  return (<div className="col-span-2"> <label className="block text-sm font-medium mb-2">
    {label} </label>


    <div
      onDrop={handleDrop}
      onDragOver={(e) =>
        e.preventDefault()
      }
      onClick={() => {
        if (!disabled) {
          inputRef.current?.click()
        }
      }}
      className={`
      border-2 border-dashed rounded-xl p-6 text-center transition
      ${disabled
          ? 'bg-gray-100 cursor-not-allowed'
          : 'cursor-pointer hover:border-black'
        }
    `}
    >
      <input
        ref={inputRef}
        type="file"
        accept="image/*"
        hidden
        disabled={disabled}
        onChange={handleChange}
      />

      {!imageSrc ? (
        <div className="space-y-2">
          <p className="text-gray-500">
            Drag & drop image here
          </p>

          <p className="text-gray-500">
            or click to upload
          </p>

          <p className="text-xs text-gray-400">
            JPG, PNG, WEBP
          </p>

          <p className="text-xs text-gray-400">
            Max {MAX_FILE_SIZE_MB} MB
          </p>
        </div>
      ) : (
        <div className="space-y-4">
          <img
            src={imageSrc}
            alt="Preview"
            className="h-40 w-auto mx-auto rounded-lg object-cover"
          />

          {isProcessing && (
            <div className="space-y-3">
              <div className="flex justify-center">
                <div className="h-8 w-8 border-4 border-gray-300 border-t-black rounded-full animate-spin" />
              </div>

              <p className="text-sm text-gray-600">
                Optimizing image...
              </p>

              <div className="w-full bg-gray-200 rounded-full h-2">
                <div
                  className="bg-black h-2 rounded-full transition-all"
                  style={{
                    width: `${progress}%`,
                  }}
                />
              </div>

              <p className="text-xs text-gray-500">
                {progress}%
              </p>
            </div>
          )}

          {!isProcessing && (
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
          )}
        </div>
      )}
    </div>

    {error && (
      <p className="text-red-500 text-xs mt-2">
        {error.message}
      </p>
    )}
  </div>


  )
}

export default ImageUpload


// import { useRef } from 'react'
// import imageCompression from 'browser-image-compression'

// interface Props {
//   label: string
//   name: string
//   setValue: any
//   watch: any
//   error?: any
// }

// const ImageUpload = ({ label, name, setValue, watch, error }: Props) => {
//   const inputRef = useRef<HTMLInputElement | null>(null)
//   const file = watch(name)

//   const handleFile = async (file: File) => {
//     try {
//       // 🔥 Compress image
//       const compressed = await imageCompression(file, {
//         maxSizeMB: 0.5,
//         maxWidthOrHeight: 1024,
//         useWebWorker: true,
//       })

//       setValue(name, compressed)
//     } catch (err) {
//       console.error('Compression failed', err)
//     }
//   }

//   const handleDrop = async (e: React.DragEvent) => {
//     e.preventDefault()
//     const droppedFile = e.dataTransfer.files?.[0]
//     if (droppedFile) handleFile(droppedFile)
//   }

//   const handleChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
//     const selected = e.target.files?.[0]
//     if (selected) handleFile(selected)
//   }

//   const removeImage = () => {
//     setValue(name, null)
//   }

//   return (
//     <div className="col-span-2">
//       <label className="block text-sm mb-2 font-medium">{label}</label>

//       {/* DROP AREA */}
//       <div
//         onDrop={handleDrop}
//         onDragOver={(e) => e.preventDefault()}
//         onClick={() => inputRef.current?.click()}
//         className="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer hover:border-black transition"
//       >
//         <input
//           ref={inputRef}
//           type="file"
//           accept="image/*"
//           hidden
//           onChange={handleChange}
//         />

//         {!file ? (
//           <p className="text-gray-500">
//             Drag & drop image here or click to upload
//           </p>
//         ) : (
//           <div className="space-y-3">
//             <img
//               src={
//                 typeof file === 'string'
//                   ? file
//                   : URL.createObjectURL(file)
//               }
//               className="h-32 mx-auto rounded"
//             />

//             <div className="flex justify-center gap-2">
//               <button
//                 type="button"
//                 onClick={(e) => {
//                   e.stopPropagation()
//                   inputRef.current?.click()
//                 }}
//                 className="px-3 py-1 bg-gray-200 rounded"
//               >
//                 Change
//               </button>

//               <button
//                 type="button"
//                 onClick={(e) => {
//                   e.stopPropagation()
//                   removeImage()
//                 }}
//                 className="px-3 py-1 bg-red-500 text-white rounded"
//               >
//                 Remove
//               </button>
//             </div>
//           </div>
//         )}
//       </div>

//       {error && (
//         <p className="text-red-500 text-xs mt-1">{error.message}</p>
//       )}
//     </div>
//   )
// }

// export default ImageUpload