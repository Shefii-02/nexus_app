const Dropdown = ({ open, children }: any) => {
  if (!open) return null

  return (
    <div className="absolute right-0 mt-2 bg-white shadow-lg rounded-xl p-3 z-50">
      {children}
    </div>
  )
}

export default Dropdown