interface Props {
  search: string
  setSearch: (v: string) => void

  status: string
  setStatus: (v: string) => void

  targetType: string
  setTargetType: (v: string) => void
}

const AnnouncementFilters = ({
  search,
  setSearch,
  status,
  setStatus,
  targetType,
  setTargetType,
}: Props) => {
  return (
    <div className="bg-white rounded-xl border p-4">
      <div className="grid md:grid-cols-3 gap-4">

        <input
          value={search}
          onChange={(e) =>
            setSearch(e.target.value)
          }
          placeholder="Search..."
          className="border rounded p-2"
        />

        <select
          value={status}
          onChange={(e) =>
            setStatus(e.target.value)
          }
          className="border rounded p-2"
        >
          <option value="">
            All Status
          </option>

          <option value="active">
            Active
          </option>

          <option value="inactive">
            Inactive
          </option>
        </select>

        <select
          value={targetType}
          onChange={(e) =>
            setTargetType(
              e.target.value
            )
          }
          className="border rounded p-2"
        >
          <option value="">
            All Targets
          </option>

          <option value="all">
            All Users
          </option>

          <option value="students">
            Students
          </option>

          <option value="teachers">
            Teachers
          </option>

          <option value="staff">
            Staff
          </option>
        </select>
      </div>
    </div>
  )
}

export default AnnouncementFilters