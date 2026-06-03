import { Bell, Search, Sun, Moon } from 'lucide-react'
import { useState } from 'react'

const NewHeader = () => {
  const [openNotif, setOpenNotif] = useState(false)
  const [openUser, setOpenUser] = useState(false)
  const [dark, setDark] = useState(false)

  return (
    <div className="h-16 bg-white dark:bg-gray-800 px-6 flex items-center justify-between shadow sticky top-0 z-50">
      
      {/* 🔍 Search */}
      <div className="flex items-center bg-gray-100 px-3 py-2 rounded-lg w-96">
        <Search size={16} />
        <input
          placeholder="Search courses, teachers..."
          className="bg-transparent outline-none ml-2 w-full"
        />
      </div>

      {/* Right */}
      <div className="flex items-center gap-4">

        {/* 🌙 Dark Mode */}
        <button onClick={() => setDark(!dark)}>
          {dark ? <Sun /> : <Moon />}
        </button>

        {/* 🔔 Notification */}
        <div className="relative">
          <button onClick={() => setOpenNotif(!openNotif)}>
            <Bell />
            <span className="absolute -top-1 -right-1 bg-red-500 text-xs text-white px-1 rounded-full">
              5
            </span>
          </button>

          {openNotif && (
            <div className="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-xl p-3">
              <h4 className="font-semibold mb-2">Notifications</h4>

              {[1,2,3,4,5].map((i) => (
                <div key={i} className="text-sm py-2 border-b">
                  New update {i}
                </div>
              ))}

              <div className="text-blue-500 text-sm mt-2 cursor-pointer">
                View all
              </div>
            </div>
          )}
        </div>

        {/* 👤 User */}
        <div className="relative">
          <div
            onClick={() => setOpenUser(!openUser)}
            className="flex items-center gap-2 cursor-pointer"
          >
            <img
              src="https://i.pravatar.cc/40"
              className="w-8 h-8 rounded-full"
            />
            <span>John</span>
          </div>

          {openUser && (
            <div className="absolute right-0 mt-2 w-40 bg-white shadow rounded-lg p-2">
              <div className="p-2 hover:bg-gray-100 cursor-pointer">Profile</div>
              <div className="p-2 hover:bg-gray-100 cursor-pointer">Settings</div>
              <div className="p-2 hover:bg-gray-100 cursor-pointer">Logout</div>
            </div>
          )}
        </div>

      </div>
    </div>
  )
}

export default NewHeader