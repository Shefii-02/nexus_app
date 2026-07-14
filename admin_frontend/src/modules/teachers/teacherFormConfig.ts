export const teacherFormConfig = [
  { name: 'name', label: 'Name', type: 'text', required: true, section: 'user_details' },
  { name: 'email', label: 'Email', type: 'email', required: true, section: 'user_details' },
  { name: 'phone', label: 'Phone', type: 'text', required: true, section: 'user_details' },
  { name: 'password', label: 'Password', type: 'password', section: 'user_details', hideOnEdit: true },

  // { name: 'subject', label: 'Subject', type: 'text', section: 'teacher_details' },
  // { name: 'qualification', label: 'Qualification', type: 'text', section: 'teacher_details' },
  // { name: 'experience_years', label: 'Experience', type: 'number', section: 'teacher_details' },

  // { name: 'address', label: 'Address', type: 'textarea', colSpan: 2, section: 'teacher_details' },

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