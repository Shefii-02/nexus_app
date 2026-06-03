export const courseFormConfig = [
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

    /** ========================
     * BASIC INFO
     ======================== */
    {
        name: 'code',
        label: 'Course Code',
        type: 'text',
        required: true,
        section: 'basic',
    },
    {
        name: 'name',
        label: 'Course Name',
        type: 'text',
        required: true,
        section: 'basic',
    },
    {
        name: 'description',
        label: 'Description',
        type: 'textarea',
        section: 'basic',
        colSpan: 2, // full width
    },

    /** ========================
     * SCHEDULE
     ======================== */
    {
        name: 'started_at',
        label: 'Start Date',
        type: 'date',
        section: 'schedule',
    },
    {
        name: 'ended_at',
        label: 'End Date',
        type: 'date',
        section: 'schedule',
    },
    {
        name: 'duration_days',
        label: 'Total Class (Per Month/Total)',
        type: 'number',
        section: 'schedule',
        dependsOn: 'is_renewal',
        dependsValue: 1, // show only if YES
    },

    /** ========================
     * PRICING
     ======================== */
    {
        name: 'actual_price',
        label: 'Actual Price',
        type: 'number',
        section: 'pricing',
    },
    {
        name: 'net_price',
        label: 'Net Price',
        type: 'number',
        section: 'pricing',
    },
    {
        name: 'coupon_available',
        label: 'Coupon Available',
        type: 'radio',
        section: 'pricing',
        options: [
            { label: 'Yes', value: 1, default: false },
            { label: 'No', value: 0, default: true },
        ],
    },
    {
        name: 'is_renewal',
        label: 'Renewal',
        type: 'radio',
        section: 'pricing',
        options: [
            { label: 'Yes', value: 1, default: false },
            { label: 'No', value: 0, default: true },
        ],
    },

    /** ========================
     * CLASS SETTINGS
     ======================== */
    {
        name: 'class_type',
        label: 'Class Type',
        type: 'select',
        section: 'class',
        required: true,
        options: [
            { label: 'Online', value: 'online' },
            { label: 'Offline', value: 'offline' },
            { label: 'Hybrid', value: 'hybrid' },
        ],
    },

    {
        name: 'teacher_id',
        label: 'Teacher',
        type: 'select',
        section: 'class',
        options: [], // 🔥 dynamic (inject in page)
    },

    /** ========================
     * FEES
     ======================== */
    {
        name: 'fee_type',
        label: 'Fee Type',
        type: 'select',
        section: 'pricing',
        options: [
            { label: 'Monthly', value: 'monthly' },
            { label: 'One Time', value: 'one_time' },
        ],
    },

    /** ========================
     * STATUS
     ======================== */
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        required: true,
        section: 'meta',
        options: [
            { label: 'Active', value: 'active' },
            { label: 'Inactive', value: 'inactive' },
            { label: 'Archived', value: 'archived' },
        ],
    },
]