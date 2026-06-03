import { useEffect, useState } from 'react'
import { useNetworkStatus } from '../hooks/useNetworkStatus'

export default function NoNetworkPage() {
  const { isOnline } = useNetworkStatus()
  const [showOffline, setShowOffline] = useState(!isOnline)
  const [showOnline, setShowOnline] = useState(false)

  useEffect(() => {
    if (!isOnline) {
      setShowOffline(true)
      setShowOnline(false)
    } else {
      // show "back online" message briefly
      if (showOffline) {
        setShowOnline(true)
        setTimeout(() => setShowOnline(false), 2000)
      }
      setShowOffline(false)
    }
  }, [isOnline])

  // ❌ OFFLINE MODAL
  if (showOffline) {
    return (
      <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg shadow-2xl max-w-md w-full overflow-hidden">
          <div className="bg-gradient-to-r from-red-500 to-red-600 px-6 py-8 text-center">
            <div className="text-6xl mb-4">📡</div>
            <h1 className="text-2xl font-bold text-white">No Internet Connection</h1>
          </div>

          <div className="px-6 py-8 text-center">
            <p className="text-gray-600 mb-6 text-sm">
              Please check your connection and try again.
            </p>

            <button
              onClick={() => window.location.reload()}
              className="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg"
            >
              Try Again
            </button>
          </div>
        </div>
      </div>
    )
  }

  // ✅ BACK ONLINE TOAST
  if (showOnline) {
    return (
      <div className="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
        ✅ Back Online
      </div>
    )
  }

  return null
}