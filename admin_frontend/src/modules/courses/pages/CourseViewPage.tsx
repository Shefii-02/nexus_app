import { useParams, useNavigate } from 'react-router-dom'
import { useCourse } from '../courseHooks'
import { useState } from 'react'
import InfoCard from '../components/InfoCard'
import TabBtn from '../components/TabBtn'

const CourseViewPage = () => {
  const { id } = useParams()
  const navigate = useNavigate()
  const { data: course, isLoading } = useCourse(Number(id))

  const [tab, setTab] = useState<'overview' | 'classes' | 'materials'>('overview')

  if (isLoading) return <div className="p-6">Loading...</div>
  if (!course) return <div className="p-6">Course not found</div>

 

  return (
    <div className="p-6 space-y-6">

      {/* =========================
       * HERO SECTION
       ========================= */}
      <div className="relative h-60 rounded-2xl overflow-hidden">
        <img
          src={course.thumbnail}
          className="w-full h-full object-cover"
        />

        <div className="absolute inset-0 bg-black/50" />

        <div className="absolute bottom-4 left-6 text-white">
          <h1 className="text-2xl font-semibold">{course.name}</h1>
          <p className="text-sm opacity-80">{course.code}</p>
        </div>

        <div className="absolute top-4 right-4 flex gap-2">
          <button
            onClick={() => navigate(`/courses/${course.id}/edit`)}
            className="px-3 py-1 bg-white text-black rounded"
          >
            Edit
          </button>
        </div>
      </div>

      {/* =========================
       * INFO CARDS
       ========================= */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <InfoCard label="Price" value={`₹${course.net_price}`} />
        <InfoCard label="Duration" value={`${course.duration_days} days`} />
        <InfoCard label="Type" value={course.class_type} />
        <InfoCard label="Status" value={course.status} />
      </div>

      {/* =========================
       * TABS
       ========================= */}
      <div className="flex gap-4 border-b">
        <TabBtn label="Overview" active={tab === 'overview'} onClick={() => setTab('overview')} />
        <TabBtn label="Classes" active={tab === 'classes'} onClick={() => setTab('classes')} />
        <TabBtn label="Materials" active={tab === 'materials'} onClick={() => setTab('materials')} />
      </div>

      {/* =========================
       * TAB CONTENT
       ========================= */}
      <div className="bg-white p-5 rounded-2xl shadow-sm">

        {tab === 'overview' && (
          <div className="space-y-3">
            <p className="text-gray-700">{course.description || 'No description'}</p>

            <div className="grid md:grid-cols-2 gap-4 text-sm">
              <p><b>Start:</b> {course.started_at}</p>
              <p><b>End:</b> {course.ended_at}</p>
              <p><b>Teacher:</b> {course.teacher?.name}</p>
              <p><b>Fee Type:</b> {course.fee_type}</p>
            </div>
          </div>
        )}

        {tab === 'classes' && (
          <div>
            <button
              onClick={() => navigate(`/courses/${course.id}/classes`)}
              className="mb-3 px-3 py-1 bg-black text-white rounded"
            >
              Manage Classes
            </button>

            <p className="text-gray-500">Classes preview here...</p>
          </div>
        )}

        {tab === 'materials' && (
          <div>
            <button
              onClick={() => navigate(`/courses/${course.id}/materials`)}
              className="mb-3 px-3 py-1 bg-black text-white rounded"
            >
              Manage Materials
            </button>

            <p className="text-gray-500">Materials preview here...</p>
          </div>
        )}
      </div>
    </div>
  )
}

export default CourseViewPage