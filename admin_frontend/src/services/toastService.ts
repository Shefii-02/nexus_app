import { toast, type ToastOptions } from 'react-toastify'

const defaultOptions: ToastOptions = {
  position: 'top-right',
  autoClose: 3000,
  hideProgressBar: false,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
}

export const toastService = {
  /**
   * Show success toast
   */
  success: (message: string, options?: ToastOptions) => {
    toast.success(message, {
      ...defaultOptions,
      ...options,
    })
  },

  /**
   * Show error toast
   */
  error: (message: string, options?: ToastOptions) => {
    toast.error(message, {
      ...defaultOptions,
      autoClose: 5000,
      ...options,
    })
  },

  /**
   * Show info toast
   */
  info: (message: string, options?: ToastOptions) => {
    toast.info(message, {
      ...defaultOptions,
      ...options,
    })
  },

  /**
   * Show warning toast
   */
  warning: (message: string, options?: ToastOptions) => {
    toast.warning(message, {
      ...defaultOptions,
      ...options,
    })
  },

  /**
   * Show loading toast (for long operations)
   */
  loading: (message: string, options?: ToastOptions) => {
    return toast.loading(message, {
      ...defaultOptions,
      autoClose: false,
      closeOnClick: false,
      ...options,
    })
  },

  /**
   * Update a loading toast
   */
  update: (toastId: string | number, options: ToastOptions) => {
    toast.update(toastId, options)
  },

  /**
   * Dismiss a specific toast
   */
  dismiss: (toastId?: string | number) => {
    toast.dismiss(toastId)
  },

  /**
   * Clear all toasts
   */
  clearAll: () => {
    toast.dismiss()
  },
}

export default toastService
