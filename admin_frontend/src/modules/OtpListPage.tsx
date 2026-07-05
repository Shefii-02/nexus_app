import { useEffect, useState } from "react";
import apiClient from "../services/apiClient";

const OtpListPage = () => {
  const [logs, setLogs] = useState("");
  const [loading, setLoading] = useState(true);
  const [clearing, setClearing] = useState(false);

  useEffect(() => {
    fetchLogs();
  }, []);

  const fetchLogs = async () => {
    setLoading(true);

    try {
      const { data } = await apiClient.get("/otp-usages");

      if (data.success) {
        setLogs(data.logs);
      } else {
        setLogs(data.message);
      }
    } catch (error) {
      console.error(error);
      setLogs("Unable to load log file.");
    } finally {
      setLoading(false);
    }
  };


  if (loading) {
    return <h3>Loading...</h3>;
  }

  return (
    <div style={{ padding: 20 }}>
      <div
        style={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          marginBottom: 20,
        }}
      >
        <h2>Laravel Log Viewer</h2>

        <button
          onClick={clearLog}
          disabled={clearing}
          style={{
            padding: "10px 20px",
            background: "#dc3545",
            color: "#fff",
            border: "none",
            borderRadius: 6,
            cursor: "pointer",
          }}
        >
          {clearing ? "Clearing..." : "Clear Log"}
        </button>
      </div>

      <pre
        style={{
          background: "#1e1e1e",
          color: "#00ff66",
          padding: 20,
          borderRadius: 8,
          whiteSpace: "pre-wrap",
          overflowX: "auto",
          maxHeight: "80vh",
          overflowY: "auto",
        }}
      >
        {logs}
      </pre>
    </div>
  );
};

export default OtpListPage;