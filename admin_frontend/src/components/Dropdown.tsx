import { useEffect, useRef, useState } from 'react'
import { createPortal } from 'react-dom'

export interface DropdownItem {
  label: string
  onClick: () => void
  className?: string
  danger?: boolean
}

interface DropdownProps {
  trigger: React.ReactNode
  items: DropdownItem[]
}

const Dropdown = ({ trigger, items }: DropdownProps) => {
  const [open, setOpen] = useState(false)
  const [position, setPosition] = useState({ top: 0, left: 0 })
  const triggerRef = useRef<HTMLDivElement | null>(null)

  const toggle = () => {
    if (!triggerRef.current) return

    const rect = triggerRef.current.getBoundingClientRect()

    setPosition({
      top: rect.bottom + window.scrollY,
      left: rect.right - 160, // dropdown width offset
    })

    setOpen((prev) => !prev)
  }

  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      setOpen(false)
    }

    if (open) {
      document.addEventListener('click', handleClickOutside)
    }

    return () => document.removeEventListener('click', handleClickOutside)
  }, [open])

  return (
    <>
      {/* Trigger */}
      <div ref={triggerRef as any} onClick={(e) => {
        e.stopPropagation()
        toggle()
      }} className="inline-block cursor-pointer">
        {trigger}
      </div>

      {/* Dropdown Portal */}
      {open &&
        createPortal(
          <div
            style={{
              position: 'absolute',
              top: position.top,
              left: position.left,
              zIndex: 9999,
            }}
            className="w-40 bg-white border rounded-xl shadow-lg overflow-hidden"
            onClick={(e) => e.stopPropagation()}
          >
            {items.map((item, index) => (
              <button
                key={index}
                onClick={() => {
                  setOpen(false)
                  item.onClick()
                }}
                className={`w-full text-left px-4 py-2 text-sm hover:bg-gray-100 ${
                  item.danger ? 'text-red-600' : 'text-gray-700'
                } ${item.className || ''}`}
              >
                {item.label}
              </button>
            ))}
          </div>,
          document.body,
        )}
    </>
  )
}

export default Dropdown