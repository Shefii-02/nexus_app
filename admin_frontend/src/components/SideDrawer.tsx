import { X } from 'lucide-react'

interface SideDrawerProps {
open: boolean
title: string
width?: string
onClose: () => void
children: React.ReactNode
}

const SideDrawer = ({
open,
title,
width = 'w-[550px]',
onClose,
children,
}: SideDrawerProps) => {
return (
<>
<div
className={`           fixed inset-0 bg-black/40 z-40 transition-opacity
          ${open ? 'opacity-100 visible' : 'opacity-0 invisible'}
        `}
onClick={onClose}
/>


  <div
    className={`
      fixed top-0 right-0 h-screen bg-white z-50 shadow-xl
      transition-transform duration-300
      ${width}
      ${open ? 'translate-x-0' : 'translate-x-full'}
    `}
  >
    <div className="flex items-center justify-between border-b p-4">
      <h2 className="font-semibold text-lg">
        {title}
      </h2>

      <button onClick={onClose}>
        <X size={20} />
      </button>
    </div>

    <div className="overflow-y-auto h-[calc(100vh-70px)] p-4">
      {children}
    </div>
  </div>
</>


)
}

export default SideDrawer
