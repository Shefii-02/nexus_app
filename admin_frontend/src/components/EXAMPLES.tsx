/**
 * EXAMPLES - How to use Toast Notifications, Error Pages & Network Detection
 * This file demonstrates best practices for using the new features
 */

// ============================================
// 1. BASIC TOAST NOTIFICATIONS
// ============================================

import { useToast } from '@/hooks/useToast'
import { toastService } from '@/services/toastService'

export function BasicToastExample() {
  return (
    <div className="space-y-4">
      <button
        onClick={() => toastService.success('Success! Operation completed.')}
        className="px-4 py-2 bg-green-500 text-white rounded"
      >
        Show Success Toast
      </button>

      <button
        onClick={() => toastService.error('Error! Something went wrong.')}
        className="px-4 py-2 bg-red-500 text-white rounded"
      >
        Show Error Toast
      </button>

      <button
        onClick={() => toastService.info('Info! Here is some information.')}
        className="px-4 py-2 bg-blue-500 text-white rounded"
      >
        Show Info Toast
      </button>

      <button
        onClick={() => toastService.warning('Warning! Be careful!')}
        className="px-4 py-2 bg-yellow-500 text-white rounded"
      >
        Show Warning Toast
      </button>
    </div>
  )
}

// ============================================
// 2. LOADING TOAST WITH UPDATE
// ============================================

export function LoadingToastExample() {
  const toast = useToast()

  const handleLongOperation = async () => {
    const toastId = toast.loading('Processing your request...')

    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 2000))
      toast.updateLoading(toastId, true, 'Operation completed successfully!')
    } catch (error) {
      toast.updateLoading(toastId, false, 'Operation failed. Please try again.')
    }
  }

  return (
    <button
      onClick={handleLongOperation}
      className="px-4 py-2 bg-purple-500 text-white rounded"
    >
      Start Long Operation
    </button>
  )
}

// ============================================
// 3. FORM WITH TOAST NOTIFICATIONS
// ============================================

import { useCreateStudent } from '@/modules/students'

export function StudentFormExample() {
  const toast = useToast()
  const createMutation = useCreateStudent()

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    const formData = new FormData(e.currentTarget)

    try {
      const toastId = toast.loading('Creating student...')

      await createMutation.mutateAsync({
        name: formData.get('name') as string,
        email: formData.get('email') as string,
      })

      toast.updateLoading(toastId, true, 'Student created successfully!')
      e.currentTarget.reset()
    } catch (error) {
      toast.error(error)
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-4 max-w-md">
      <input
        type="text"
        name="name"
        placeholder="Student name"
        required
        className="w-full px-3 py-2 border rounded"
      />
      <input
        type="email"
        name="email"
        placeholder="Student email"
        required
        className="w-full px-3 py-2 border rounded"
      />
      <button
        type="submit"
        disabled={createMutation.isPending}
        className="w-full px-4 py-2 bg-blue-500 text-white rounded disabled:opacity-50"
      >
        {createMutation.isPending ? 'Creating...' : 'Create Student'}
      </button>
    </form>
  )
}

// ============================================
// 4. DELETE WITH CONFIRMATION AND TOAST
// ============================================

import { useDeleteStudent } from '@/modules/students'

export function DeleteStudentExample({ studentId, studentName }: { studentId: number; studentName: string }) {
  const toast = useToast()
  const deleteMutation = useDeleteStudent()

  const handleDelete = async () => {
    const confirmed = window.confirm(`Are you sure you want to delete ${studentName}?`)
    if (!confirmed) {
      toast.info('Delete cancelled.')
      return
    }

    try {
      const toastId = toast.loading(`Deleting ${studentName}...`)

      await deleteMutation.mutateAsync(studentId)

      toast.updateLoading(toastId, true, `${studentName} deleted successfully!`)
    } catch (error) {
      toast.error(error)
    }
  }

  return (
    <button
      onClick={handleDelete}
      disabled={deleteMutation.isPending}
      className="px-4 py-2 bg-red-500 text-white rounded disabled:opacity-50"
    >
      {deleteMutation.isPending ? 'Deleting...' : 'Delete'}
    </button>
  )
}

// ============================================
// 5. NETWORK STATUS DETECTION
// ============================================

import { useNetworkStatus } from '@/hooks/useNetworkStatus'
import NoNetworkPage from '@/components/NoNetworkPage'

export function OfflineSensitiveComponent() {
  const { isOnline } = useNetworkStatus()

  if (!isOnline) {
    return <NoNetworkPage />
  }

  return (
    <div className="p-4 bg-green-50 border border-green-200 rounded">
      <p className="text-green-700">✓ You are online. Data syncing is available.</p>
    </div>
  )
}

