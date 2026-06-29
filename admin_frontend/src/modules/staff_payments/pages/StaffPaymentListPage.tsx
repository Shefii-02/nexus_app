import { useState } from 'react'

import { useNavigate } from 'react-router-dom'

import PageHeader from '../../../components/PageHeader'

import Button from '../../../components/Button'

import ConfirmModal from '../../../components/ConfirmModal'

import {
  useAdmissions,
  useDeleteAdmission,
} from '../staffPaymentHooks'

import AdmissionTable from '../components/AdmissionTable'

import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const AdmissionListPage = () => {

  const navigate =
    useNavigate()

  const [search, setSearch] =
    useState('')

  const [page, setPage] =
    useState(1)

  const [confirmId, setConfirmId] =
    useState<number | null>(
      null
    )

  const { data, isLoading } =
    useAdmissions({
      page,
      search,
    })

  const remove =
    useDeleteAdmission()

  return (
    <div className="space-y-6">

      <PageHeader
        title="Admissions"
        subtitle="Manage admissions"

        actions={
          <Button
            onClick={() =>
              navigate(
                '/admissions/create'
              )
            }
          >
            + New Admission
          </Button>
        }
      />

      <div className="bg-white p-4 rounded-xl shadow-sm">

        <input
          value={search}
          onChange={(e) =>
            setSearch(
              e.target.value
            )
          }

          placeholder="Search admission..."

          className="w-full md:w-80 border rounded p-2"
        />
      </div>

      <AdmissionTable
        data={
          data?.data || []
        }

        loading={
          isLoading
        }

        onView={(id) =>
          navigate(
            `/admissions/${id}/show`
          )
        }

        // onEdit={(id) =>
        //   navigate(
        //     `/admissions/${id}/edit`
        //   )
        // }

        onDelete={setConfirmId}
      />

      <ConfirmModal
        open={
          confirmId !== null
        }

        title="Delete Admission?"

        message="This action cannot be undone."

        confirmText="Delete"

        onCancel={() =>
          setConfirmId(null)
        }

        onConfirm={() =>
          handleMutationWithToast({
            action: () =>
              remove.mutateAsync(
                confirmId as number
              ),

            loadingMessage:
              'Deleting admission...',

            successMessage:
              'Admission deleted',

            onSuccess: () =>
              setConfirmId(null),
          })
        }
      />
    </div>
  )
}

export default AdmissionListPage