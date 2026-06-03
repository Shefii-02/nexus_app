import { useState } from 'react'
import { ChevronDown } from 'lucide-react'

const NewSidebar = () => {
  const [open, setOpen] = useState(true)

  return (
    <div className="w-64 bg-white dark:bg-gray-800 h-full p-4 shadow-lg">
      
      <h2 className="text-xl font-bold mb-6">Nexus App</h2>

      {/* Dashboard */}
      <div className="mb-2">Dashboard</div>

      {/* Courses */}
      <div>
        <div
          className="flex items-center justify-between cursor-pointer"
          onClick={() => setOpen(!open)}
        >
          <span>Courses</span>
          <ChevronDown className={`transition ${open ? 'rotate-180' : ''}`} />
        </div>

        {open && (
          <div className="ml-4 mt-2 border-l pl-3 space-y-2">
            <div className="bg-blue-500 text-white px-3 py-1 rounded-lg">
              Manage Course
            </div>
            <div>Add New Course</div>
            <div>Course Category</div>
            <div>Coupons</div>
            <div>Course Bundle</div>
          </div>
        )}
      </div>

      {/* Other */}
      <div className="mt-6 space-y-3 text-gray-600">
        <div>Bootcamp</div>
        <div>Team Training</div>
        <div>Tutor Booking</div>
        <div>EBook</div>
        <div>Enrollments</div>
      </div>
    </div>
  )
}

export default NewSidebar