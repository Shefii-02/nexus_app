import { useNavigate } from 'react-router-dom'
import {useDeleteRole, useRoles } from '../roleHooks'
import PageHeader from '../../../components/PageHeader'
import Button from '../../../components/Button'
import { useState } from 'react'
import { useAppSelector } from '../../../store/hooks'
import RoleTable from './RoleTable'

const RoleListPage = () => {
  const navigate = useNavigate()
  const user = useAppSelector((state) => state.auth.user)

  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const { data, isLoading } = useRoles({ page, search })
  const deleteRole = useDeleteRole()

  // ✅ frontend filter (optional)
  const roles = (data?.data || []).filter((r) =>
    r.name?.toLowerCase().includes(search.toLowerCase()) ||
    r.user?.email?.toLowerCase().includes(search.toLowerCase())
  )

  const handleEdit = (id: number) => {
    navigate(`/roles/${id}/edit`)
  }

  const handleDelete = (id: number) => {
    setConfirmId(id)
  }

  return (
    <div className="space-y-6">
      {/* HEADER */}
      <PageHeader
        title="Roles"
        subtitle="Manage roles and their permissions"
        actions={
          user?.acc_type === 'admin' && (
            <Button onClick={() => navigate('/roles/create')}>
              + Create Role
            </Button>
          )
        }
      />

      {/* SEARCH */}
      <div className="bg-white p-4 rounded-xl shadow-sm">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Search teachers..."
          className="w-full md:w-80 border p-2 rounded"
        />
      </div>

      {/* TABLE */}
      <RoleTable
        roles={roles}
        loading={isLoading}
        onEdit={handleEdit}
        onDelete={handleDelete}
        onView={(id) => navigate(`/roles/${id}`)}
      />
    </div>
  )
}

export default RoleListPage