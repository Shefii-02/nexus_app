import Dropdown from '../../../components/Dropdown'
import { MoreVertical } from 'lucide-react'
import type { Staff } from '../staffService'

interface Props {
  staff: Staff[]
  loading: boolean
  onView: (id: number) => void
  onEdit: (id: number) => void
  onDelete: (id: number) => void
}

const StaffTable = ({ staff, loading, onView, onEdit, onDelete }: Props) => {
  return (
    <div className="overflow-visible border rounded-xl bg-white">
      <table className="w-full text-sm">
        <thead className="bg-gray-50">
          <tr>
            <th className="p-3 text-left">Name</th>
            <th className="p-3 text-left">Department</th>
            <th className="p-3 text-left">Designation</th>
            <th className="p-3 text-left">Status</th>
            <th className="px-6 py-4 text-left">Last Active</th>
            <th className="px-6 py-4 text-left">Created At</th>
            <th className="p-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          {loading ? (
            <tr>
              <td colSpan={6} className="p-4 text-center">Loading...</td>
            </tr>
          ) : staff.length === 0 ? (
            <tr>
              <td colSpan={6} className="p-4 text-center">No staff found</td>
            </tr>
          ) : (
            staff.map((s) => (
              <tr key={s.id} className="border-t">
                <td className="p-3">
                  {s.user?.name || '-'}<br />
                  {s.user?.email || '-'}<br />
                  {s.phone}
                </td>
                <td className="p-3">{s.department}</td>
                <td className="p-3">{s.designation}</td>
                <td className="p-3">{s.status}</td>
                <td className="p-3">{s.user?.last_activated ?? "--"}</td>
                <td className="p-3">{s.user?.created_at ?? "--"}</td>

                <td className="p-3 text-right">
                  <Dropdown
                    trigger={
                      <button className="p-2 hover:bg-gray-100 rounded">
                        <MoreVertical size={18} />
                      </button>
                    }
                    items={[
                      { label: 'View', onClick: () => onView(s.id) },
                      { label: 'Edit', onClick: () => onEdit(s.id) },
                      { label: 'Delete', danger: true, onClick: () => onDelete(s.id) },
                    ]}
                  />
                </td>
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  )
}

export default StaffTable