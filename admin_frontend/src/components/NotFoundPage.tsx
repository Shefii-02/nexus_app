import { useNavigate } from 'react-router-dom'

export default function NotFoundPage() {
  const navigate = useNavigate()

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center px-4 py-8">
      <div className="max-w-md w-full">
        <div className="bg-white rounded-lg shadow-lg overflow-hidden">
          {/* Error Icon */}
          <div className="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-8 text-center">
            <div className="text-6xl mb-4">🔍</div>
            <h1 className="text-3xl font-bold text-white">404</h1>
          </div>

          {/* Error Content */}
          <div className="px-6 py-8 text-center">
            <h2 className="text-2xl font-bold text-gray-800 mb-3">
              Page Not Found
            </h2>
            <p className="text-gray-600 mb-6 leading-relaxed">
              The page you are looking for might have been removed or is temporarily unavailable.
            </p>

            {/* Helpful Links */}
            <div className="bg-blue-50 p-4 rounded-lg mb-6 text-left">
              <h3 className="font-semibold text-blue-900 mb-3">Quick Links:</h3>
              <div className="space-y-2">
                <button
                  onClick={() => navigate('/dashboard')}
                  className="w-full text-left px-3 py-2 text-sm text-blue-700 hover:bg-blue-100 rounded transition"
                >
                  → Dashboard
                </button>
                <button
                  onClick={() => navigate('/students')}
                  className="w-full text-left px-3 py-2 text-sm text-blue-700 hover:bg-blue-100 rounded transition"
                >
                  → Students
                </button>
                <button
                  onClick={() => navigate('/courses')}
                  className="w-full text-left px-3 py-2 text-sm text-blue-700 hover:bg-blue-100 rounded transition"
                >
                  → Courses
                </button>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex flex-col gap-3 sm:flex-row sm:gap-3 sm:justify-center">
              <button
                onClick={() => navigate(-1)}
                className="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition duration-200 inline-flex items-center justify-center gap-2"
              >
                <i className="bi bi-arrow-left"></i>
                Go Back
              </button>
              <button
                onClick={() => navigate('/')}
                className="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center justify-center gap-2"
              >
                <i className="bi bi-house"></i>
                Home
              </button>
            </div>
          </div>

          {/* Footer */}
          <div className="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <p className="text-xs text-gray-500 text-center">
              Error Code: 404 | {new Date().toLocaleString()}
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
