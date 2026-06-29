import { useState } from 'react'
import { useNavigate } from 'react-router-dom'

import PageHeader from '../../../components/PageHeader'
import Button from '../../../components/Button'
import ConfirmModal from '../../../components/ConfirmModal'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

import {
  useTeacherPayments,
  useDeleteTeacherPayment,
} from '../teacherPaymentHooks'

import PaymentTable from './PaymentTable'

const TeacherPaymentListPage = () => {
  const navigate = useNavigate()

  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const { data, isLoading } = useTeacherPayments({ page, search })
  const remove = useDeleteTeacherPayment()

  return (
    <div className="space-y-6">
      <PageHeader
        title="Teacher Payments"
        subtitle="Manage teacher salary and payment records"
        actions={
          <Button onClick={() => navigate('/teacher-payments/create')}>
            + New Payment
          </Button>
        }
      />

      <div className="bg-white p-4 rounded-xl shadow-sm">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Search by teacher name or period..."
          className="w-full md:w-80 border rounded p-2"
        />
      </div>

      <PaymentTable
        data={data?.data || []}
        loading={isLoading}
        onView={(id) => navigate(`/teacher-payments/${id}/show`)}
        onEdit={(id) => navigate(`/teacher-payments/${id}/edit`)}
        onDelete={setConfirmId}
      />

      <ConfirmModal
        open={confirmId !== null}
        title="Delete Payment?"
        message="This action cannot be undone."
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () => remove.mutateAsync(confirmId as number),
            loadingMessage: 'Deleting payment...',
            successMessage: 'Payment deleted successfully',
            onSuccess: () => setConfirmId(null),
          })
        }
      />
    </div>
  )
}

export default TeacherPaymentListPage