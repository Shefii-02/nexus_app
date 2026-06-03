const MaterialTable = ({ data, onEdit, onView, onDelete }: any) => {
  return (
    <div className="border rounded-xl bg-white">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-gray-50">
            <th className="p-3">Title</th>
            <th className="p-3">Type</th>
            <th className="p-3">Order</th>
            <th className="p-3">Status</th>
            <th className="p-3">Actions</th>
          </tr>
        </thead>

        <tbody>
          {data.map((m: any) => (
            <tr key={m.id} className="border-t">
              <td className="p-3">{m.title}</td>
              <td className="p-3">{m.material_type}</td>
              <td className="p-3">{m.order}</td>
              <td className="p-3">{m.status}</td>

              <td className="p-3 space-x-2">
                <button onClick={() => onView(m.id)}>View</button>
                <button onClick={() => onEdit(m.id)}>Edit</button>
                <button onClick={() => onDelete(m.id)}>Delete</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}

export default MaterialTable