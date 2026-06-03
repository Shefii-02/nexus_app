interface Props {
  user: any
}

const UserOption = ({ user }: Props) => {
  return (
    <div className="flex items-center gap-3">
      <img
        src={
          user.avatar ||
          '/default-avatar.png'
        }
        alt=""
        className="w-10 h-10 rounded-full object-cover"
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

        <div className="text-xs text-blue-600">
          {user.acc_type}
        </div>
      </div>
    </div>
  )
}

export default UserOption