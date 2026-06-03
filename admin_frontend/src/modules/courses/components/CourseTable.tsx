
const CourseTable = ({ courses, loading, onEdit, onView, onDelete }: any) => {
  return (
    <table className="w-full bg-white">
      <thead>
        <tr>
          <th>Name</th>
          <th>Code</th>
          <th>Price</th>
          <th>Status</th>
          <th />
        </tr>
      </thead>

      <tbody>
        {courses.map((c: any) => (
          <tr key={c.id}>
            <td>{c.name}</td>
            <td>{c.code}</td>
            <td>₹{c.net_price}</td>
            <td>{c.status}</td>

            <td>
              <button onClick={() => onView(c.id)}>View</button>
              <button onClick={() => onEdit(c.id)}>Edit</button>
              <button onClick={() => onDelete(c.id)}>Delete</button>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  )
}

export default CourseTable
