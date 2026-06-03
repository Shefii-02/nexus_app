export const StaffFormConfig = [
  { name: 'name', label: 'Name', type: 'text', required: true, section: 'user_details' },
  { name: 'email', label: 'Email', type: 'email', required: true, section: 'user_details' },
  { name: 'phone', label: 'Phone', type: 'text', section: 'user_details' },
  { name: 'password', label: 'Password', type: 'password', section: 'user_details', hideOnEdit: true },

  { name: 'department', label: 'Department', type: 'text', section: 'staff_details' },
  { name: 'designation', label: 'Qualification', type: 'text', section: 'staff_details' },

  { name: 'address', label: 'Address', type: 'textarea', colSpan: 2, section: 'staff_details' },

  {   
    name: 'status',
    label: 'Status',
    type: 'radio',
    colSpan: 2,
    section: 'account_details',
    options: [
      { label: 'Active', value: 'active' },
      { label: 'Inactive', value: 'inactive' },
    ],
  },
]