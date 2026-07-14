import { useNavigate } from 'react-router-dom'
import TeacherTable from '../components/TeacherTable'
import { useTeachers, useDeleteTeacher } from '../teacherHooks'
import PageHeader from '../../../components/PageHeader'
import Button from '../../../components/Button'
import { useState } from 'react'
import { useAppSelector } from '../../../store/hooks'

const TeacherListPage = () => {
  const navigate = useNavigate()
  const user = useAppSelector((state) => state.auth.user)

  const [search, setSearch] = useState('')
  const [status, setStatus] = useState('')
  const [page, setPage] = useState(1)
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const { data, isLoading } = useTeachers({ page, search,status })
  const deleteTeacher = useDeleteTeacher()

  // ✅ frontend filter (optional)
  const teachers = (data?.data || []).filter((t) => {

    const matchesSearch =
      t.user?.name?.toLowerCase().includes(search.toLowerCase()) ||
      t.user?.email?.toLowerCase().includes(search.toLowerCase())

    const matchesStatus =
      !status ||
      t.status === status ||
      t.user?.status === status

    return matchesSearch && matchesStatus
  })

  const handleEdit = (id: number) => {
    navigate(`/teachers/${id}/edit`)
  }

  const handleDelete = (id: number) => {
    setConfirmId(id)
  }

  return (
    <div className="space-y-6">
      {/* HEADER */}
      <PageHeader
        title="Teachers"
        subtitle="Manage teachers"
        actions={
          // user?.acc_type === 'admin' && (
          <Button onClick={() => navigate('/teachers/create')}>
            + Create Teacher
          </Button>
          // )
        }
      />

      <div className="grid md:grid-cols-3 gap-3 bg-white">
        {/* SEARCH */}
        <div className=" p-4 w-full rounded-xl shadow-sm">
          <input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Search teachers..."
            className="w-full md:w-801 border p-2 rounded"
          />
        </div>

        <div className=" p-4 rounded-xl shadow-sm">

          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="w-full md:w-80 border p-2 rounded"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      {/* TABLE */}
      <TeacherTable
        teachers={teachers}
        loading={isLoading}
        onEdit={handleEdit}
        onDelete={handleDelete}
        onView={(id) => navigate(`/teachers/${id}`)}
      />
    </div>
  )
}

export default TeacherListPage