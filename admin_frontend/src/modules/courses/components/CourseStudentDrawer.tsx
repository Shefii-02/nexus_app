import SideDrawer from '../../../components/SideDrawer'
import {
    useCourseStudents,
} from '../courseHooks'

interface Props {
    open: boolean
    courseId: number
    courseTitle: string
    onClose: () => void
}

const CourseStudentDrawer = ({
    open,
    courseId,
    courseTitle,
    onClose,
}: Props) => {

    const {
        data,
        isLoading,
    } = useCourseStudents(courseId)
    const students = data?.data || []
    return (
        <SideDrawer
            open={open}
            title="Enrolled Students"
            width="w-[900px]"
            onClose={onClose}
        >
            <div className="space-y-4">

                <div className="bg-gray-50 p-4 rounded-xl">
                    <h2 className="font-semibold">
                        {courseTitle}
                    </h2>
                </div>

                {isLoading && (
                    <p>Loading...</p>
                )}

                <table className="w-full text-sm">
                    <thead>
                        <tr className="border-b">
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Expiry</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        {data?.data?.map(
                            (student: any) => (
                                <tr
                                    key={
                                        student.admission_id
                                    }
                                    className="border-b"
                                >
                                    <td>
                                        {student.student_name}
                                    </td>

                                    <td>
                                        {student.phone}
                                    </td>

                                    <td>
                                        {student.status}
                                    </td>

                                    <td>
                                        {student.expiry_date}
                                    </td>

                                    <td>

                                        <button
                                            className="
                      text-blue-600
                      mr-2
                    "
                                        >
                                            Edit
                                        </button>

                                        <button
                                            className="
                      text-red-600
                    "
                                        >
                                            Remove
                                        </button>

                                    </td>

                                </tr>

                            ))}
                    </tbody>
                </table>

            </div>
        </SideDrawer>
    )
}

export default CourseStudentDrawer