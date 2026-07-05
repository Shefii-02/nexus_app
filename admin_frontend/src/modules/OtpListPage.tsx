import { useEffect, useState } from "react";
import apiClient from "../services/apiClient";

interface OtpVerification {
  id: number;
  email: string | null;
  phone: string;
  type: string;
  device_id: string;
  is_used: boolean;
  otp_code: string;
  expires_at: string;
  verified_at: string | null;
  created_at: string;
}

const OtpListPage = () => {
  const [otpList, setOtpList] = useState<OtpVerification[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchOtpList();
  }, []);

  const fetchOtpList = async () => {
    setLoading(true);

    try {
      const { data } = await apiClient.get("/otp-usages");

      if (data.success) {
        setOtpList(data.data);
      }
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <h3>Loading...</h3>;
  }

  return (
    <div style={{ padding: 20 }}>
      <h2>OTP Usage List</h2>

      <table
        style={{
          width: "100%",
          borderCollapse: "collapse",
          marginTop: 20,
        }}
      >
        <thead>
          <tr style={{ background: "#f5f5f5" }}>
            <th style={th}>ID</th>
            <th style={th}>Phone</th>
            <th style={th}>OTP</th>
            <th style={th}>Used</th>
            <th style={th}>Verified</th>
            <th style={th}>Expires</th>
            <th style={th}>Created</th>
          </tr>
        </thead>

        <tbody>
          {otpList.map((otp) => (
            <tr key={otp.id}>
              <td style={td}>{otp.id}</td>
              <td style={td}>{otp.phone}</td>
              <td style={td}>{otp.otp_code}</td>
              <td style={td}>
                {otp.is_used ? (
                  <span style={{ color: "green" }}>Yes</span>
                ) : (
                  <span style={{ color: "red" }}>No</span>
                )}
              </td>
              <td style={td}>
                {otp.verified_at
                  ? new Date(otp.verified_at).toLocaleString()
                  : "-"}
              </td>
              <td style={td}>
                {new Date(otp.expires_at).toLocaleString()}
              </td>
              <td style={td}>
                {new Date(otp.created_at).toLocaleString()}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

const th: React.CSSProperties = {
  border: "1px solid #ddd",
  padding: 10,
  textAlign: "left",
};

const td: React.CSSProperties = {
  border: "1px solid #ddd",
  padding: 10,
};

export default OtpListPage;