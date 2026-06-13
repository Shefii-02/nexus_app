import PageHeader from '../../../components/PageHeader'
import RenewalTable from '../components/RenewalTable'

import {
  useRenewalHistory
} from '../admissionHooks'

const RenewalHistoryPage = () => {
  const {
    data,
    isLoading,
  } = useRenewalHistory()

  return (
    <div className="space-y-6">

      <PageHeader
        title="Renewal History"
        subtitle="All renewal payments"
      />

      <RenewalTable
        data={data?.data || []}
        loading={isLoading}
        hidePayButton
      />
    </div>
  )
}

export default RenewalHistoryPage