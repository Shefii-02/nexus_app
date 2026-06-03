import React from 'react'
import { useNavigate } from 'react-router-dom'
import { toastService } from '../services/toastService'

interface Props {
  children: React.ReactNode
}

interface State {
  hasError: boolean
  error: Error | null
}

export default class ErrorBoundary extends React.Component<Props, State> {
  constructor(props: Props) {
    super(props)
    this.state = { hasError: false, error: null }
  }

  static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error }
  }

  componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
    console.error('Error caught by boundary:', error, errorInfo)
    
    // Log error to external service if needed
    if (import.meta.env.PROD) {
      // Send to error tracking service (e.g., Sentry)
    }
  }

  render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center px-4 py-8">
          <div className="max-w-md w-full">
            <div className="bg-white rounded-lg shadow-lg overflow-hidden">
              {/* Error Icon */}
              <div className="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-8 text-center">
                <div className="text-6xl mb-4">⚠️</div>
                <h1 className="text-3xl font-bold text-white">Something went wrong</h1>
              </div>

              {/* Error Content */}
              <div className="px-6 py-8 text-center">
                <p className="text-gray-600 mb-4 leading-relaxed">
                  We encountered an unexpected error. Our team has been notified.
                </p>

                {/* Error Details (Dev Only) */}
                {import.meta.env.DEV && (
                  <div className="mb-6 p-4 bg-gray-100 rounded-lg text-left overflow-auto max-h-40">
                    <p className="text-xs font-mono text-gray-700 break-words">
                      {this.state.error?.message}
                    </p>
                  </div>
                )}

                {/* Action Buttons */}
                <div className="flex flex-col gap-3">
                  <button
                    onClick={() => {
                      this.setState({ hasError: false, error: null })
                      toastService.info('Please try your action again')
                    }}
                    className="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition duration-200"
                  >
                    Try Again
                  </button>
                  <button
                    onClick={() => {
                      window.location.href = '/'
                    }}
                    className="px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-semibold rounded-lg transition duration-200"
                  >
                    Go to Home
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )
    }

    return this.props.children
  }
}
