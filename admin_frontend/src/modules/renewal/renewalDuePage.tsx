import { useNavigate } from 'react-router-dom'
import PageHeader from '../../../components/PageHeader'
import RenewalTable from '../components/RenewalTable'
import RenewalPaymentDrawer from '../components/RenewalPaymentDrawer'

import { useState } from 'react'
import { useRenewalDueList } from '../admissionHooks'

const RenewalDuePage = () => {
  const navigate = useNavigate()

  const [selected, setSelected] =
    useState<any>(null)

  const [drawerOpen, setDrawerOpen] =
    useState(false)

  const {
    data,
    isLoading,
  } = useRenewalDueList()

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