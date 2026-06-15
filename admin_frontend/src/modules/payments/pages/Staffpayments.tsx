import { useState, useEffect, useCallback } from "react";

const STATUS_BADGE = {
  pending:  { bg: "#fef3c7", color: "#d97706", label: "Pending"  },
  released: { bg: "#dcfce7", color: "#16a34a", label: "Released" },
  paid:     { bg: "#dbeafe", color: "#2563eb", label: "Paid"     },
};

const fmt     = (n) => "₹" + parseFloat(n || 0).toLocaleString("en-IN", { minimumFractionDigits: 2 });
const fmtDate = (d) => d ? new Date(d).toLocaleDateString("en-IN", { day: "2-digit", month: "short", year: "numeric" }) : "—";

const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

function ReleaseModal({ payment, onClose, onDone }) {
  const [form, setForm]   = useState({ payment_method: "cash", transaction_no: "", remarks: "" });
  const [loading, setLoading] = useState(false);

  const handleRelease = async () => {
    setLoading(true);
    try {
      await fetch(`/api/staff-payments/${payment.id}/release`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        body: JSON.stringify(form),
      });
      onDone?.();
      onClose();
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="sp-overlay" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="sp-modal">
        <div className="sp-modal-header">
          <h3>Release Staff Salary</h3>
          <button onClick={onClose} className="sp-close">×</button>
        </div>
        <div className="sp-modal-body">
          <div className="sp-info-row">
            <span>Staff</span><strong>{payment.staff?.name || `#${payment.staff_id}`}</strong>
          </div>
          <div className="sp-info-row">
            <span>Salary Month</span>
            <strong>{payment.salary_month ? new Date(payment.salary_month + "-01").toLocaleDateString("en-IN", { month: "long", year: "numeric" }) : "—"}</strong>
          </div>
          <div className="sp-info-row">
            <span>Final Amount</span>
            <strong style={{ color: "#16a34a", fontSize: 16 }}>{fmt(payment.final_amount)}</strong>
          </div>
          <div className="sp-field">
            <label>Payment Method</label>
            <select value={form.payment_method} onChange={(e) => setForm((f) => ({ ...f, payment_method: e.target.value }))}>
              {["cash","bank_transfer","cheque","online","other"].map((m) => (
                <option key={m} value={m}>{m.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase())}</option>
              ))}
            </select>
          </div>
          <div className="sp-field">
            <label>Transaction No</label>
            <input type="text" placeholder="Optional" value={form.transaction_no}
              onChange={(e) => setForm((f) => ({ ...f, transaction_no: e.target.value }))} />
          </div>
          <div className="sp-field">
            <label>Remarks</label>
            <textarea rows={2} value={form.remarks}
              onChange={(e) => setForm((f) => ({ ...f, remarks: e.target.value }))} />
          </div>
        </div>
        <div className="sp-modal-footer">
          <button className="sp-btn-sec" onClick={onClose}>Cancel</button>
          <button className="sp-btn-pri" onClick={handleRelease} disabled={loading}>
            {loading ? "Processing…" : "Release Salary"}
          </button>
        </div>
      </div>
    </div>
  );
}

const TABS = [
  { key: "pending",  label: "Pending",  icon: "⏳" },
  { key: "released", label: "Released", icon: "✅" },
  { key: "history",  label: "History",  icon: "📋" },
];

