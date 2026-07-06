import { useEffect, useState } from 'react'

export default function UpdateToast() {
  const [needsRefresh, setNeedsRefresh] = useState(false)
  const [offlineReady, setOfflineReady] = useState(false)

  useEffect(() => {
    const onNeedRefresh = () => setNeedsRefresh(true)
    const onOfflineReady = () => setOfflineReady(true)
    window.addEventListener('pwa-need-refresh', onNeedRefresh)
    window.addEventListener('pwa-offline-ready', onOfflineReady)
    return () => {
      window.removeEventListener('pwa-need-refresh', onNeedRefresh)
      window.removeEventListener('pwa-offline-ready', onOfflineReady)
    }
  }, [])

  if (!needsRefresh && !offlineReady) return null

  return (
    <div className="fixed top-4 left-1/2 -translate-x-1/2 z-50 rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-200 shadow-lg">
      {needsRefresh ? (
        <span>
          New version available.{' '}
          <button
            onClick={() => window.location.reload()}
            className="font-medium text-indigo-400 hover:text-indigo-300"
          >
            Reload
          </button>
        </span>
      ) : (
        'App ready to work offline'
      )}
    </div>
  )
}