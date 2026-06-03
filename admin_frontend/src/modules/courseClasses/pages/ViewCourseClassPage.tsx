import { useParams } from 'react-router-dom'
import { useCourseClass } from '../courseClassHooks'
import PageHeader from '../../../components/PageHeader'

const ViewCourseClassPage = () => {
 const { courseId, classId } = useParams()

  const { data } = useCourseClass(Number(classId), Number(courseId))

  const c = data

  if (!c) return <div>Loading...</div>

  return (
    <div>
      <PageHeader title="View Course Class" />

      <div className="bg-white p-6 rounded-xl border space-y-3">
        <p><b>Title:</b> {c.title}</p>
        <p><b>Class Number:</b> {c.class_number}</p>
        <p><b>Scheduled Date:</b> {c.scheduled_date}</p>
        <p><b>Duration:</b> {c.duration_minutes} min</p>
        <p><b>Status:</b> {c.status}</p>

        <p>
          <b>Class Link:</b>{' '}
          <a href={c.class_link} target="_blank" className="text-blue-500">
            Open
          </a>
        </p>

        <p>
          <b>Record Link:</b>{' '}
          <a href={c.record_link} target="_blank" className="text-blue-500">
            Open
          </a>
        </p>
      </div>
    </div>
  )
}

export default ViewCourseClassPage