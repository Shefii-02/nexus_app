import { useEffect, useState } from 'react'
import SideDrawer from '../../../components/SideDrawer'
import ImageUpload from '../../../components/form/ImageUpload'

import {
    useCourseConversation,
    useConversationMembers,
    useConversationUserSearch,
    useSaveConversation,
    useRemoveConversationMember,
} from '../courseHooks'

import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

interface Props {
    open: boolean
    courseId?: number
    courseTitle?: string
    onClose: () => void
}

const CourseConversationDrawer = ({
    open,
    courseId,
    courseTitle,
    onClose,
}: Props) => {
    const [tab, setTab] = useState<
        'members' | 'add'

    >('members')

    const [search, setSearch] = useState('')

    const [title, setTitle] = useState('')
    const [status, setStatus] =
        useState('active')

    const [avatar, setAvatar] =
        useState<any>(null)

    const [selected, setSelected] =
        useState<number[]>([])

    const { data: conversation } =
        useCourseConversation(courseId)

    const { data: members } =
        useConversationMembers(
            courseId
        )

    const { data: users } =
        useConversationUserSearch(
            courseId,
            search
        )

    const saveMutation =
        useSaveConversation()

    const removeMutation =
        useRemoveConversationMember()

    useEffect(() => {
        if (!conversation?.data)
            return


        setTitle(
            conversation.data.title || ''
        )

        setStatus(
            conversation.data.status ||
            'active'
        )

        setAvatar(
            conversation.data.avatar ||
            null
        )


    }, [conversation])

    const toggleParticipant = (
        userId: number
    ) => {
        setSelected((prev) =>
            prev.includes(userId)
                ? prev.filter(
                    (x) => x !== userId
                )
                : [...prev, userId]
        )
    }

    const saveConversation = () => {
        const form = new FormData()


        form.append('title', title)

        form.append('status', status)

        if (
            avatar &&
            avatar instanceof File
        ) {
            form.append(
                'avatar',
                avatar
            )
        }

        selected.forEach((id) =>
            form.append(
                'participant_ids[]',
                String(id)
            )
        )

        handleMutationWithToast({
            action: () =>
                saveMutation.mutateAsync({
                    courseId: courseId!,
                    payload: form,
                }),

            loadingMessage:
                'Saving group...',

            successMessage:
                'Group saved successfully',

            onSuccess: () => {
                setSelected([])
                  onClose()
            },
        })


    }

    return (<SideDrawer
        open={open}
        title="Conversation Group"
        width="w-[700px]"
        onClose={onClose}
    > <div className="space-y-5">


            {/* Course */}

            <div className="bg-gray-50 rounded-xl p-4">
                <div className="font-semibold">
                    {courseTitle}
                </div>

                <div className="text-sm text-gray-500">
                    Course Group
                </div>
            </div>

            {/* Avatar */}

            <ImageUpload
                label="Group Avatar"
                name="avatar"
                watch={() => avatar}
                setValue={(
                    _name: string,
                    file: File
                ) => setAvatar(file)}
            />

            {/* Title */}

            <div>
                <label className="block mb-1">
                    Group Title
                </label>

                <input
                    value={title}
                    onChange={(e) =>
                        setTitle(
                            e.target.value
                        )
                    }
                    className="w-full border rounded-lg p-2"
                />
            </div>

            {/* Status */}

            <div>
                <label className="block mb-1">
                    Status
                </label>

                <select
                    value={status}
                    onChange={(e) =>
                        setStatus(
                            e.target.value
                        )
                    }
                    className="w-full border rounded-lg p-2"
                >
                    <option value="active">
                        Active
                    </option>

                    <option value="inactive">
                        Inactive
                    </option>
                </select>
            </div>

            {/* Tabs */}

            <div className="flex border-b">
                <button
                    onClick={() =>
                        setTab('members')
                    }
                    className={`px-4 py-2 ${tab === 'members'
                            ? 'border-b-2 border-black font-semibold'
                            : ''
                        }`}
                >
                    Members
                </button>

                <button
                    onClick={() =>
                        setTab('add')
                    }
                    className={`px-4 py-2 ${tab === 'add'
                            ? 'border-b-2 border-black font-semibold'
                            : ''
                        }`}
                >
                    Add Members
                </button>
            </div>

            {/* Existing Members */}

            {tab === 'members' && (
                <div className="border rounded-xl max-h-[450px] overflow-y-auto">

                    {members?.data?.length ===
                        0 && (
                            <div className="p-5 text-center text-gray-500">
                                No members found
                            </div>
                        )}

                    {members?.data?.map(
                        (user: any) => (
                            <div
                                key={user.id}
                                className="
              flex
              justify-between
              items-center
              border-b
              p-3
            "
                            >
                                <div className="flex gap-3">

                                    <img
                                        src={
                                            user.avatar ||
                                            '/avatar.png'
                                        }
                                        className="
                  w-12
                  h-12
                  rounded-full
                  object-cover
                "
                                    />

                                    <div>
                                        <div className="font-medium">
                                            {user.name}
                                        </div>

                                        <div className="text-xs text-gray-500">
                                            {user.email}
                                        </div>

                                        <div className="text-xs text-gray-500">
                                            {user.phone}
                                        </div>

                                        <div className="text-xs text-blue-500">
                                            {user.acc_type}
                                        </div>
                                    </div>
                                </div>

                                <button
                                    className="
                text-red-500
                text-sm
              "
                                    onClick={() =>
                                        handleMutationWithToast({
                                            action: () =>
                                                removeMutation.mutateAsync(
                                                    {
                                                        courseId:
                                                            courseId!,
                                                        userId:
                                                            user.id,
                                                    }
                                                ),

                                            loadingMessage:
                                                'Removing member...',

                                            successMessage:
                                                'Member removed',
                                        })
                                    }
                                >
                                    Remove
                                </button>
                            </div>
                        )
                    )}
                </div>
            )}

            {/* Add Members */}

            {tab === 'add' && (
                <>
                    <input
                        value={search}
                        onChange={(e) =>
                            setSearch(
                                e.target.value
                            )
                        }
                        placeholder="Search users..."
                        className="
          w-full
          border
          rounded-lg
          p-2
        "
                    />

                    <div
                        className="
          border
          rounded-xl
          max-h-[450px]
          overflow-y-auto
        "
                    >
                        {users?.data?.map(
                            (user: any) => (
                                <label
                                    key={user.id}
                                    className="
                flex
                items-center
                gap-3
                p-3
                border-b
              "
                                >
                                    <input
                                        type="checkbox"
                                        checked={selected.includes(
                                            user.id
                                        )}
                                        onChange={() =>
                                            toggleParticipant(
                                                user.id
                                            )
                                        }
                                    />

                                    <img
                                        src={
                                            user.avatar ||
                                            '/avatar.png'
                                        }
                                        className="
                  w-12
                  h-12
                  rounded-full
                  object-cover
                "
                                    />

                                    <div>
                                        <div className="font-medium">
                                            {user.name}
                                        </div>

                                        <div className="text-xs text-gray-500">
                                            {user.email}
                                        </div>

                                        <div className="text-xs text-gray-500">
                                            {user.phone}
                                        </div>

                                        <div className="text-xs text-blue-500">
                                            {user.acc_type}
                                        </div>
                                    </div>
                                </label>
                            )
                        )}
                    </div>
                </>
            )}

            {/* Save */}

            <button
                onClick={
                    saveConversation
                }
                className="
      w-full
      bg-black
      text-white
      py-3
      rounded-lg
    "
            >
                Save Group
            </button>

        </div>
    </SideDrawer>


    )
}

export default CourseConversationDrawer
