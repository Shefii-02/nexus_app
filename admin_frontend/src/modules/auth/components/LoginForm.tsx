interface LoginFormProps {
  email: string
  password: string
  loading: boolean
  error?: string
  onEmailChange: (value: string) => void
  onPasswordChange: (value: string) => void
  onSubmit: () => void
}

const LoginForm = ({
  email,
  password,
  loading,
  error,
  onEmailChange,
  onPasswordChange,
  onSubmit,
}: LoginFormProps) => {
  return (
    <div className="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-200/40 sm:p-10">
      <h1 className="mb-4 text-3xl font-semibold text-slate-900">Staff & Admin Login</h1>
      <p className="mb-6 text-sm text-slate-500">Sign in to manage teachers, students, courses and more.</p>
      {error ? <div className="mb-4 rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-700">{error}</div> : null}
      <div className="space-y-5">
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-700">Email</span>
          <input
            value={email}
            onChange={(event) => onEmailChange(event.target.value)}
            type="email"
            placeholder="admin@example.com"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-700">Password</span>
          <input
            value={password}
            onChange={(event) => onPasswordChange(event.target.value)}
            type="password"
            placeholder="Enter your password"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white"
          />
        </label>
        <button
          type="button"
          onClick={onSubmit}
          disabled={loading}
          className="mt-2 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
        >
          {loading ? 'Signing in...' : 'Sign in'}
        </button>
      </div>
    </div>
  )
}

export default LoginForm
