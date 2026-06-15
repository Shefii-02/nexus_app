import { useState } from 'react'
import PageHeader from '../../components/PageHeader'
import RenewalTable from './RenewalTable'
import RenewalPaymentDrawer from './RenewalPaymentDrawer'
import { useRenewalDueList } from './RenewalHooks'


const RenewalDuePage = () => {
  const [selected, setSelected] = useState<any>(null)
  const [drawerOpen, setDrawerOpen] = useState(false)

  const { data, isLoading } = useRenewalDueList()

  return (
    <div className="space-y-6">
      <PageHeader
        title="Renewal Due"
        subtitle="Pending course renewals"
      />

      <RenewalTable
        data={data?.data || []}
        loading={isLoading}
        onPay={(row) => {
          setSelected(row)
          setDrawerOpen(true)
        }}
      />

      {drawerOpen && selected && (
        <RenewalPaymentDrawer
          open={drawerOpen}
          renewal={selected}
          onClose={() => {
            setDrawerOpen(false)
            setSelected(null)
          }}
        />
      )}
    </div>
  )
}

export default RenewalDuePage