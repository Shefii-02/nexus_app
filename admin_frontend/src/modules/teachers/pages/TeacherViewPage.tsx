import { useParams, useNavigate } from 'react-router-dom'
import { useTeacher, useDeleteTeacher } from '../teacherHooks'
import ConfirmModal from '../../../components/ConfirmModal'
import { useState } from 'react'
import PageHeader from '../../../components/PageHeader'
import Button from '../../../components/Button'
import { toast } from 'react-toastify'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const TeacherViewPage = () => {
    const { id } = useParams<{ id: string }>()
    const teacherId = Number(id)
    const navigate = useNavigate()

    const { data, isLoading } = useTeacher(teacherId)
    const deleteTeacher = useDeleteTeacher()

    const [confirmOpen, setConfirmOpen] = useState(false)

    if (isLoading) {
        return <div className="p-6">Loading...</div>
    }

    if (!data) {
        return <div className="p-6">Teacher not found</div>
    }

    return (
        <div className=" mx-auto space-y-6">

            {/* Header */}
            {/* HEADER */}
            <PageHeader
                title="View Teacher"
                subtitle="Details of the selected teacher"
                onBack={() => navigate('/teachers')}
                actions={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => navigate(`/teachers/edit/${id}`)}
                        >
                            Edit
                        </Button>

                        <Button
                            variant="danger"
                            onClick={() => setConfirmOpen(true)}
                        >
                            Delete
                        </Button>
                    </>
                }
            />

            <div className='card bg-white shadow-sm rounded-2xl p-6 '>


                {/* 👤 USER DETAILS */}
                <div className="  p-5  shadow-sm">
                    <h3 className="text-lg font-semibold mb-4">User Details</h3>

                    <div className="space-y-2 text-sm">
                        <p><strong>Name:</strong> {data.user?.name}</p>
                        <p><strong>Email:</strong> {data.user?.email}</p>
                        <p><strong>Phone:</strong> {data.user?.phone ?? '—'}</p>
                    </div>
                </div>

                {/* 🎓 TEACHER DETAILS */}
                <div className=" p-5  mt-3">
                    <h3 className="text-lg font-semibold mb-4">Teacher Details</h3>

                    <div className="space-y-2 text-sm">
                        <p><strong>Subject:</strong> {data.subject}</p>
                        <p><strong>Qualification:</strong> {data.qualification}</p>
                        <p><strong>Experience:</strong> {data.experience_years} years</p>
                        <p><strong>Address:</strong> {data.address ?? '—'}</p>
                    </div>
                </div>
            </div>



            {/* 🔥 Confirm Modal */}
            <ConfirmModal
                open={confirmOpen}
                title="Delete Teacher?"
                message="This cannot be undone."
                confirmText="Delete"
                onCancel={() => setConfirmOpen(false)}
                onConfirm={() =>
                    handleMutationWithToast({
                        action: () => deleteTeacher.mutateAsync(teacherId),
                        successMessage: 'Teacher deleted successfully',
                        redirect: '/teachers',
                        navigate,
                        onSuccess: () => setConfirmOpen(false),
                    })
                }
            />
        </div>
    )
}

export default TeacherViewPage