import { useCallback } from 'react'
import { toastService } from '@/services/toastService'
import { getErrorMessage } from '@/services/apiClient'

export const useToast = () => {
  const showSuccess = useCallback((message: string) => {
    toastService.success(message)
  }, [])

  const showError = useCallback((error: unknown) => {
    const message = getErrorMessage(error)
    toastService.error(message)
  }, [])

  const showInfo = useCallback((message: string) => {
    toastService.info(message)
  }, [])

  const showWarning = useCallback((message: string) => {
    toastService.warning(message)
  }, [])

  const showLoading = useCallback((message: string) => {
    return toastService.loading(message)
  }, [])

  const updateLoading = useCallback((toastId: string | number, success: boolean, message?: string) => {
    toastService.update(toastId, {
      render: message || (success ? 'Done!' : 'Failed!'),
      type: success ? 'success' : 'error',
      isLoading: false,
      autoClose: 3000,
    })
  }, [])

  return {
    success: showSuccess,
    error: showError,
    info: showInfo,
    warning: showWarning,
    loading: showLoading,
    updateLoading,
    dismiss: toastService.dismiss,
    clearAll: toastService.clearAll,
  }
}

export default useToast
