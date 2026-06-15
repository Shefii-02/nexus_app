export const admissionFormConfig = [
  {
    section: 'Admission Details',
    name: 'student_id',
    label: 'Student',
    type: 'async-select',
    endpoint: '/users/search',
    valueField: 'id',
    labelField: 'name',
    required: true,
  },

  {
    section: 'Admission Details',
    name: 'course_id',
    label: 'Course',
    type: 'async-select',
    endpoint: '/admission/courses/search',
    valueField: 'id',
    labelField: 'name',
    required: true,
  },

 

  // {
  //   section: 'Fee Details',
  //   name: 'actual_fee',
  //   label: 'Actual Fee',
  //   type: 'number',
  //   required: true,
  // },

  // {
  //   section: 'Fee Details',
  //   name: 'discount_amount',
  //   label: 'Discount',
  //   type: 'number',
  // },

  // {
  //   section: 'Fee Details',
  //   name: 'discount_reason',
  //   label: 'Discount Reason',
  //   type: 'textarea',
  // },

  // {
  //   section: 'Fee Details',
  //   name: 'net_fee',
  //   label: 'Net Fee',
  //   type: 'number',
  //   required: true,
  // },

  {
    section: 'Payment',
    name: 'paid_amount',
    label: 'Paid Amount',
    type: 'number',
  },

  {
    section: 'Payment',
    name: 'payment_method',
    label: 'Payment Method',
    type: 'select',
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
        label: 'Card',
        value: 'card',
      },
      {
        label: 'Bank Transfer',
        value: 'bank_transfer',
      },
    ],
  },

  {
    section: 'Payment',
    name: 'transaction_no',
    label: 'Transaction No',
    type: 'text',
  },

  // {
  //   section: 'Others',
  //   name: 'status',
  //   label: 'Status',
  //   type: 'select',
  //   options: [
  //     {
  //       label: 'Active',
  //       value: 'active',
  //     },
  //     {
  //       label: 'Inactive',
  //       value: 'inactive',
  //     },
  //   ],
  // },

  {
    section: 'Others',
    name: 'notes',
    label: 'Notes',
    type: 'textarea',
    colSpan: 2,
  },
]