export const teacherPaymentFormConfig = [
  // ── Payment Info ──────────────────────────────────────────
  {
    section: 'Payment Info',
    name: 'teacher_id',
    label: 'Teacher',
    type: 'async-select',
    endpoint: '/teacher/search',
    valueField: 'id',
    labelField: 'name',
    required: true,
  },
  {
    section: 'Payment Info',
    name: 'period_start',
    label: 'Period Start',
    type: 'date',
    required: true,
  },
  {
    section: 'Payment Info',
    name: 'period_end',
    label: 'Period End',
    type: 'date',
    required: true,
  },

  // ── Amount Details ────────────────────────────────────────
  {
    section: 'Amount Details',
    name: 'gross_amount',
    label: 'Gross Amount',
    type: 'number',
    required: true,
  },
  {
    section: 'Amount Details',
    name: 'deduction_amount',
    label: 'Deduction Amount',
    type: 'number',
    required: true,
    hideOnEdit: true,
  },
  {
    section: 'Amount Details',
    name: 'amount',
    label: 'Transfer Amount',
    type: 'number',
    required: true,
  },

  // ── Transaction Details ───────────────────────────────────
  {
    section: 'Transaction Details',
    name: 'payment_method',
    label: 'Payment Method',
    type: 'select',
    options: [
      { label: 'Bank Transfer', value: 'bank_transfer' },
      { label: 'UPI', value: 'upi' },
      { label: 'Cash', value: 'cash' },
      { label: 'Cheque', value: 'cheque' },
    ],
  },
  {
    section: 'Transaction Details',
    name: 'tax_no',
    label: 'Transaction Number',
    type: 'text',
  },
  {
    section: 'Transaction Details',
    name: 'payment_date',
    label: 'Payment Date',
    type: 'date',
    fullWidth: true,
    colSpan: 2,
  },
  {
    section: 'Transaction Details',
    name: 'remark',
    label: 'Remark',
    type: 'textarea',
    fullWidth: true,
    colSpan: 2,
  },

  // ── Status ────────────────────────────────────────────────
  {
    section: 'Status',
    name: 'status',
    label: 'Status',
    type: 'radio',
    colSpan: 2,
    options: [
      { label: 'Pending', value: 'pending' },
      { label: 'Released', value: 'released' },
    ],
  },
]