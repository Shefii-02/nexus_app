import { useParams } from 'react-router-dom'
import PageHeader from '../../../components/PageHeader'
import { useNotification } from '../notificationHooks'

const ViewNotificationPage = () => {
  const { id } = useParams()

  const { data, isLoading } = useNotification(Number(id))

  if (isLoading) return <div>Loading...</div>

  return (
    <div className="space-y-6">
      <PageHeader title="Notification Details" />

      <div className="bg-white rounded-xl shadow-sm p-6">

        <div className="grid md:grid-cols-2 gap-6">

          <div>
            <label className="text-gray-500 text-sm">
              Title
            </label>

            <div className="font-medium mt-1">
              {data?.title}
            </div>
          </div>

          <div>
            <label className="text-gray-500 text-sm">
              Type
            </label>

            <div className="mt-1">
              {data?.type}
            </div>
          </div>

          <div className="md:col-span-2">
            <label className="text-gray-500 text-sm">
              Message
            </label>

            <div className="mt-1 whitespace-pre-wrap">
              {data?.message}
            </div>
          </div>

          <div>
            <label className="text-gray-500 text-sm">
              Priority
            </label>

            <div className="mt-1">
              {data?.priority}
            </div>
          </div>

          <div>
            <label className="text-gray-500 text-sm">
              User
            </label>

            <div className="mt-1">
              {data?.user?.name}
            </div>
          </div>

          <div>
            <label className="text-gray-500 text-sm">
              Read At
            </label>

            <div className="mt-1">
              {data?.read_at || 'Unread'}
            </div>
          </div>

          <div>
            <label className="text-gray-500 text-sm">
              Created
            </label>

            <div className="mt-1">
              {data?.created_at}
            </div>
          </div>

        </div>
      </div>
    </div>
  )
}

export default ViewNotificationPage