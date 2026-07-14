import { useState } from 'react'
import type { Teacher } from '../teacherService'
import ConfirmModal from '../../../components/ConfirmModal'
import Dropdown from '../../../components/Dropdown'
import { MoreVertical } from 'lucide-react'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import { useDeleteTeacher } from '../teacherHooks'
import { useNavigate } from 'react-router-dom'
import { humanDate } from '../../../utils/dateTime'

interface TeacherTableProps {
  teachers: Teacher[]
  loading: boolean
  onEdit: (id: number) => void
  onView?: (id: number) => void
}

const TeacherTable = ({ teachers, loading, onEdit, onView, onDelete }: TeacherTableProps) => {
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const deleteTeacher = useDeleteTeacher()
  const navigate = useNavigate()

  return (
    <>
      <div className="overflow-visible rounded-3xl border border-slate-200 bg-white shadow-sm">
        <table className="min-w-full divide-y divide-slate-200 text-sm">
          <thead className="bg-slate-50">
            <tr>
              <th className="px-6 py-4 text-left">User</th>
              {/* <th className="px-6 py-4 text-left">Subject</th> */}
              <th className="px-6 py-4 text-left">Status</th>
              <th className="px-6 py-4 text-left">Last Active</th>
              <th className="px-6 py-4 text-left">Created At</th>
              <th className="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            {loading ? (
              <tr>
                <td colSpan={5} className="p-6 text-center">Loading...</td>
              </tr>
            ) : teachers.length === 0 ? (
              <tr>
                <td colSpan={5} className="p-6 text-center">No data</td>
              </tr>
            ) : (
              teachers.map((teacher) => (
                <tr key={teacher.id}>
                  <td className="px-6 py-4">
                    {teacher.user?.name ?? '—'} <br/>
                    {teacher.user?.email ?? '—'}<br/>
                    {teacher.user?.phone ?? '—'}
                  </td>
                  {/* <td className="px-6 py-4">{teacher.subject ?? '—'}</td> */}
                  <td className="px-6 py-4 capitalize">{teacher.user?.status ?? '—'}</td>
                  <td className="px-6 py-4">{humanDate(teacher.user?.last_activated) ?? '—'}</td>
                  <td className="px-6 py-4">{humanDate(teacher.user?.created_at) ?? '—'}</td>
                  <td className="px-6 py-4 text-right">
                    <Dropdown
                      trigger={
                        <button className="p-2 rounded hover:bg-gray-100">
                          <MoreVertical size={18} />
                        </button>
                      }
                      items={[
                        {
                          label: 'View',
                          onClick: () => onView?.(teacher.id),
                        },
                        {
                          label: 'Edit',
                          onClick: () => onEdit(teacher.id),
                        },
                        {
                          label: 'Delete',
                          danger: true,
                          onClick: () => setConfirmId(teacher.id),
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

      {/* ✅ Confirm Modal */}
      <ConfirmModal
        open={confirmId !== null}
        title="Delete Teacher?"
        message="This cannot be undone."
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () => deleteTeacher.mutateAsync(confirmId as number),
            loadingMessage: 'Deleting teacher...',
            successMessage: 'Teacher deleted successfully',
            onSuccess: () => setConfirmId(null), // no redirect needed
          })
        }
      />
    </>
  )
}

export default TeacherTable