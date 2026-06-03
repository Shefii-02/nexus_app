export const studentFormConfig = [
  // USER
  { name: 'name', label: 'Name', type: 'text', required: true, section: 'user_details', },
  { name: 'email', label: 'Email', type: 'email', required: true, section: 'user_details' },
  { name: 'phone', label: 'Phone', type: 'text', section: 'user_details' },
  { name: 'password', label: 'Password', type: 'password', required: true, section: 'user_details', hideOnEdit: true },

  // STUDENT
  { name: 'roll_number', label: 'Roll Number', type: 'text', required: true, section: 'personal_details' },

  { name: 'guardian_name', label: 'Guardian Name', type: 'text', section: 'personal_details' },
  {
    name: 'guardian_phone', label: 'Guardian Phone', type: 'text',
    section: 'personal_details',
  },
  { name: 'address', label: 'Address', type: 'textarea', fullWidth: true, colSpan: 2, section: 'personal_details', },


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