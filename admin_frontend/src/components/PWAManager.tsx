// import { useEffect, useState, useCallback } from 'react'

// interface BeforeInstallPromptEvent extends Event {
//   prompt: () => Promise<void>
//   userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>
// }

export const PWAManager = () => {
  // const [deferredPrompt, setDeferredPrompt] = useState<BeforeInstallPromptEvent | null>(null)
  // const [isInstallable, setIsInstallable] = useState(false)
  // const [isInstalled, setIsInstalled] = useState(false)
  // const [dismissed, setDismissed] = useState(false)
  // const [needsRefresh, setNeedsRefresh] = useState(false)
  // const [offlineReady, setOfflineReady] = useState(false)

  // useEffect(() => {
    // const isStandalone =
    //   window.matchMedia('(display-mode: standalone)').matches ||
    //   (window.navigator as any).standalone === true
    // setIsInstalled(isStandalone)
    // setDismissed(sessionStorage.getItem('pwa-install-dismissed') === '1')

    // const handleBeforeInstallPrompt = (e: Event) => {
    //   e.preventDefault()
    //   setDeferredPrompt(e as BeforeInstallPromptEvent)
    //   setIsInstallable(true)
    // }
    // const handleAppInstalled = () => {
    //   setIsInstalled(true)
    //   setIsInstallable(false)
    //   setDeferredPrompt(null)
    // }
    // const onNeedRefresh = () => setNeedsRefresh(true)
    // const onOfflineReady = () => setOfflineReady(true)

  //   window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
  //   window.addEventListener('appinstalled', handleAppInstalled)
  //   window.addEventListener('pwa-need-refresh', onNeedRefresh)
  //   window.addEventListener('pwa-offline-ready', onOfflineReady)

  //   return () => {
  //     window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
  //     window.removeEventListener('appinstalled', handleAppInstalled)
  //     window.removeEventListener('pwa-need-refresh', onNeedRefresh)
  //     window.removeEventListener('pwa-offline-ready', onOfflineReady)
  //   }
  // }, [])

  // const promptInstall = useCallback(async () => {
  //   if (!deferredPrompt) return
  //   await deferredPrompt.prompt()
  //   await deferredPrompt.userChoice
  //   setDeferredPrompt(null)
  //   setIsInstallable(false)
  // }, [deferredPrompt])

  // const dismissInstall = () => {
  //   sessionStorage.setItem('pwa-install-dismissed', '1')
  //   setDismissed(true)
  // }

  return (
    <>
      {/* {isInstallable && !isInstalled && !dismissed && (
        <div className="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 shadow-lg">
          <span className="text-sm text-slate-200">Install Nexus Admin for quick access</span>
          <button
            onClick={promptInstall}
            className="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-500"
          >
            Install
          </button>
          <button onClick={dismissInstall} className="text-sm text-slate-400 hover:text-slate-200">
            Dismiss
          </button>
        </div>
      )}

      {(needsRefresh || offlineReady) && (
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
      )} */}
    </>
  )
}