import { useState, useRef, useEffect } from 'react'

interface ActionItem {
  label: string
  onClick: () => void
  danger?: boolean
}

interface Props {
  actions: ActionItem[]
}

const ActionDropdown = ({ actions }: Props) => {
  const [open, setOpen] = useState(false)
  const ref = useRef<HTMLDivElement | null>(null)

  useEffect(() => {
    const handleClick = (e: any) => {
      if (!ref.current?.contains(e.target)) {
        setOpen(false)
      }
    }
    document.addEventListener('click', handleClick)
    return () => document.removeEventListener('click', handleClick)
  }, [])

  return (
    <div className="relative" ref={ref}>
      <button
        onClick={() => setOpen((p) => !p)}
        className="p-1 rounded hover:bg-gray-100"
      >
        ⋮
      </button>

      {open && (
        <div className="absolute right-0 mt-2 w-44 bg-white border rounded-xl shadow-lg z-50">
          {actions.map((action, i) => (
            <button
              key={i}
              onClick={() => {
                action.onClick()
                setOpen(false)
              }}
              className={`w-full text-left px-3 py-2 text-sm hover:bg-gray-100 ${
                action.danger ? 'text-red-500' : ''
              }`}
            >
              {action.label}
            </button>
          ))}
        </div>
      )}
    </div>
  )
}

export default ActionDropdown