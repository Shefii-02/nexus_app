export const notificationFormConfig = [

  {
    name: 'target_type',
    label: 'Send To',
    type: 'select',
    section: 'basic',

    options: [
      {
        label: 'Single User',
        value: 'single'
      },

      {
        label: 'Multiple Users',
        value: 'multiple'
      },

      {
        label: 'All Students',
        value: 'students'
      },

      {
        label: 'All Teachers',
        value: 'teachers'
      },

      {
        label: 'All Staff',
        value: 'staff'
      },

      {
        label: 'All Users',
        value: 'all'
      }
    ]
  },

  {
    name: 'user_ids',
    label: 'Users',
    type: 'user-multi-search',
    options: [],
    dependsOn: 'target_type',
    dependsValue: 'multiple',
    colSpan: 2
  },

  {
    name: 'user_id',
    label: 'User',
    type: 'user-search',
    options: [],
    dependsOn: 'target_type',
    dependsValue: 'single',
    colSpan: 2
  },



  {
    name: 'title',
    label: 'Title',
    type: 'text',
    required: true,
    colSpan: 2
  },

  {
    name: 'message',
    label: 'Message',
    type: 'textarea',
    required: true,
    colSpan: 2
  },

  {
    name: 'type',
    label: 'Type',
    type: 'select',

    options: [
      {
        label: 'General',
        value: 'general'
      },

      {
        label: 'Course',
        value: 'course'
      },

      {
        label: 'Class',
        value: 'class'
      },

      {
        label: 'Payment',
        value: 'payment'
      }
    ]
  },

  {
    name: 'priority',
    label: 'Priority',
    type: 'select',

    options: [
      {
        label: 'Low',
        value: 'low'
      },

      {
        label: 'Normal',
        value: 'normal'
      },

      {
        label: 'High',
        value: 'high'
      },

      {
        label: 'Urgent',
        value: 'urgent'
      }
    ]
  },

]