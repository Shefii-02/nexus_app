import Dropdown from '../../../components/Dropdown'
import { MoreVertical } from 'lucide-react'
import type { Student } from '../studentService'
import ConfirmModal from '../../../components/ConfirmModal'
import { useDeleteStudent } from '../studentHooks'
import { useNavigate } from 'react-router-dom'
import { useState } from 'react'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'


interface Props {
  students: Student[]
  loading: boolean
  onView: (id: number) => void
  onEdit: (id: number) => void
  onDelete: (id: number) => void
}

const StudentTable = ({ students, loading, onView, onEdit, onDelete }: Props) => {
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const deleteStudent = useDeleteStudent()
  // const navigate = useNavigate()
  return (
    <>
      <div className="overflow-visible border rounded-xl bg-white">
        <table className="w-full text-sm">
          <thead className="bg-gray-50">
            <tr>
              <th className="p-3 text-left">Name</th>
              <th className="p-3 text-left">Roll No</th>
              <th className="px-6 py-4 text-left">Status</th>
              <th className="px-6 py-4 text-left">Last Active</th>
              <th className="px-6 py-4 text-left">Created At</th>
              <th className="p-3 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            {loading ? (
              <tr>
                <td colSpan={5} className="p-4 text-center">
                  Loading...
                </td>
              </tr>
            ) : students.length === 0 ? (
              <tr>
                <td colSpan={5} className="p-4 text-center">
                  No students found
                </td>
              </tr>
            ) : (
              students.map((s) => (
                <tr key={s.id} className="border-t">
                  <td className="p-3">
                    {s.user?.name || '-'}<br />
                    {s.user?.email || '-'} <br />
                    {s.phone} <br />
                  </td>
                  <td className="p-3">{s.roll_number}</td>
                  <td className="p-3 capitalize">{s.status}</td>
                  <td className="p-3">{s.user?.last_activated || '-'}</td> 
                  <td className="p-3">{s.user?.created_at || '-'}</td>
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
                        {
                          label: 'Delete',
                          danger: true,
                          onClick: () => setConfirmId(s.id),
                        },
                      ]}
                    />
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
      <ConfirmModal
        open={confirmId !== null}
        title="Delete Student?"
        message="This cannot be undone."
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () => deleteStudent.mutateAsync(confirmId as number),
            loadingMessage: 'Deleting student...',
            successMessage: 'Student deleted successfully',
            onSuccess: () => setConfirmId(null), // no redirect needed
          })
        }

      />
    </>
  )
}

export default StudentTable