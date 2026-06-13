import Button from '../../../components/Button'

interface Props {
  view: 'grid' | 'list'
  setView: (view: 'grid' | 'list') => void
}

const CourseViewToggle = ({ view, setView }: Props) => {
  return (<div className="flex gap-2">
    <Button
      variant={view === 'grid' ? 'primary' : 'secondary'}
      onClick={() => setView('grid')}
    >
      Grid </Button>

    <Button
      variant={view === 'list' ? 'primary' : 'secondary'}
      onClick={() => setView('list')}
    >
      List
    </Button>
  </div>
  )
}

export default CourseViewToggle
