import AsyncSelect from 'react-select/async'
import UserOption from './UserOption'
import apiClient from '../../services/apiClient'

interface Props {
  label: string
  onChange: (id: number) => void
}

const UserSearchField = ({
  label,
  onChange,
}: Props) => {
  const loadUsers = async (
    inputValue: string
  ) => {
    const res =
      await apiClient.get(
        '/users/search',
        {
          params: {
            q: inputValue,
          },
        }
      )

    return res.data.data.map(
      (user: any) => ({
        value: user.id,
        label: user.name,
        user,
      })
    )
  }

  return (
    <div>
      <label className="block mb-2">
        {label}
      </label>

      <AsyncSelect
        cacheOptions
        defaultOptions
        loadOptions={loadUsers}
        onChange={(selected: any) =>
          onChange(selected?.value)
        }
        formatOptionLabel={(
          option: any
        ) => (
          <UserOption
            user={option.user}
          />
        )}
      />
    </div>
  )
}

export default UserSearchField