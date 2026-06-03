import PageHeader from '../../../components/PageHeader'
import DynamicForm from '../../../components/DynamicForm'

import { useCreateNotification, useUsers } from '../notificationHooks'

import { notificationFormConfig } from '../notificationFormConfig'

import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

import { useNavigate } from 'react-router-dom'

const CreateNotificationPage = () => {

  const navigate = useNavigate()

  const createNotification =
    useCreateNotification()


  // ✅ FETCH TEACHERS HERE (NOT in config file)
  const { data: userData } = useUsers()

  // ✅ INJECT options dynamically
 const config = notificationFormConfig.map((field) => {
  if (
    field.name === 'user_id' ||
    field.name === 'user_ids'
  ) {
    return {
      ...field,
      options:
        userData?.data?.map((u) => ({
          label: `${u.name} (${u.email})`,
          value: u.id,

          // extra data
          user: u,
        })) || [],
    }
  }

  return field
})


  const submit = async (data: any) => {

    return handleMutationWithToast({

      action: () =>
        createNotification.mutateAsync(
          data
        ),

      loadingMessage:
        'Sending notification...',

      successMessage:
        'Notification sent',

      navigate,

      redirect: '/notifications'
    })
  }

  return (

    <>
      <PageHeader
        title="Create Notification"
      />

      <DynamicForm
        config={config}
        onSubmit={submit}
      />
    </>
  )
}

export default CreateNotificationPage