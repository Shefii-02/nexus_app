export const materialFormConfig = [
  {
    name: 'title',
    label: 'Title',
    type: 'text',
    section: 'basic',
    required: true,
    colSpan:2
  },

  {
    name: 'description',
    label: 'Description',
    type: 'textarea',
    section: 'basic',
    colSpan:2
  },

  {
    name: 'file_url',
    label: 'File',
    type: 'file',
    section: 'media',
    accept: ''
  },

  {
    name: 'material_type',
    label: 'Type',
    type: 'select',
    section: 'basic',
    options: [
      { label: 'PDF', value: 'pdf' },
      { label: 'Video', value: 'video' },
      { label: 'Link', value: 'link' },
    ],
  },

  {
    name: 'order',
    label: 'Order',
    type: 'number',
    section: 'basic',
  },

  {
    name: 'status',
    label: 'Status',
    type: 'select',
    section: 'meta',
    options: [
      { label: 'Active', value: 'active' },
      { label: 'Inactive', value: 'inactive' },
    ],
  },
]