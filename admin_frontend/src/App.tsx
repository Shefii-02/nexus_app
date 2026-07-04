import { useEffect } from 'react'
import { BrowserRouter } from 'react-router-dom'
import { Provider } from 'react-redux'
import { QueryClientProvider } from '@tanstack/react-query'
import { ToastContainer } from 'react-toastify'
import AppRoutes from './routes/AppRoutes'
import { queryClient } from './services/queryClient'
import { store } from './store'
import { useAppDispatch, useAppSelector } from './store/hooks'
import { fetchProfile, logout } from './modules/auth/authSlice'
import NoNetworkPage from './components/NoNetworkPage'
import ErrorBoundary from './components/ErrorBoundary'
import { OfflineBanner } from './components/OfflineBanner'
import { PWAManager } from './components/PWAManager'

const AppContent = () => {
  const dispatch = useAppDispatch()
  const token = useAppSelector((state) => state.auth.token)

  useEffect(() => {
    if (token) {
      dispatch(fetchProfile())
    }
  }, [dispatch, token])

  // 🔥 ADD THIS BLOCK
  useEffect(() => {
    const handler = () => {
      dispatch(logout())
    }

    window.addEventListener('unauthorized', handler)

    return () => {
      window.removeEventListener('unauthorized', handler)
    }
  }, [dispatch])

  return (
    <>
      <BrowserRouter>
        <AppRoutes />
        <PWAManager />
      </BrowserRouter>
      <NoNetworkPage />
      <ToastContainer
        position="top-right"
        autoClose={3000}
        hideProgressBar={false}
        newestOnTop={true}
        closeOnClick
        rtl={false}
        pauseOnFocusLoss
        draggable
        pauseOnHover
      />
    </>
  )
}

const App = () => (
  <ErrorBoundary>
    <Provider store={store}>
      <QueryClientProvider client={queryClient}>
        <AppContent />
      </QueryClientProvider>
    </Provider>
  </ErrorBoundary>
)

export default App
