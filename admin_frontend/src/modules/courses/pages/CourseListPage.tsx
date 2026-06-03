import { useEffect, useState } from 'react'
import { useInfiniteCourses, useDeleteCourse } from '../courseHooks'
import CourseCard from '../components/CourseCard'
import CourseListItem from '../components/CourseListItem'
import ViewToggle from '../../../components/ViewToggle'
import PageHeader from '../../../components/PageHeader'
import { useNavigate } from 'react-router-dom'

const CourseListPage = () => {
  const [view, setView] = useState<'grid' | 'list'>('grid')
  const navigate = useNavigate()
  const {
    data,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage,
    isLoading,
  } = useInfiniteCourses()

  const deleteCourse = useDeleteCourse()

  const courses =
    data?.pages.flatMap((p) => p.data) || []

  const handleDelete = async (id: number) => {
    if (!confirm('Delete this course?')) return
    await deleteCourse.mutateAsync(id)
  }

  /** 🔥 AUTO SCROLL LOAD */
  useEffect(() => {
    const handleScroll = () => {
      const nearBottom =
        window.innerHeight + window.scrollY >=
        document.body.offsetHeight - 200

      if (nearBottom && hasNextPage && !isFetchingNextPage) {
        fetchNextPage()
      }
    }

    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [hasNextPage, isFetchingNextPage])

  return (
    <div className="p-6 space-y-4">

       {/* HEADER */}
      <PageHeader
        title="Courses"
        subtitle="Manage Courses"
        onBack={() => navigate('/')}
        actions={
            <button
              onClick={() => navigate('/courses/create')}
              className="bg-black text-white px-4 py-2 rounded"
            >
              + Create Course
            </button>
        }
      />

      {/* HEADER */}
      {/* <div className="flex justify-between items-center">
        <h1 className="text-xl font-semibold"></h1>
        <ViewToggle view={view} setView={setView} />
      </div> */}

      {/* EMPTY */}
      {!isLoading && courses.length === 0 && (
        <p className="text-center py-10 text-gray-500">
          No courses found
        </p>
      )}

      {/* GRID / LIST */}
      {view === 'grid' ? (
        <div className="grid md:grid-cols-3 gap-6">
          {courses.map((c) => (
            <CourseCard
              key={c.id}
              course={c}
              onDelete={handleDelete}
            />
          ))}
        </div>
      ) : (
        <div className="space-y-3">
          {courses.map((c) => (
            <CourseListItem
              key={c.id}
              course={c}
              onDelete={handleDelete}
            />
          ))}
        </div>
      )}

      {/* LOADING */}
      {isFetchingNextPage && (
        <p className="text-center py-4">Loading more...</p>
      )}

      {/* END */}
      {!hasNextPage && courses.length > 0 && (
        <p className="text-center py-6 text-gray-400">
          🎉 No more courses
        </p>
      )}
    </div>
  )
}

export default CourseListPage