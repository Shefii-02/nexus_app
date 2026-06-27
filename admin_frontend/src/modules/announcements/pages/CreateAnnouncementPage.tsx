import { useNavigate } from 'react-router-dom'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { useCreateAnnouncement } from '../announcementHooks'
import { announcementFormConfig } from '../announcementFormConfig'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const CreateAnnouncementPage = () => {
  const navigate = useNavigate()

  const createAnnouncement = useCreateAnnouncement()

  const handleSubmit = async (data: any) => {
    const formData = new FormData()

    Object.keys(data).forEach((key) => {
      formData.append(key, data[key])
    })
    return handleMutationWithToast({
      action: () => createAnnouncement.mutateAsync(formData),
      loadingMessage: 'Creating announcement...',
      successMessage: 'Announcement created successfully',
      navigate,
      redirect: '/announcements',
    })
  }

  return (
    <div>
      <PageHeader title="Create Announcement" />

      <div className="pt-4">
        <DynamicForm
          config={announcementFormConfig}
          defaultValues={{
            priority: 'normal',
            status: 'active',
          }}
          onSubmit={handleSubmit}
        />
      </div>
    </div>
  )
}

export default CreateAnnouncementPage