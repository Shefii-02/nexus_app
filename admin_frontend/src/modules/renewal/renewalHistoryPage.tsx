import PageHeader from "../../components/PageHeader"
import { useRenewalHistory } from "./RenewalHooks"
import RenewalTable from "./RenewalTable"


const RenewalHistoryPage = () => {
  const { data, isLoading } = useRenewalHistory()

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