import { useNavigate, useParams } from 'react-router-dom'
import PageHeader from '../../components/PageHeader'
import Button from '../../components/Button'
import { useMaterial } from './materialHooks'

const ViewMaterial = () => {
  const navigate = useNavigate()

  const { courseId, materialId } = useParams()

  const { data, isLoading } = useMaterial(
    Number(materialId),
    Number(courseId)
  )

  if (isLoading)
    return (
      <div className="p-10 text-center">
        Loading Material...
      </div>
    )

  if (!data)
    return (
      <div className="p-10 text-center">
        Material not found
      </div>
    )

  const material = data;

  const isPdf =
    material.material_type === 'pdf'

  const isVideo =
    material.material_type === 'video'

  const isYoutube =
    material.file_url?.includes('youtube.com') ||
    material.file_url?.includes('youtu.be')

  const youtubeEmbed = material.file_url
    ?.replace('watch?v=', 'embed/')
    ?.replace('youtu.be/', 'youtube.com/embed/')
console.log(material)
  return (
    <div className="space-y-6">
      <PageHeader
        title={material.title}
        subtitle="Course Material Details"
        actions={
          <div className="flex gap-2">
            <Button
              variant="secondary"
              onClick={() =>
                navigate(`/courses/${courseId}/materials`)
              }
            >
              Back
            </Button>

            <Button
              onClick={() =>
                navigate(
                  `/courses/${courseId}/materials/${material.id}/edit`
                )
              }
            >
              Edit
            </Button>
          </div>
        }
      />

      {/* Preview */}
      <div className="bg-white rounded-2xl shadow-sm h-60 border overflow-hidden">
        {isPdf && (
          <iframe
            src={material.file_url}
            className="w-full h-[700px]"
            title="PDF Preview"
          />
        )}

        {isVideo && isYoutube && (
          <iframe
            src={youtubeEmbed}
            className="w-full h-[600px]"
            allowFullScreen
            title="Video"
          />
        )}

        {isVideo && !isYoutube && (
          <video
            controls
            className="w-full"
          >
            <source
              src={material.file_url}
            />
          </video>
        )}
      </div>

      {/* Details */}
      <div className="grid md:grid-cols-3 gap-6">
        {/* Left */}
        <div className="md:col-span-2 bg-white border rounded-2xl p-6">
          <h2 className="text-xl font-semibold mb-4">
            {material.title}
          </h2>

          <p className="text-gray-600 whitespace-pre-wrap">
            {material.description}
          </p>
        </div>

        {/* Right */}
        <div className="bg-white border rounded-2xl p-6">
          <h3 className="font-semibold mb-4">
            Material Info
          </h3>

          <div className="space-y-4">
            <div>
              <div className="text-xs text-gray-500">
                Type
              </div>

              <div className="font-medium">
                {material.material_type}
              </div>
            </div>

            <div>
              <div className="text-xs text-gray-500">
                Order
              </div>

              <div className="font-medium">
                #{material.order}
              </div>
            </div>

            <div>
              <div className="text-xs text-gray-500">
                Status
              </div>

              <span
                className={`inline-flex px-3 py-1 rounded-full text-xs font-medium ${
                  material.status === 'active'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700'
                }`}
              >
                {material.status}
              </span>
            </div>

            <div>
              <div className="text-xs text-gray-500">
                Created
              </div>

              <div className="font-medium">
                {material.created_at}
              </div>
            </div>

            <a
              href={material.file_url}
              target="_blank"
              rel="noreferrer"
              className="block"
            >
              <Button className="w-full">
                Download Material
              </Button>
            </a>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ViewMaterial