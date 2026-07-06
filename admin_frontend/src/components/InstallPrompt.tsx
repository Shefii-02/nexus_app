import { useState, useEffect } from 'react'
import { usePWAInstall } from '../hooks/usePWAInstall'

export default function InstallPrompt() {
  const { isInstallable, isInstalled, promptInstall } = usePWAInstall()
  const [dismissed, setDismissed] = useState(false)

  useEffect(() => {
    setDismissed(sessionStorage.getItem('pwa-install-dismissed') === '1')
  }, [])

  if (!isInstallable || isInstalled || dismissed) return null

  const handleDismiss = () => {
    sessionStorage.setItem('pwa-install-dismissed', '1')
    setDismissed(true)
  }

  return (
    <div className="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 shadow-lg">
      <span className="text-sm text-slate-200">Install Nexus Admin for quick access</span>
      <button
        onClick={promptInstall}
        className="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-500"
      >
        Install
      </button>
      <button
        onClick={handleDismiss}
        className="text-sm text-slate-400 hover:text-slate-200"
      >
        Dismiss
      </button>
    </div>
  )
}