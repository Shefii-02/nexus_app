import { useNavigate, useParams } from 'react-router-dom'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

import {
  useAnnouncement,
  useUpdateAnnouncement,
} from '../announcementHooks'

import { announcementFormConfig } from '../announcementFormConfig'

const EditAnnouncementPage = () => {
  const navigate = useNavigate()

  const { id } = useParams()

  const { data, isLoading } = useAnnouncement(Number(id))

  const updateAnnouncement = useUpdateAnnouncement()

  const handleSubmit = async (formData: any) => {
    return handleMutationWithToast({
      action: () =>
        updateAnnouncement.mutateAsync({
          id: Number(id),
          data: formData,
        }),

      loadingMessage: 'Updating announcement...',
      successMessage: 'Announcement updated successfully',
      navigate,
      redirect: '/announcements',
    })
  }

  if (isLoading) {
    return <div>Loading...</div>
  }

  const defaultValues = data
    ? {
        ...data,

        start_date: data.start_date
          ? data.start_date.split(' ')[0]
          : '',

        end_date: data.end_date
          ? data.end_date.split(' ')[0]
          : '',

        target_type: String(
          data.target_type ?? ''
        ),

        priority: String(
          data.priority ?? ''
        ),

        status: String(
          data.status ?? ''
        ),
      }
    : {}

  return (
    <div>
      <PageHeader title="Edit Announcement" />

      <div className="pt-4">
        <DynamicForm
          config={announcementFormConfig}
          defaultValues={defaultValues}
          onSubmit={handleSubmit}
          isEdit
        />
      </div>
    </div>
  )
}

export default EditAnnouncementPage