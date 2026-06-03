type Variant = 'primary' | 'secondary' | 'danger' | 'outline'

interface ButtonProps {
  children: React.ReactNode
  onClick?: () => void
  type?: 'button' | 'submit'
  variant?: Variant
  loading?: boolean
  className?: string
}

const Button = ({
  children,
  onClick,
  type = 'button',
  variant = 'primary',
  loading = false,
  className = '',
}: ButtonProps) => {
  const base = 'px-4 py-2 rounded-xl text-sm font-medium transition'

  const styles = {
    primary: 'bg-black text-white hover:bg-gray-800',
    secondary: 'bg-slate-100 text-slate-700 hover:bg-slate-200',
    danger: 'bg-red-100 text-red-600 hover:bg-red-200',
    outline: 'border border-slate-300 text-slate-700 hover:bg-slate-100',
  }

  return (
    <button
      type={type}
      onClick={onClick}
      disabled={loading}
      className={`${base} ${styles[variant]} ${className}`}
    >
      {loading ? 'Please wait...' : children}
    </button>
  )
}

export default Button