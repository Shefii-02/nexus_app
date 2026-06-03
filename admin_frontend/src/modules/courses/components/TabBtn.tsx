const TabBtn = ({ label, active, onClick }: any) => (
  <button
    onClick={onClick}
    className={`pb-2 ${
      active
        ? 'border-b-2 border-black font-semibold'
        : 'text-gray-400'
    }`}
  >
    {label}
  </button>
)

export default TabBtn;