import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import PageHeader from '../../../components/PageHeader'
import Button from '../../../components/Button'
import AnnouncementTable from '../components/AnnouncementTable'
import ConfirmModal from '../../../components/ConfirmModal'
import { useAnnouncements, useDeleteAnnouncement } from '../announcementHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const AnnouncementListPage = () => {
  const navigate = useNavigate()

  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const { data, isLoading } = useAnnouncements({
    page,
    search,
  })

  const deleteAnnouncement = useDeleteAnnouncement()

  const handleView = (id: number) => {
    navigate(`/announcements/${id}`)
  }

  const handleEdit = (id: number) => {
    navigate(`/announcements/${id}/edit`)
  }

  const handleDelete = (id: number) => {
    setConfirmId(id)
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Announcements"
        subtitle="Manage announcements"
        actions={
          <Button onClick={() => navigate('/announcements/create')}>
            + Create Announcement
          </Button>
        }
      />

      <div className="bg-white p-4 rounded-xl shadow-sm">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Search announcement..."
          className="w-full md:w-80 border rounded p-2"
        />
      </div>

      <AnnouncementTable
        data={data?.data || []}
        loading={isLoading}
        onView={handleView}
        onEdit={handleEdit}
        onDelete={handleDelete}
      />

      <ConfirmModal
        open={confirmId !== null}
        title="Delete Announcement?"
        message="This action cannot be undone."
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () =>
              deleteAnnouncement.mutateAsync(confirmId as number),
            loadingMessage: 'Deleting announcement...',
            successMessage: 'Announcement deleted',
            onSuccess: () => setConfirmId(null),
          })
        }
      />
    </div>
  )
}

export default AnnouncementListPage