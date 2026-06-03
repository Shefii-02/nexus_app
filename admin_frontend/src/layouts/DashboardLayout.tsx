import NewHeader from './NewHeader'
import NewSidebar from './NewSidebar'

const DashboardLayout = ({ children }: any) => {
  return (
    <div className="flex h-screen bg-gray-100 dark:bg-gray-900">
      
      {/* Sidebar */}
      <NewSidebar />

      {/* Right Side */}
      <div className="flex-1 flex flex-col overflow-hidden">
        
        {/* Header */}
        <NewHeader />

        {/* Content */}
        <main className="flex-1 overflow-y-auto p-6">
          {children}
        </main>
      </div>
    </div>
  )
}

export default DashboardLayout