export default function StaffPayments() {
  const [tab, setTab]           = useState("pending");
  const [rows, setRows]         = useState([]);
  const [meta, setMeta]         = useState({ total: 0, current_page: 1, last_page: 1 });
  const [loading, setLoading]   = useState(false);
  const [search, setSearch]     = useState("");
  const [monthFilter, setMonthFilter] = useState("");
  const [page, setPage]         = useState(1);
  const [releasing, setReleasing] = useState(null);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams(
        Object.entries({ status: tab, search, month: monthFilter, page, per_page: 15 })
          .filter(([, v]) => v !== "")
      );
      const res = await fetch(`/api/staff-payments?${params}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });
      const data = await res.json();
      setRows(data.data);
      setMeta({ total: data.total, current_page: data.current_page, last_page: data.last_page });
    } finally {
      setLoading(false);
    }
  }, [tab, search, monthFilter, page]);

  useEffect(() => { fetchData(); }, [fetchData]);
  useEffect(() => { setPage(1); }, [tab, search, monthFilter]);

  const currentYear = new Date().getFullYear();
  const monthOptions = [];
  for (let y = currentYear; y >= currentYear - 2; y--) {
    for (let m = 11; m >= 0; m--) {
      monthOptions.push({ value: `${y}-${String(m + 1).padStart(2, "0")}`, label: `${MONTHS[m]} ${y}` });
    }
  }

  return (
    <div className="sp-wrap">
      <div className="sp-topbar">
        <div>
          <h1 className="sp-title">Staff Payments</h1>
          <p className="sp-sub">{meta.total} records</p>
        </div>
      </div>

      <div className="sp-tabs">
        {TABS.map((t) => (
          <button key={t.key} className={`sp-tab ${tab === t.key ? "active" : ""}`} onClick={() => setTab(t.key)}>
            <span>{t.icon}</span> {t.label}
          </button>
        ))}
      </div>

      <div className="sp-toolbar">
        <input
          className="sp-search"
          placeholder="Search staff name…"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
        <select className="sp-month-sel" value={monthFilter} onChange={(e) => setMonthFilter(e.target.value)}>
          <option value="">All Months</option>
          {monthOptions.map((m) => (
            <option key={m.value} value={m.value}>{m.label}</option>
          ))}
        </select>
      </div>

      <div className="sp-table-wrap">
        {loading ? (
          <div className="sp-loading">Loading…</div>
        ) : (
          <table className="sp-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Staff Member</th>
                <th>Salary Month</th>
                <th>Base Salary</th>
                <th>Bonus</th>
                <th>Deduction</th>
                <th>Final Amount</th>
                <th>Method</th>
                {tab !== "pending" && <th>Paid On</th>}
                <th>Status</th>
                {tab === "pending" && <th>Action</th>}
              </tr>
            </thead>
            <tbody>
              {rows.length === 0 ? (
                <tr>
                  <td colSpan={tab === "pending" ? 10 : 9} className="sp-empty">
                    No {tab} staff payments found.
                  </td>
                </tr>
              ) : rows.map((r, i) => {
                const b = STATUS_BADGE[r.status] || STATUS_BADGE.pending;
                const salaryMonthLabel = r.salary_month
                  ? new Date(r.salary_month + "-01").toLocaleDateString("en-IN", { month: "short", year: "numeric" })
                  : "—";
                return (
                  <tr key={r.id}>
                    <td className="sp-muted">{(page - 1) * 15 + i + 1}</td>
                    <td><strong>{r.staff?.name || `Staff #${r.staff_id}`}</strong></td>
                    <td>{salaryMonthLabel}</td>
                    <td>{fmt(r.salary_amount)}</td>
                    <td style={{ color: "#16a34a" }}>
                      {r.bonus_amount > 0 ? `+${fmt(r.bonus_amount)}` : "—"}
                    </td>
                    <td style={{ color: "#dc2626" }}>
                      {r.deduction_amount > 0 ? (
                        <span title={r.deduction_reason || ""}>-{fmt(r.deduction_amount)}</span>
                      ) : "—"}
                    </td>
                    <td style={{ fontWeight: 700, color: "#16a34a" }}>{fmt(r.final_amount)}</td>
                    <td className="sp-method">{r.payment_method?.replace(/_/g, " ") || "—"}</td>
                    {tab !== "pending" && <td>{fmtDate(r.paid_at)}</td>}
                    <td>
                      <span className="sp-badge" style={{ background: b.bg, color: b.color }}>{b.label}</span>
                    </td>
                    {tab === "pending" && (
                      <td>
                        <button className="sp-btn-release" onClick={() => setReleasing(r)}>
                          Release
                        </button>
                      </td>
                    )}
                  </tr>
                );
              })}
            </tbody>
          </table>
        )}
      </div>

      <div className="sp-pager">
        <span>Page {meta.current_page} of {meta.last_page}</span>
        <div className="sp-pager-btns">
          <button disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>‹ Prev</button>
          <button disabled={page >= meta.last_page} onClick={() => setPage((p) => p + 1)}>Next ›</button>
        </div>
      </div>

      {releasing && (
        <ReleaseModal payment={releasing} onClose={() => setReleasing(null)} onDone={fetchData} />
      )}

      <style>{`
        .sp-wrap { padding: 24px; max-width: 1200px; }
        .sp-topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .sp-title { margin: 0; font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .sp-sub { margin: 4px 0 0; font-size: 13px; color: #888; }
        .sp-tabs { display: flex; gap: 0; margin-bottom: 20px; border-bottom: 2px solid #e4e4e4; }
        .sp-tab {
          padding: 10px 22px; background: none; border: none; cursor: pointer;
          font-size: 14px; color: #888; border-bottom: 2px solid transparent;
          margin-bottom: -2px; display: flex; align-items: center; gap: 6px; transition: all .15s;
        }
        .sp-tab.active { color: #4f6ef7; border-bottom-color: #4f6ef7; font-weight: 600; }
        .sp-toolbar { display: flex; gap: 10px; margin-bottom: 14px; }
        .sp-search, .sp-month-sel {
          padding: 9px 14px; border: 1.5px solid #e4e4e4; border-radius: 8px;
          font-size: 13.5px; outline: none; background: #fff;
        }
        .sp-search { width: 260px; }
        .sp-table-wrap {
          background: #fff; border-radius: 10px; overflow: hidden;
          box-shadow: 0 1px 4px rgba(0,0,0,.07); overflow-x: auto;
        }
        .sp-loading, .sp-empty { text-align: center; padding: 48px; color: #aaa; font-size: 14px; }
        .sp-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .sp-table thead tr { background: #f7f8fc; }
        .sp-table th {
          padding: 12px 14px; text-align: left; font-size: 12px; font-weight: 600;
          color: #888; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap;
        }
        .sp-table td { padding: 11px 14px; border-top: 1px solid #f0f0f0; }
        .sp-table tbody tr:hover { background: #fafbff; }
        .sp-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .sp-muted { color: #bbb; font-size: 12px; }
        .sp-method { text-transform: capitalize; color: #666; font-size: 13px; }
        .sp-btn-release {
          padding: 6px 14px; background: #4f6ef7; color: #fff; border: none;
          border-radius: 6px; cursor: pointer; font-size: 12.5px; font-weight: 600;
        }
        .sp-btn-release:hover { background: #3a5ce4; }
        .sp-pager {
          display: flex; align-items: center; justify-content: space-between;
          padding: 14px 0; font-size: 13px; color: #888;
        }
        .sp-pager-btns { display: flex; gap: 8px; }
        .sp-pager-btns button {
          padding: 7px 16px; border: 1.5px solid #e4e4e4; border-radius: 7px;
          background: #fff; cursor: pointer; font-size: 13px;
        }
        .sp-pager-btns button:disabled { opacity: .4; cursor: not-allowed; }
        /* Modal */
        .sp-overlay {
          position: fixed; inset: 0; background: rgba(0,0,0,.45);
          display: flex; align-items: center; justify-content: center; z-index: 1000;
        }
        .sp-modal {
          background: #fff; border-radius: 12px; width: 420px; max-width: 95vw;
          box-shadow: 0 20px 60px rgba(0,0,0,.18); overflow: hidden;
        }
        .sp-modal-header {
          display: flex; justify-content: space-between; align-items: center;
          padding: 18px 22px 14px; border-bottom: 1px solid #f0f0f0;
        }
        .sp-modal-header h3 { margin: 0; font-size: 16px; font-weight: 600; color: #1a1a2e; }
        .sp-close { background: none; border: none; font-size: 22px; cursor: pointer; color: #888; }
        .sp-modal-body { padding: 18px 22px; display: flex; flex-direction: column; gap: 14px; }
        .sp-info-row {
          display: flex; justify-content: space-between; align-items: center;
          padding: 8px 12px; background: #f7f8fc; border-radius: 8px; font-size: 13.5px;
        }
        .sp-info-row span { color: #888; }
        .sp-field { display: flex; flex-direction: column; gap: 5px; }
        .sp-field label { font-size: 12.5px; font-weight: 500; color: #444; }
        .sp-field input, .sp-field select, .sp-field textarea {
          border: 1.5px solid #e4e4e4; border-radius: 8px; padding: 9px 12px;
          font-size: 13.5px; outline: none; font-family: inherit; background: #fff;
        }
        .sp-modal-footer {
          display: flex; gap: 10px; justify-content: flex-end;
          padding: 14px 22px; border-top: 1px solid #f0f0f0;
        }
        .sp-btn-sec {
          padding: 9px 18px; border: 1.5px solid #e0e0e0; border-radius: 8px;
          background: #fff; cursor: pointer; font-size: 13.5px; color: #555;
        }
        .sp-btn-pri {
          padding: 9px 22px; border: none; border-radius: 8px; background: #4f6ef7;
          color: #fff; cursor: pointer; font-size: 13.5px; font-weight: 600;
        }
        .sp-btn-pri:disabled { background: #a0aec0; cursor: not-allowed; }
      `}</style>
    </div>
  );
}