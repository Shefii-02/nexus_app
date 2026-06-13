import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'

import PageHeader from '../../../components/PageHeader'
import ConfirmModal from '../../../components/ConfirmModal'

import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

import {
  useInfiniteCourses,
  useDeleteCourse,
} from '../courseHooks'

import CourseCard from '../components/CourseCard'
import CourseListItem from '../components/CourseListItem'
import CourseViewToggle from '../components/CourseViewToggle'
import CourseTeacherDrawer from '../components/CourseTeacherDrawer'
import CourseStudentDrawer from '../components/CourseStudentDrawer'
import CourseConversationDrawer from '../components/CourseConversationDrawer'

const CourseListPage = () => {
  const navigate = useNavigate()

  const [view, setView] = useState<'grid' | 'list'>(
    'grid'
  )

  const [search, setSearch] = useState('')
  const [mode, setMode] = useState('')
  const [status, setStatus] = useState('')

  const [confirmId, setConfirmId] =
    useState<number | null>(null)


  const [drawerType, setDrawerType] = useState<
    'teachers' | 'students' | 'conversation' | null
  >(null)

  const [selectedCourse, setSelectedCourse] =
    useState<any>(null)


  const deleteCourse = useDeleteCourse()

  const {
    data,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage,
    isLoading,
  } = useInfiniteCourses({
    search,
    mode,
    status,
  })

  const courses =
    data?.pages.flatMap((p) => p.data) || []

  useEffect(() => {
    const handleScroll = () => {
      const nearBottom =
        window.innerHeight + window.scrollY >=
        document.body.offsetHeight - 200


      if (
        nearBottom &&
        hasNextPage &&
        !isFetchingNextPage
      ) {
        fetchNextPage()
      }
    }

    window.addEventListener(
      'scroll',
      handleScroll
    )

    return () =>
      window.removeEventListener(
        'scroll',
        handleScroll
      )


  }, [
    hasNextPage,
    isFetchingNextPage,
    fetchNextPage,
  ])

  return (<div className="space-y-6">
    <PageHeader
      title="Courses"
      subtitle="Manage Courses"
      actions={
        <button
          onClick={() =>
            navigate('/courses/create')
          }
          className="bg-black text-white px-4 py-2 rounded"
        >
          + Create Course </button>
      }
    />


    <div className="bg-white p-4 rounded-xl shadow-sm">
      <div className="grid md:grid-cols-4 gap-3">
        <input
          value={search}
          onChange={(e) =>
            setSearch(e.target.value)
          }
          placeholder="Search course..."
          className="border p-2 rounded"
        />

        <select
          value={mode}
          onChange={(e) =>
            setMode(e.target.value)
          }
          className="border p-2 rounded"
        >
          <option value="">
            All Modes
          </option>

          <option value="online">
            Online
          </option>

          <option value="offline">
            Offline
          </option>

          <option value="hybrid">
            Hybrid
          </option>
        </select>

        <select
          value={status}
          onChange={(e) =>
            setStatus(e.target.value)
          }
          className="border p-2 rounded"
        >
          <option value="">
            All Status
          </option>

          <option value="active">
            Active
          </option>

          <option value="inactive">
            Inactive
          </option>

          <option value="draft">
            Draft
          </option>
        </select>

        {/* <CourseViewToggle
          view={view}
          setView={setView}
        /> */}
      </div>
    </div>

    {!isLoading &&
      courses.length === 0 && (
        <div className="text-center py-10 text-gray-500">
          No courses found
        </div>
      )}

    {view === 'grid' ? (
      <div className="grid md:grid-cols-3 gap-6">
        {courses.map((course) => (
          <CourseCard
            key={course.id}
            course={course}
            onDelete={setConfirmId}
            onTeachers={(course: any) => {
              setSelectedCourse(course)
              setDrawerType('teachers')
            }}
            onStudents={(course: any) => {
              setSelectedCourse(course)
              setDrawerType('students')
            }}
            onConversation={(course: any) => {
              setSelectedCourse(course)
              setDrawerType('conversation')
            }}

          />
        ))}
      </div>
    ) : (
      <div className="space-y-3">
        {courses.map((course) => (
          <CourseListItem
            key={course.id}
            course={course}
            onDelete={setConfirmId}
          />
        ))}
      </div>
    )}

    {isFetchingNextPage && (
      <p className="text-center">
        Loading more...
      </p>
    )}

    {!hasNextPage &&
      courses.length > 0 && (
        <p className="text-center text-gray-400">
          🎉 No more courses
        </p>
      )}

    <ConfirmModal
      open={confirmId !== null}
      title="Delete Course?"
      message="This action cannot be undone."
      confirmText="Delete"
      onCancel={() =>
        setConfirmId(null)
      }
      onConfirm={() =>
        handleMutationWithToast({
          action: () =>
            deleteCourse.mutateAsync(
              confirmId as number
            ),

          loadingMessage:
            'Deleting course...',

          successMessage:
            'Course deleted successfully',

          onSuccess: () =>
            setConfirmId(null),
        })
      }
    />
    {drawerType === 'teachers' &&
      selectedCourse && (
        <CourseTeacherDrawer
          open
          courseId={selectedCourse.id}
          courseTitle={selectedCourse.name}
          onClose={() => {
            setDrawerType(null)
            setSelectedCourse(null)
          }}
        />
      )}

    {drawerType === 'students' &&
      selectedCourse && (
        <CourseStudentDrawer
          open
          courseId={selectedCourse.id}
          courseTitle={selectedCourse.name}
          onClose={() => {
            setDrawerType(null)
            setSelectedCourse(null)
          }}
        />
      )}

    {drawerType === 'conversation' &&
      selectedCourse && (
        <CourseConversationDrawer
          open
          courseId={selectedCourse.id}
          courseTitle={selectedCourse.name}
          onClose={() => {
            setDrawerType(null)
            setSelectedCourse(null)
          }}
        />
      )}


  </div>


  )
}

export default CourseListPage
