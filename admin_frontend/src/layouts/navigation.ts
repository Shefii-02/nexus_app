export type NavChild = {
  label: string
  path: string
}

export type NavItem =
  | { label: string; path: string }
  | { label: string; children: NavChild[] }

export type NavSection = {
  title: string
  items: NavItem[]
}

export const navSections: NavSection[] = [

  {
    title: 'General',
    items: [
      {
        label: 'Dashboard',
        path: '/',
      },
    ],
  },

  {
    title: 'Users',
    items: [
      {
        label: 'Teachers',
        path: '/teachers',
      },
      {
        label: 'Students',
        path: '/students',
      },
      {
        label: 'Staff User',
        path: '/staff',
      },

    ],
  },

  {
    title: 'Academic',
    items: [
      {
        label: 'Courses',
        path: '/courses',
      },
       {
        label: 'Chat',
        path: '/chats',
      },

      {
        label: 'Admissions',
        children: [
          {
            label: 'New Admission',
            path: '/admissions/create',
          },
          {
            label: 'Admission List',
            path: '/admissions',
          },
          {
            label: 'Renewal Due',
            path: '/renewals/due',
          },
          {
            label: 'Renewal History',
            path: '/renewals',
          },
        ],
      },

      // {
      //   label: 'Attendance',
      //   children: [
      //     {
      //       label: 'Student Attendance',
      //       path: '/attendance/students',
      //     },
      //     {
      //       label: 'Teacher Attendance',
      //       path: '/attendance/teachers',
      //     },
      //     {
      //       label: 'Staff Attendance',
      //       path: '/attendance/staff',
      //     },
      //     {
      //       label: 'Leave Requests',
      //       path: '/leave-requests',
      //     },
      //   ],
      // },
    ],


  },

  {
    title: 'Finance',
    items: [

      
      {
        label: 'Payments',
        children: [
          {
            label: 'Create Payment',
            path: '/payments/create',
          },
          {
            label: 'All Transactions',
            path: '/transactions',
          },
          // {
          //   label: 'Income',
          //   path: '/transactions/income',
          // },
          // {
          //   label: 'Expenses',
          //   path: '/transactions/expenses',
          // },
          // {
          //   label: 'Refunds',
          //   path: '/transactions/refunds',
          // },
        ],
      },
      {
        label: 'Teacher Payments',
        path: '/',
      },
      {
        label: 'Staff Payments',
        path: '/',
      },

      // {
      //   label: 'Teacher Payments',
      //   children: [
      //     {
      //       label: 'Pending',
      //       path: '/teacher-payments/pending',
      //     },
      //     {
      //       label: 'Release',
      //       path: '/teacher-payments/release',
      //     },
      //     {
      //       label: 'History',
      //       path: '/teacher-payments/history',
      //     },
      //   ],
      // },

      // {
      //   label: 'Staff Payments',
      //   children: [
      //     {
      //       label: 'Pending',
      //       path: '/staff-payments/pending',
      //     },
      //     {
      //       label: 'Release',
      //       path: '/staff-payments/release',
      //     },
      //     {
      //       label: 'History',
      //       path: '/staff-payments/history',
      //     },
      //   ],
      // },

      // {
      //   label: 'Coupons',
      //   children: [
      //     {
      //       label: 'All Coupons',
      //       path: '/coupons',
      //     },
      //     {
      //       label: 'Create Coupon',
      //       path: '/coupons/create',
      //     },
      //     {
      //       label: 'Usage History',
      //       path: '/coupons/history',
      //     },
      //   ],
      // },
    ],


  },
  {
    title: 'Communication',
    items: [
      {
        label: 'Announcements',
        children: [
          {
            label: 'All Announcements',
            path: '/announcements',
          },
          {
            label: 'Send announcent',
            path: '/announcements/create',
          },

          // {
          //   label: 'Delivery Logs',
          //   path: '/notifications/create',
          // },
          // {
          //   label: 'Failed Deliveried',
          //   path: '/notifications/create',
          // },
        ],
      },
      {
        label: 'Notifications',
        children: [
          {
            label: 'All Notifications',
            path: '/notifications',
          },
          {
            label: 'Send Notification',
            path: '/notifications/create',
          },

          // {
          //   label: 'Delivery Logs',
          //   path: '/notifications/create',
          // },
          // {
          //   label: 'Failed Notifications',
          //   path: '/notifications/create',
          // },

        ],
      },
    ],
  },

  {
    title: 'Masters',
    items: [
      // {
        // label: 'System',
        // children: [
          // {
          //   label: 'Activity Logs',
          //   path: '/announcements',
          // },
          // {
          //   label: 'Login Logs',
          //   path: '/announcements/create',
          // },
          // {
          //   label: 'Audit Logs',
          //   path: '/announcements/create',
          // },

      //   ],
      // },
      {
        label: 'Settings',
        children: [
          // {
          //   label: 'General',
          //   path: '/notifications',
          // },
          {
            label: 'Roles',
            path: '/roles',
          },
          // {
          //   label: 'Notification Settings',
          //   path: '/notifications/create',
          // },
          // {
          //   label: 'Attendance Settings',
          //   path: '/notifications/create',
          // },

          // {
          //   label: 'Coupon Settings',
          //   path: '/notifications/create',

          // },

          // {
          //   label: 'Report Settings',
          //   path: '/notifications/create',

          // },

          // {
          //   label: 'Coupon Types',
          //   path: '/notifications/create',

          // },

          // {
          //   label: 'Leave Types',
          //   path: '/notifications/create',

          // },

          // {
          //   label: 'Payment Types',
          //   path: '/notifications/create',

          // },

          // {
          //   label: 'Categories / Sub Categroies',
          //   path: '/notifications/create',

          // },


        ],
      },
      // {
      //   label: 'Reports',
      //   children: [

      //     {
      //       label: 'Revenue Reports',
      //       path: '/reports/revenue',
      //     },
      //     {
      //       label: 'Profit Reports',
      //       path: '/reports/profit',
      //     },
      //     {
      //       label: 'Teacher Earnings',
      //       path: '/reports/teacher-earnings',
      //     },
      //     {
      //       label: 'Staff Salary',
      //       path: '/reports/staff-salary',
      //     },
      //     {
      //       label: 'Attendance Reports',
      //       path: '/reports/attendance',
      //     },
      //     {
      //       label: 'Admission Reports',
      //       path: '/reports/admissions',
      //     },
      //     {
      //       label: 'Renewal Reports',
      //       path: '/reports/renewals',
      //     },
      //     {
      //       label: 'Coupon Reports',
      //       path: '/reports/coupons',
      //     },
      //   ]
      // }
    ],
  },

]
