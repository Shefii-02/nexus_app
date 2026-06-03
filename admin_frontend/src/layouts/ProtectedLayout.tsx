import { useState } from 'react'
import { useAppDispatch, useAppSelector } from '../store/hooks'
import { logout } from '../modules/auth/authSlice'
import Sidebar from './Sidebar'
import Header from './Header'
import { navSections } from './navigation'

const ProtectedLayout = ({ children }: { children: React.ReactNode }) => {
  const dispatch = useAppDispatch()
  const user = useAppSelector((state) => state.auth.user)

  const [menuOpen, setMenuOpen] = useState(false)

  return (
    <div className="min-h-screen flex bg-slate-50">

      {/* 🖥 DESKTOP SIDEBAR */}
      <aside className="hidden h-screen lg:block w-72 bg-white border-r p-6 overflow-auto">
        <Header />
        <Sidebar sections={navSections} user={user} />
      </aside>

      {/* 📱 MOBILE HEADER */}
      <div className="lg:hidden fixed top-0 left-0 right-0 bg-white border-b p-4 flex justify-between z-30">
        <h1 className="font-semibold">Nexus Admin</h1>
        <button onClick={() => setMenuOpen(true)}>Menu</button>
      </div>

      {/* 📱 MOBILE MENU */}
      {menuOpen && (
        <div className="fixed inset-0 bg-black/40 z-40">
          <div className="w-72 bg-white h-full p-6">
            <button onClick={() => setMenuOpen(false)}>Close</button>
            <Sidebar
              sections={navSections}
              user={user}
              onNavigate={() => setMenuOpen(false)}
            />
          </div>
        </div>
      )}

      {/* 🧠 MAIN */}
      <main className="flex-1 h-screen p-6 lg:p-8 mt-16 lg:mt-0 overflow-auto">

        {/* TOP BAR */}
        <div className="mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm">
          <div>
            <p className="text-xs text-gray-500 uppercase">{user?.acc_type}</p>
            <h2 className="text-lg font-semibold">
              Welcome back, {user?.name}
            </h2>
          </div>

          <button
            onClick={() => dispatch(logout())}
            className="bg-black text-white px-4 py-2 rounded"
          >
            Logout
          </button>
        </div>

        {children}
      </main>
    </div>
  )
}

export default ProtectedLayout