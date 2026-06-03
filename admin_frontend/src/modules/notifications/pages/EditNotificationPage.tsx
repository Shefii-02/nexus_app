import { useNavigate, useParams } from 'react-router-dom'
import PageHeader from '../../../components/PageHeader'
import DynamicForm from '../../../components/DynamicForm'

import { notificationFormConfig } from '../notificationFormConfig'

import {
  useNotification,
  useUpdateNotification,
} from '../notificationHooks'

import {
  handleMutationWithToast,
} from '../../../utils/handleMutationWithToast'

const EditNotificationPage = () => {
  const navigate = useNavigate()

  const { id } = useParams()

  const { data } = useNotification(Number(id))

  const updateNotification =
    useUpdateNotification()

  const handleSubmit = async (
    formData: any
  ) => {
    return handleMutationWithToast({
      action: () =>
        updateNotification.mutateAsync({
          id: Number(id),
          data: formData,
        }),

      loadingMessage:
        'Updating Notification...',

      successMessage:
        'Notification Updated',

      navigate,

      redirect: '/notifications',
    })
  }

  const defaultValues = data
    ? {
        user_id: data.user_id,
        type: data.type,
        title: data.title,
        message: data.message,
        priority: data.priority,
      }
    : {}

  return (
    <div>
      <PageHeader
        title="Edit Notification"
      />

      <div className="pt-4">
        <DynamicForm
          config={notificationFormConfig}
          defaultValues={defaultValues}
          onSubmit={handleSubmit}
          isEdit
        />
      </div>
    </div>
  )
}

export default EditNotificationPage