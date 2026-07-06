import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import LoginForm from '../components/LoginForm'
import { login } from '../authSlice'
import { useAppDispatch, useAppSelector } from '../../../store/hooks'

const LoginPage = () => {
  const dispatch = useAppDispatch()
  const navigate = useNavigate()
  const auth = useAppSelector((state) => state.auth)
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [formError, setFormError] = useState<string | null>(null)

  useEffect(() => {
    if (auth.token) {
      navigate('/')
    }
  }, [auth.token, navigate])

  const handleSubmit = async () => {
    if (!email || !password) {
      setFormError('Email and password are required.')
      return
    }

    setFormError(null)
    try {
      await dispatch(login({ email, password })).unwrap()
      navigate('/')
    } catch (error: any) {
      setFormError(error || 'Unable to login. Please verify your credentials.')
    }
  }

  return (
    <div className="min-h-screen bg-slate-50 px-4 py-12 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-5xl">
        <div className="grid gap-12 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
          <div className="rounded-3xl bg-gradient-to-br from-slate-900 to-slate-700 p-10 text-white shadow-2xl shadow-slate-900/20">
            <p className="mb-3 text-sm uppercase tracking-[0.24em] text-slate-300">Welcome back</p>
            <h2 className="text-4xl font-semibold leading-tight text-white">Nexus Learning Platform Admin</h2>
            <p className="mt-4 max-w-md text-sm text-slate-200">
              {/* Secure staff and administrator access to manage users, courses, payments, notifications and announcements. */}
            </p>
            <div className="mt-10 grid gap-4 text-sm text-slate-300">
              {/* <div className="rounded-3xl bg-slate-800/70 p-4">
                <p className="font-semibold">Role-based access</p>
                <p className="mt-1 text-slate-300">Admin gets full access; Staff sees a reduced action set.</p>
              </div>
              <div className="rounded-3xl bg-slate-800/70 p-4">
                <p className="font-semibold">JWT authentication</p>
                <p className="mt-1 text-slate-300">Your session is handled securely through the backend API.</p>
              </div> */}
            </div>
          </div>
          <LoginForm
            email={email}
            password={password}
            loading={auth.status === 'loading'}
            error={formError || auth.error || undefined}
            onEmailChange={setEmail}
            onPasswordChange={setPassword}
            onSubmit={handleSubmit}
          />
        </div>
      </div>
    </div>
  )
}

export default LoginPage
