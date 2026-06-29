import { useParams } from 'react-router-dom'

import PageHeader from '../../../components/PageHeader'

import {
  useAdmission,
} from '../teacherPaymentHooks'

const ViewAdmissionPage = () => {

  const { id } =
    useParams()

  const { data } =
    useAdmission(
      Number(id)
    )

  if (!data) return null

  return (
    <div className="space-y-6">

      <PageHeader
        title="Admission Details"
      />

      <div className="bg-white rounded-xl p-5 shadow">

        <div>
          Student:
          {data.student_name}
        </div>

        <div>
          Course:
          {data.course_name}
        </div>

        <div>
          Admission:
          {data.admission_date}
        </div>

        <div>
          Expiry:
          {data.expiry_date}
        </div>

        <div>
          Status:
          {data.status}
        </div>

        <div>
          Fee:
          ₹{data.net_fee}
        </div>

        <div>
          Notes:
          {data.notes}
        </div>
      </div>
    </div>
  )
}

export default ViewAdmissionPage