export const announcementFormConfig = [

  /** ========================
 * MEDIA
 ======================== */
  {
    name: 'thumbnail',
    label: 'Thumbnail',
    type: 'image-with-crop', // 🔥 custom component
    section: 'media',
    colSpan: 2,
  },

  {
    name: 'title',
    label: 'Title',
    type: 'text',
    required: true,
    colSpan: 2,
    section: 'basic'
  },

  {
    name: 'content',
    label: 'Content',
    type: 'textarea',
    required: true,
    colSpan: 2,
    section: 'basic'
  },
  {
    name: 'target_type',
    label: 'Target Audience',
    type: 'select',
    section: 'target',

    options: [
      {
        label: 'All Users',
        value: 'all_users'
      },

      {
        label: 'Students',
        value: 'all_students'
      },

      {
        label: 'Teachers',
        value: 'all_teachers'
      },

      {
        label: 'Staff',
        value: 'all_staffs'
      },

      // {
      //   label: 'Course Students',
      //   value: 'course'
      // },

      {
        label: 'Selected Users',
        value: 'selected_users'
      }
    ]
  },

  {
    name: 'course_id',
    label: 'Course',
    type: 'select',
    section: 'target',

    dependsOn: 'target_type',
    dependsValue: 'course'
  },

  {
    name: 'user_ids',
    label: 'Users',
    type: 'multi-select',
    section: 'target',

    dependsOn: 'target_type',
    dependsValue: 'selected_users'
  },

  {
    name: 'start_date',
    label: 'Start Date',
    type: 'datetime-local',
    section: 'schedule'
  },

  {
    name: 'end_date',
    label: 'End Date',
    type: 'datetime-local',
    section: 'schedule'
  },

  {
    name: 'priority',
    label: 'Priority',
    type: 'select',
    section: 'meta',

    options: [
      {
        label: 'Low',
        value: 'low'
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

  {
    name: 'status',
    label: 'Status',
    type: 'select',
    section: 'meta',

    options: [
      {
        label: 'Draft',
        value: 'draft'
      },

      {
        label: 'Published',
        value: 'published'
      },

      {
        label: 'Archived',
        value: 'archived'
      }
    ]
  },
  {
    name: 'position',
    label: 'Position',
    type: 'number',
    section: 'meta',
  },

  {
    name: 'is_pinned',
    label: 'Is Pin',
    type: 'toggle',
    section: 'meta',
  }
]