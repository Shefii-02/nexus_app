const AnnouncementDetails = ({
  announcement,
}: {
  announcement: any
}) => {
  return (
    <div className="bg-white rounded-xl border shadow-sm">

      <div className="border-b p-6">
        <h1 className="text-2xl font-bold">
          {announcement.title}
        </h1>

        <div className="flex gap-3 mt-3">
          <span className="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
            {announcement.target_type}
          </span>

          <span className="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">
            {announcement.priority}
          </span>

          <span
            className={`px-2 py-1 rounded-full text-xs ${
              announcement.status ===
              'active'
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700'
            }`}
          >
            {announcement.status}
          </span>
        </div>
      </div>

      <div className="p-6">
        <div className="mb-6">
          <label className="font-medium">
            Content
          </label>

          <div className="mt-2 whitespace-pre-wrap text-gray-700">
            {announcement.content}
          </div>
        </div>

        <div className="grid md:grid-cols-2 gap-6">

          <div>
            <label className="font-medium">
              Start Date
            </label>

            <div className="mt-1 text-gray-700">
              {announcement.start_date}
            </div>
          </div>

          <div>
            <label className="font-medium">
              End Date
            </label>

            <div className="mt-1 text-gray-700">
              {announcement.end_date}
            </div>
          </div>

          <div>
            <label className="font-medium">
              Created At
            </label>

            <div className="mt-1 text-gray-700">
              {announcement.created_at}
            </div>
          </div>

          <div>
            <label className="font-medium">
              Updated At
            </label>

            <div className="mt-1 text-gray-700">
              {announcement.updated_at}
            </div>
          </div>

        </div>
      </div>
    </div>
  )
}

export default AnnouncementDetails