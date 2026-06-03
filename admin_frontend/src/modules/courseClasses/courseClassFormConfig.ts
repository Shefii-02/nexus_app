export const courseClassFormConfig = [
  // {
  //   name: 'course_id',
  //   label: 'Course',
  //   type: 'select',
  //   section: 'basic',
  //   options: [], // inject from API
  // },
  {
    name: 'teacher_id',
    label: 'Teacher',
    type: 'select',
    section: 'basic',
    options: [],
  },

 

  {
    name: 'source',
    label: 'Source',
    type: 'select',
    options: [
      { label: 'Google Meet', value: 'google_meet' },
      { label: 'Youtube', value: 'youtube' },
      { label: 'Zoom', value: 'zoom'},
      { label: 'Offline', value: 'offline'},
      { label: 'Other', value: 'other'},
    ],
    section: 'basic',
  },

   {
    name: 'title',
    label: 'Title',
    type: 'text',
    section: 'basic',
    required: true,
    colSpan: 2,
  },

  {
    name: 'description',
    label: 'Description',
    type: 'textarea',
    section: 'basic',
    colSpan: 2,
  },

  {
    name: 'class_link',
    label: 'Class Link',
    type: 'text',
    section: 'links',
  },

  {
    name: 'record_link',
    label: 'Record Link',
    type: 'text',
    section: 'links',
  },



  {
    name: 'class_number',
    label: 'Class Number',
    type: 'text',
    section: 'schedule',
  },

  {
    name: 'scheduled_date',
    label: 'Scheduled Date',
    type: 'date',
    section: 'schedule',
  },

   {
    name: 'started_at',
    label: 'Started At',
    type: 'datetime-local',
    section: 'schedule',
  },

   {
    name: 'ended_at',
    label: 'Ended At',
    type: 'datetime-local',
    section: 'schedule',
  },

  {
    name: 'duration_minutes',
    label: 'Duration (Minutes)',
    type: 'number',
    section: 'schedule',
  },

  {
    name: 'room_location',
    label: 'Room Location',
    type: 'text',
    section: 'schedule',
  },

  {
    name: 'status',
    label: 'Status',
    type: 'select',
    section: 'meta',
    options: [
      { label: 'Draft', value: 'draft'},
      { label: 'Scheduled', value: 'scheduled' },
      { label: 'Completed', value: 'completed' },
      { label: 'Cancelled', value: 'cancelled' },
    ],
  },
]