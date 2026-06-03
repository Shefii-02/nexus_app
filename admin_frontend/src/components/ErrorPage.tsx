import { useNavigate } from 'react-router-dom'

interface ErrorPageProps {
  statusCode?: number
  title?: string
  message?: string
  showBackButton?: boolean
}

export default function ErrorPage({
  statusCode = 500,
  title = 'Oops! Something went wrong',
  message = 'An unexpected error occurred. Please try again later.',
  showBackButton = true,
}: ErrorPageProps) {
  const navigate = useNavigate()

  const getErrorDetails = (code: number) => {
    switch (code) {
      case 400:
        return {
          title: 'Bad Request',
          message: 'The request was invalid. Please check your input and try again.',
          icon: '⚠️',
        }
      case 401:
        return {
          title: 'Unauthorized',
          message: 'You need to log in to access this resource.',
          icon: '🔒',
        }
      case 403:
        return {
          title: 'Access Denied',
          message: 'You do not have permission to access this resource.',
          icon: '🚫',
        }
      case 404:
        return {
          title: 'Page Not Found',
          message: 'The page you are looking for does not exist.',
          icon: '🔍',
        }
      case 422:
        return {
          title: 'Validation Error',
          message: 'Please check your input and try again.',
          icon: '✓',
        }
      case 429:
        return {
          title: 'Too Many Requests',
          message: 'You have made too many requests. Please try again later.',
          icon: '⏱️',
        }
      case 500:
        return {
          title: 'Server Error',
          message: 'The server encountered an error. Please try again later.',
          icon: '💥',
        }
      case 503:
        return {
          title: 'Service Unavailable',
          message: 'The service is temporarily unavailable. Please try again later.',
          icon: '⚙️',
        }
      default:
        return {
          title,
          message,
          icon: '❌',
        }
    }
  }

  const errorDetails = getErrorDetails(statusCode)

  return (
    <div className="min-h-screen bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center px-4 py-8">
      <div className="max-w-md w-full">
        <div className="bg-white rounded-lg shadow-lg overflow-hidden">
          {/* Error Icon */}
          <div className="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-8 text-center">
            <div className="text-6xl mb-4">{errorDetails.icon}</div>
            <h1 className="text-3xl font-bold text-white">{statusCode}</h1>
          </div>

          {/* Error Content */}
          <div className="px-6 py-8 text-center">
            <h2 className="text-2xl font-bold text-gray-800 mb-3">
              {errorDetails.title}
            </h2>
            <p className="text-gray-600 mb-6 leading-relaxed">
              {errorDetails.message}
            </p>

            {/* Action Buttons */}
            <div className="flex flex-col gap-3 sm:flex-row sm:gap-3 sm:justify-center">
              {showBackButton && (
                <button
                  onClick={() => navigate(-1)}
                  className="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition duration-200 inline-flex items-center justify-center gap-2"
                >
                  <i className="bi bi-arrow-left"></i>
                  Go Back
                </button>
              )}
              <button
                onClick={() => navigate('/')}
                className="flex-1 px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center justify-center gap-2"
              >
                <i className="bi bi-house"></i>
                Home
              </button>
            </div>
          </div>

          {/* Footer */}
          <div className="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <p className="text-xs text-gray-500 text-center">
              Error Code: {statusCode} | {new Date().toLocaleString()}
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
