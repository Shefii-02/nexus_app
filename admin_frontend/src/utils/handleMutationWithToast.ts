import { toast } from 'react-toastify'

interface Props<T = any> {
  action: () => Promise<T>

  loadingMessage?: string
  successMessage?: string
  errorMessage?: string

  redirect?: string
  navigate?: (path: string) => void

  onSuccess?: (data: T) => void
  onError?: (error: any) => void
}

export const handleMutationWithToast = async <T>({
  action,
  loadingMessage = 'Processing...',
  successMessage = 'Success',
  errorMessage = 'Something went wrong',
  redirect,
  navigate,
  onSuccess,
  onError,
}: Props<T>) => {
  const toastId = toast.loading(loadingMessage)

  try {
    const result = await action()

    toast.update(toastId, {
      render: successMessage,
      type: 'success',
      isLoading: false,
      autoClose: 2000,
    })

    onSuccess?.(result)

    if (redirect && navigate) {
      setTimeout(() => {
        navigate(redirect)
      }, 2000)
    }

    return result
  } catch (err: any) {
    toast.update(toastId, {
      render: err?.response?.data?.message || errorMessage,
      type: 'error',
      isLoading: false,
      autoClose: 3000,
    })

    onError?.(err)

    throw err
  }
}