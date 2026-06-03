import { useNavigate, useParams } from 'react-router-dom'
import PageHeader from '../../../components/PageHeader'
import Button from '../../../components/Button'
import { useAnnouncement } from '../announcementHooks'

const ViewAnnouncementPage = () => {
  const navigate = useNavigate()

  const { id } = useParams()

  const { data, isLoading } = useAnnouncement(Number(id))

  if (isLoading) {
    return <div>Loading...</div>
  }

  if (!data) {
    return <div>Announcement not found</div>
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title={data.title}
        actions={
          <Button
            onClick={() =>
              navigate(`/announcements/${id}/edit`)
            }
          >
            Edit
          </Button>
        }
      />

      <div className="bg-white rounded-xl shadow-sm border p-6">
        <div className="grid md:grid-cols-2 gap-6">

          <div>
            <p className="text-sm text-gray-500">
              Title
            </p>

            <p className="font-medium">
              {data.title}
            </p>
          </div>

          <div>
            <p className="text-sm text-gray-500">
              Status
            </p>

            <p className="font-medium">
              {data.status}
            </p>
          </div>

          <div>
            <p className="text-sm text-gray-500">
              Priority
            </p>

            <p className="font-medium">
              {data.priority}
            </p>
          </div>

          <div>
            <p className="text-sm text-gray-500">
              Target Type
            </p>

            <p className="font-medium">
              {data.target_type}
            </p>
          </div>

          <div>
            <p className="text-sm text-gray-500">
              Start Date
            </p>

            <p className="font-medium">
              {data.start_date}
            </p>
          </div>

          <div>
            <p className="text-sm text-gray-500">
              End Date
            </p>

            <p className="font-medium">
              {data.end_date}
            </p>
          </div>
        </div>

        <hr className="my-6" />

        <div>
          <h3 className="font-semibold mb-3">
            Content
          </h3>

          <div className="whitespace-pre-wrap">
            {data.content}
          </div>
        </div>
      </div>
    </div>
  )
}

export default ViewAnnouncementPage