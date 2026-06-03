const ViewToggle = ({ view, setView }: any) => {
  return (
    <div className="flex gap-2">
      <button
        onClick={() => setView('grid')}
        className={view === 'grid' ? 'font-bold' : ''}
      >
        Grid
      </button>

      <button
        onClick={() => setView('list')}
        className={view === 'list' ? 'font-bold' : ''}
      >
        List
      </button>
    </div>
  )
}

export default ViewToggle