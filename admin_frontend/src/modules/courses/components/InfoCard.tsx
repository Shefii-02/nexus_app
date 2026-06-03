const InfoCard = ({ label, value }: any) => (
  <div className="bg-white p-4 rounded-xl shadow-sm">
    <p className="text-xs text-gray-500">{label}</p>
    <p className="font-semibold">{value}</p>
  </div>
)

export default InfoCard;