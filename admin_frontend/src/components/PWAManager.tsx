// src/components/PWAManager.tsx
import React, { useEffect } from 'react'
import { useDispatch } from 'react-redux'
// import { useRegisterSW } from 'virtual:pwa-register/react'
// import { setOnlineStatus } from '@/store/networkSlice'

export const PWAManager: React.FC = () => {
//   const dispatch = useDispatch()

  // Registers the SW. 'autoUpdate' handles the actual refreshing behind the scenes.
//   useRegisterSW({
//     onRegisteredSW(swScriptUrl, registration) {
//       console.log('Service Worker registered:', swScriptUrl)
//     },
//     onRegisterError(error) {
//       console.error('Service Worker failed:', error)
//     },
//   })

//   useEffect(() => {
//     const handleOnline = () => dispatch(setOnlineStatus(true))
//     const handleOffline = () => dispatch(setOnlineStatus(false))

//     window.addEventListener('online', handleOnline)
//     window.addEventListener('offline', handleOffline)

//     return () => {
//       window.removeEventListener('online', handleOnline)
//       window.removeEventListener('offline', handleOffline)
//     }
//   }, [dispatch])

  return null // This manager runs silently in the background
}
