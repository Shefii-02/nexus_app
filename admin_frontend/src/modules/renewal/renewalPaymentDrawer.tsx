import { useState } from 'react'

import SideDrawer from '../../../components/SideDrawer'

import {
  useRenewalPayment
} from '../admissionHooks'

import {
  handleMutationWithToast
} from '../../../utils/handleMutationWithToast'

interface Props {
  open: boolean
  renewal: any
  onClose: () => void
}

const RenewalPaymentDrawer = ({
  open,
  renewal,
  onClose,
}: Props) => {
  const paymentMutation =
    useRenewalPayment()

  const [months, setMonths] =
    useState(1)

  const [discount, setDiscount] =
    useState(0)

  const [method, setMethod] =
    useState('cash')

  const [transactionNo, setTransactionNo] =
    useState('')

  const amount =
    Number(renewal.course_fee || 0)

  const total =
    amount * months

  const finalAmount =
    total - discount

  return (
    <SideDrawer
      open={open}
      title="Renew Course"
      width="w-[600px]"
      onClose={onClose}
    >
      <div className="space-y-4">

        <div className="bg-gray-50 p-4 rounded-xl">
          <div className="font-semibold">
            {renewal.student_name}
          </div>

          <div>
            {renewal.course_name}
          </div>
        </div>

        <div>
          <label>
            Renewal Months
          </label>

          <input
            type="number"
            min={1}
            value={months}
            onChange={(e) =>
              setMonths(
                Number(e.target.value)
              )
            }
            className="w-full border rounded p-2"
          />
        </div>

        <div>
          <label>
            Discount
          </label>

          <input
            type="number"
            value={discount}
            onChange={(e) =>
              setDiscount(
                Number(e.target.value)
              )
            }
            className="w-full border rounded p-2"
          />
        </div>

        <div>
          <label>
            Payment Method
          </label>

          <select
            value={method}
            onChange={(e) =>
              setMethod(
                e.target.value
              )
            }
            className="w-full border rounded p-2"
          >
            <option value="cash">
              Cash
            </option>

            <option value="upi">
              UPI
            </option>

            <option value="bank">
              Bank
            </option>
          </select>
        </div>

        <div>
          <label>
            Transaction No
          </label>

          <input
            value={transactionNo}
            onChange={(e) =>
              setTransactionNo(
                e.target.value
              )
            }
            className="w-full border rounded p-2"
          />
        </div>

        <div className="bg-green-50 rounded-xl p-4">
          <div>
            Fee : ₹{amount}
          </div>

          <div>
            Months : {months}
          </div>

          <div>
            Total : ₹{total}
          </div>

          <div>
            Discount : ₹{discount}
          </div>

          <div className="font-bold">
            Final :
            ₹{finalAmount}
          </div>
        </div>

        <button
          className="
            w-full
            bg-black
            text-white
            py-3
            rounded-lg
          "
          onClick={() =>
            handleMutationWithToast({
              action: () =>
                paymentMutation.mutateAsync({
                  admission_id:
                    renewal.admission_id,

                  months,

                  discount_amount:
                    discount,

                  payment_method:
                    method,

                  transaction_no:
                    transactionNo,
                }),

              loadingMessage:
                'Processing payment...',

              successMessage:
                'Renewal completed',

              onSuccess: onClose,
            })
          }
        >
          Receive Payment
        </button>

      </div>
    </SideDrawer>
  )
}

export default RenewalPaymentDrawer