export const admissionFormConfig = [
  {
    name: 'student_id',
    label: 'Student',
    type: 'async-select',
    section: 'basic',
    required: true,
    api: '/students/search',
  },

  {
    name: 'course_id',
    label: 'Course',
    type: 'async-select',
    section: 'basic',
    required: true,
    api: '/courses/search',
  },

  {
    name: 'actual_fee',
    label: 'Actual Fee',
    type: 'number',
    section: 'fee',
    readOnly: true,
  },

  {
    name: 'discount_amount',
    label: 'Discount Amount',
    type: 'number',
    section: 'fee',
  },

  {
    name: 'discount_reason',
    label: 'Discount Reason',
    type: 'text',
    section: 'fee',
  },

  {
    name: 'net_fee',
    label: 'Net Fee',
    type: 'number',
    section: 'fee',
    readOnly: true,
  },

  {
    name: 'admission_date',
    label: 'Admission Date',
    type: 'date',
    section: 'admission',
    required: true,
  },

  {
    name: 'expiry_date',
    label: 'Expiry Date',
    type: 'date',
    section: 'admission',
    required: true,
  },

  {
    name: 'status',
    label: 'Status',
    type: 'select',
    section: 'admission',

    options: [
      {
        label: 'Active',
        value: 'active',
      },

      {
        label: 'Inactive',
        value: 'inactive',
      },
    ],
  },

  {
    name: 'notes',
    label: 'Notes',
    type: 'textarea',
    section: 'admission',
    colSpan: 2,
  },

  {
    name: 'payment_method',
    label: 'Payment Method',
    type: 'select',
    section: 'payment',

    options: [
      {
        label: 'Cash',
        value: 'cash',
      },

      {
        label: 'UPI',
        value: 'upi',
      },

      {
        label: 'Bank',
        value: 'bank',
      },

      {
        label: 'Card',
        value: 'card',
      },
    ],
  },

  {
    name: 'transaction_no',
    label: 'Transaction No',
    type: 'text',
    section: 'payment',
  },
]