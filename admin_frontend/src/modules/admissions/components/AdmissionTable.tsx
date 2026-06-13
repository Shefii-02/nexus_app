// src/modules/admissions/components/AdmissionTable.tsx

import Button from '../../../components/Button'

interface Props {
  data: any[]
  loading?: boolean
  onView: (id: number) => void
  onEdit: (id: number) => void
  onDelete: (id: number) => void
}

const AdmissionTable = ({
  data,
  loading,
  onView,
  onEdit,
  onDelete,
}: Props) => {
  if (loading) {
    return (
      <div className="bg-white rounded-xl p-6">
        Loading admissions...
      </div>
    )
  }

  return (
    <div className="overflow-x-auto bg-white rounded-xl shadow-sm border">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-gray-50">
            <th className="p-3 text-left">
              Student
            </th>

            <th className="p-3 text-left">
              Course
            </th>

            <th className="p-3 text-left">
              Admission Date
            </th>

            <th className="p-3 text-left">
              Expiry Date
            </th>

            <th className="p-3 text-left">
              Fee
            </th>

            <th className="p-3 text-left">
              Status
            </th>

            <th className="p-3 text-right">
              Actions
            </th>
          </tr>
        </thead>

        <tbody>
          {data?.length === 0 && (
            <tr>
              <td
                colSpan={7}
                className="p-6 text-center text-gray-500"
              >
                No admissions found
              </td>
            </tr>
          )}

          {data?.map((item) => (
            <tr
              key={item.id}
              className="border-t hover:bg-gray-50"
            >
              {/* Student */}
              <td className="p-3">
                <div className="font-medium">
                  {item.student?.name ||
                    item.student_name}
                </div>

                <div className="text-xs text-gray-500">
                  {item.student?.phone ||
                    item.phone}
                </div>
              </td>

              {/* Course */}
              <td className="p-3">
                <div className="font-medium">
                  {item.course?.name ||
                    item.course_name}
                </div>
              </td>

              {/* Admission Date */}
              <td className="p-3">
                {item.admission_date}
              </td>

              {/* Expiry Date */}
              <td className="p-3">
                {item.expiry_date}
              </td>

              {/* Fee */}
              <td className="p-3">
                ₹
                {item.net_fee ??
                  item.final_amount ??
                  0}
              </td>

              {/* Status */}
              <td className="p-3">
                <span
                  className={`px-2 py-1 rounded-full text-xs font-medium
                  ${
                    item.status ===
                    'active'
                      ? 'bg-green-100 text-green-700'
                      : item.status ===
                        'expired'
                      ? 'bg-red-100 text-red-700'
                      : item.status ===
                        'inactive'
                      ? 'bg-yellow-100 text-yellow-700'
                      : 'bg-gray-100 text-gray-700'
                  }
                `}
                >
                  {item.status}
                </span>
              </td>

              {/* Actions */}
              <td className="p-3 text-right">
                <div className="flex gap-2 justify-end">
                  <Button
                    size="sm"
                    onClick={() =>
                      onView(item.id)
                    }
                  >
                    View
                  </Button>

                  <Button
                    size="sm"
                    variant="secondary"
                    onClick={() =>
                      onEdit(item.id)
                    }
                  >
                    Edit
                  </Button>

                  <Button
                    size="sm"
                    variant="danger"
                    onClick={() =>
                      onDelete(item.id)
                    }
                  >
                    Delete
                  </Button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}

export default AdmissionTable