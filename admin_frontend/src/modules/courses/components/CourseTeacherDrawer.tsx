import { useEffect, useState } from 'react'
import SideDrawer from '../../../components/SideDrawer'
import {
    useCourseTeachers,
    useSaveCourseTeachers,
} from '../courseHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

interface Props {
    open: boolean
    courseId?: number
    courseTitle?: string
    onClose: () => void
}

const CourseTeacherDrawer = ({
    open,
    courseId,
    courseTitle,
    onClose,
}: Props) => {

    const [search, setSearch] = useState('')

    const [selected, setSelected] =
        useState<number[]>([])

    const {
        data,
        isLoading,
    } = useCourseTeachers(
        courseId,
        search
    )

    const saveMutation =
        useSaveCourseTeachers()

    useEffect(() => {


        if (!data?.teachers) return

        const checked =
            data.teachers
                .filter(
                    (teacher: any) =>
                        teacher.checked
                )
                .map(
                    (teacher: any) =>
                        teacher.id
                )

        setSelected(checked)


    }, [data])

    const toggleTeacher = (
        teacherId: number
    ) => {


        setSelected((prev) => {

            if (
                prev.includes(
                    teacherId
                )
            ) {
                return prev.filter(
                    (id) =>
                        id !== teacherId
                )
            }

            return [
                ...prev,
                teacherId,
            ]
        })


    }

    return (<SideDrawer
        open={open}
        title="Assign Teachers"
        width="w-[600px]"
        onClose={onClose}
    >


        <div className="space-y-4">

            <div className="bg-gray-50 rounded-xl p-4">

                <div className="font-semibold text-lg">
                    {courseTitle}
                </div>

                <div className="text-sm text-gray-500">
                    Selected Teachers:
                    {selected.length}
                </div>

            </div>

            <input
                type="text"
                placeholder="Search teachers..."
                value={search}
                onChange={(e) =>
                    setSearch(
                        e.target.value
                    )
                }
                className="
        w-full
        border
        rounded-lg
        p-3
      "
            />

            {isLoading && (
                <div>
                    Loading...
                </div>
            )}

            <div className="border rounded-xl">

                {data?.teachers?.map(
                    (teacher: any) => (

                        <label
                            key={teacher.id}
                            className="
              flex
              items-center
              justify-between
              p-4
              border-b
              hover:bg-gray-50
              cursor-pointer
            "
                        >

                            <div className="flex items-center gap-3">

                                <input
                                    type="checkbox"
                                    checked={selected.includes(
                                        teacher.id
                                    )}
                                    onChange={() =>
                                        toggleTeacher(
                                            teacher.id
                                        )
                                    }
                                />

                                <div>

                                    <div className="font-medium">
                                        {teacher.name}
                                    </div>

                                    <div className="text-xs text-gray-500">
                                        {teacher.email}
                                    </div>

                                </div>

                            </div>

                        </label>

                    )
                )}

            </div>

            <button
                className="
        w-full
        bg-black
        text-white
        py-3
        rounded-lg
      "
                onClick={() =>
                    handleMutationWithToast({

                        action: () =>
                            saveMutation.mutateAsync({
                                courseId:
                                    courseId!,
                                teacher_ids:
                                    selected,
                            }),

                        loadingMessage:
                            'Saving teachers...',

                        successMessage:
                            'Teachers assigned successfully',

                        onSuccess: () => {
                            onClose()
                        },
                    })
                }
            >
                Save Teachers
            </button>

        </div>

    </SideDrawer>


    )
}

export default CourseTeacherDrawer