// ============================================
// 6. NETWORK STATUS IN HEADER
// ============================================

export function NetworkStatusIndicator() {
  const { isOnline } = useNetworkStatus()

  return (
    <div className="flex items-center gap-2">
      <div className={`w-3 h-3 rounded-full ${isOnline ? 'bg-green-500' : 'bg-red-500'}`}></div>
      <span className="text-sm text-gray-600">
        {isOnline ? 'Online' : 'Offline'}
      </span>
    </div>
  )
}

// ============================================
// 7. BATCH OPERATIONS WITH TOAST
// ============================================

export function BatchDeleteExample() {
  const toast = useToast()
  const deleteMutation = useDeleteStudent()

  const handleBatchDelete = async (studentIds: number[]) => {
    const confirmed = window.confirm(
      `Are you sure you want to delete ${studentIds.length} students?`
    )
    if (!confirmed) return

    const toastId = toast.loading(`Deleting ${studentIds.length} students...`)
    let successCount = 0
    let failCount = 0

    for (const id of studentIds) {
      try {
        await deleteMutation.mutateAsync(id)
        successCount++
      } catch (error) {
        failCount++
      }
    }

    if (failCount === 0) {
      toast.updateLoading(
        toastId,
        true,
        `Successfully deleted ${successCount} students!`
      )
    } else {
      toast.updateLoading(
        toastId,
        false,
        `Deleted ${successCount} students. ${failCount} failed.`
      )
    }
  }

  return (
    <button
      onClick={() => handleBatchDelete([1, 2, 3])}
      className="px-4 py-2 bg-red-500 text-white rounded"
    >
      Batch Delete Students
    </button>
  )
}

// ============================================
// 8. ERROR PAGE INTEGRATION IN ROUTES
// ============================================

import { useRouteError } from 'react-router-dom'
import ErrorPage from '@/components/ErrorPage'

export function StudentErrorElement() {
  const error = useRouteError() as any

  return (
    <ErrorPage
      statusCode={error?.status || 500}
      title={error?.statusText}
      message={error?.data?.message}
    />
  )
}

// ============================================
// 9. COMBINED EXAMPLE - COMPLETE STUDENT LIST
// ============================================

import { useStudents, useDeleteStudent, useUpdateStudent } from '@/modules/students'

export function CompleteStudentListExample() {
  const toast = useToast()
  const { isOnline } = useNetworkStatus()
  const { data: students, isLoading } = useStudents()
  const deleteMutation = useDeleteStudent()
  const updateMutation = useUpdateStudent()

  if (!isOnline) {
    return <NoNetworkPage />
  }

  if (isLoading) {
    return <div className="p-4">Loading students...</div>
  }

  const handleStatusUpdate = async (id: number, newStatus: string) => {
    try {
      const toastId = toast.loading('Updating student...')
      await updateMutation.mutateAsync({
        id,
        payload: { name: '', email: '', ...{ status: newStatus } },
      })
      toast.updateLoading(toastId, true, 'Student updated!')
    } catch (error) {
      toast.error(error)
    }
  }

  const handleDelete = async (id: number, name: string) => {
    if (!window.confirm(`Delete ${name}?`)) return

    try {
      const toastId = toast.loading('Deleting...')
      await deleteMutation.mutateAsync(id)
      toast.updateLoading(toastId, true, 'Student deleted!')
    } catch (error) {
      toast.error(error)
    }
  }

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-2xl font-bold">Students</h2>
        <NetworkStatusIndicator />
      </div>

      {students?.data.length === 0 ? (
        <div className="p-4 text-center text-gray-500">
          No students found. Create one to get started.
        </div>
      ) : (
        <table className="w-full border-collapse">
          <thead>
            <tr className="bg-gray-100">
              <th className="border px-4 py-2 text-left">Name</th>
              <th className="border px-4 py-2 text-left">Email</th>
              <th className="border px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            {students?.data.map((student) => (
              <tr key={student.id} className="hover:bg-gray-50">
                <td className="border px-4 py-2">{student.name}</td>
                <td className="border px-4 py-2">{student.email}</td>
                <td className="border px-4 py-2 text-center space-x-2">
                  <button
                    onClick={() => handleStatusUpdate(student.id, 'active')}
                    className="px-3 py-1 bg-blue-500 text-white rounded text-sm"
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => handleDelete(student.id, student.name)}
                    className="px-3 py-1 bg-red-500 text-white rounded text-sm"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </div>
  )
}

export default {
  BasicToastExample,
  LoadingToastExample,
  StudentFormExample,
  DeleteStudentExample,
  OfflineSensitiveComponent,
  NetworkStatusIndicator,
  BatchDeleteExample,
  StudentErrorElement,
  CompleteStudentListExample,
}
