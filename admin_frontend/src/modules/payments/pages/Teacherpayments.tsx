import { useState, useEffect, useCallback } from "react";

const STATUS_BADGE = {
  pending:  { bg: "#fef3c7", color: "#d97706", label: "Pending"  },
  released: { bg: "#dcfce7", color: "#16a34a", label: "Released" },
  paid:     { bg: "#dbeafe", color: "#2563eb", label: "Paid"     },
};

const fmt      = (n) => "₹" + parseFloat(n || 0).toLocaleString("en-IN", { minimumFractionDigits: 2 });
const fmtDate  = (d) => d ? new Date(d).toLocaleDateString("en-IN", { day: "2-digit", month: "short", year: "numeric" }) : "—";
const fmtRange = (s, e) => s && e ? `${fmtDate(s)} – ${fmtDate(e)}` : "—";

const TABS = [
  { key: "pending",  label: "Pending",  icon: "⏳" },
  { key: "released", label: "Released", icon: "✅" },
  { key: "history",  label: "History",  icon: "📋" },
];

/* ──────────────────────────────────────────────────────────
   Release Payment Modal
────────────────────────────────────────────────────────── */
function ReleaseModal({ payment, onClose, onDone }) {
  const [form, setForm]   = useState({ payment_method: "cash", payment_reference: "", remarks: "" });
  const [loading, setLoading] = useState(false);

  const handleRelease = async () => {
    setLoading(true);
    try {
      await fetch(`/api/teacher-payments/${payment.id}/release`, {
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
    <div className="tp-overlay" onClick={(e) => e.target === e.currentTarget && onClose()}>
      <div className="tp-modal">
        <div className="tp-modal-header">
          <h3>Release Payment</h3>
          <button onClick={onClose} className="tp-close">×</button>
        </div>
        <div className="tp-modal-body">
          <div className="tp-info-row">
            <span>Teacher</span><strong>{payment.teacher?.name || `#${payment.teacher_id}`}</strong>
          </div>
          <div className="tp-info-row">
            <span>Period</span><strong>{fmtRange(payment.period_start, payment.period_end)}</strong>
          </div>
          <div className="tp-info-row">
            <span>Net Amount</span>
            <strong style={{ color: "#16a34a", fontSize: 16 }}>{fmt(payment.amount)}</strong>
          </div>

          <div className="tp-field">
            <label>Payment Method</label>
            <select value={form.payment_method} onChange={(e) => setForm((f) => ({ ...f, payment_method: e.target.value }))}>
              {["cash","bank_transfer","cheque","online","other"].map((m) => (
                <option key={m} value={m}>{m.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase())}</option>
              ))}
            </select>
          </div>
          <div className="tp-field">
            <label>Reference / Transaction No</label>
            <input type="text" placeholder="Optional" value={form.payment_reference}
              onChange={(e) => setForm((f) => ({ ...f, payment_reference: e.target.value }))} />
          </div>
          <div className="tp-field">
            <label>Remarks</label>
            <textarea rows={2} value={form.remarks}
              onChange={(e) => setForm((f) => ({ ...f, remarks: e.target.value }))} />
          </div>
        </div>
        <div className="tp-modal-footer">
          <button className="tp-btn-secondary" onClick={onClose}>Cancel</button>
          <button className="tp-btn-primary" onClick={handleRelease} disabled={loading}>
            {loading ? "Processing…" : "Release Payment"}
          </button>
        </div>
      </div>
    </div>
  );
}

/* ──────────────────────────────────────────────────────────
   Main Page
────────────────────────────────────────────────────────── */
export default function TeacherPayments() {
  const [tab, setTab]           = useState("pending");
  const [rows, setRows]         = useState([]);
  const [meta, setMeta]         = useState({ total: 0, current_page: 1, last_page: 1 });
  const [loading, setLoading]   = useState(false);
  const [search, setSearch]     = useState("");
  const [page, setPage]         = useState(1);
  const [releasing, setReleasing] = useState(null); // payment object

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({ status: tab, search, page, per_page: 15 });
      const res = await fetch(`/api/teacher-payments?${params}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });
      const data = await res.json();
      setRows(data.data);
      setMeta({ total: data.total, current_page: data.current_page, last_page: data.last_page });
    } finally {
      setLoading(false);
    }
  }, [tab, search, page]);

  useEffect(() => { fetchData(); }, [fetchData]);
  useEffect(() => { setPage(1); }, [tab, search]);

  return (
    <div className="tp-wrap">
      <div className="tp-topbar">
        <div>
          <h1 className="tp-title">Teacher Payments</h1>
          <p className="tp-sub">{meta.total} records</p>
        </div>
      </div>

      {/* Tabs */}
      <div className="tp-tabs">
        {TABS.map((t) => (
          <button
            key={t.key}
            className={`tp-tab ${tab === t.key ? "active" : ""}`}
            onClick={() => setTab(t.key)}
          >
            <span className="tp-tab-icon">{t.icon}</span> {t.label}
          </button>
        ))}
      </div>

      {/* Search */}
      <div className="tp-toolbar">
        <input
          className="tp-search"
          placeholder="Search teacher name, transaction no…"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
      </div>

      {/* Table */}
      <div className="tp-table-wrap">
        {loading ? (
          <div className="tp-loading">Loading…</div>
        ) : (
          <table className="tp-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Teacher</th>
                <th>Period</th>
                <th>Classes</th>
                <th>Gross</th>
                <th>Deduction</th>
                <th>Net Amount</th>
                <th>Method</th>
                {tab !== "pending" && <th>Paid On</th>}
                <th>Status</th>
                {tab === "pending" && <th>Action</th>}
              </tr>
            </thead>
            <tbody>
              {rows.length === 0 ? (
                <tr>
                  <td colSpan={tab === "pending" ? 10 : 9} className="tp-empty">
                    No {tab} payments found.
                  </td>
                </tr>
              ) : rows.map((r, i) => {
                const b = STATUS_BADGE[r.status] || STATUS_BADGE.pending;
                return (
                  <tr key={r.id}>
                    <td className="tp-muted">{(page - 1) * 15 + i + 1}</td>
                    <td><strong>{r.teacher?.name || `Teacher #${r.teacher_id}`}</strong></td>
                    <td className="tp-period">{fmtRange(r.period_start, r.period_end)}</td>
                    <td className="tp-center">{r.total_classes ?? "—"}</td>
                    <td>{fmt(r.gross_amount)}</td>
                    <td className="tp-deduct">
                      {r.deduction_amount > 0 ? (
                        <span title={r.deduction_reason || ""}>-{fmt(r.deduction_amount)}</span>
                      ) : "—"}
                    </td>
                    <td style={{ fontWeight: 700, color: "#16a34a" }}>{fmt(r.amount)}</td>
                    <td className="tp-method">{r.payment_method?.replace(/_/g, " ") || "—"}</td>
                    {tab !== "pending" && <td>{fmtDate(r.paid_at)}</td>}
                    <td>
                      <span className="tp-badge" style={{ background: b.bg, color: b.color }}>
                        {b.label}
                      </span>
                    </td>
                    {tab === "pending" && (
                      <td>
                        <button
                          className="tp-btn-release"
                          onClick={() => setReleasing(r)}
                        >
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

      {/* Pagination */}
      <div className="tp-pager">
        <span>Page {meta.current_page} of {meta.last_page}</span>
        <div className="tp-pager-btns">
          <button disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>‹ Prev</button>
          <button disabled={page >= meta.last_page} onClick={() => setPage((p) => p + 1)}>Next ›</button>
        </div>
      </div>

      {releasing && (
        <ReleaseModal
          payment={releasing}
          onClose={() => setReleasing(null)}
          onDone={fetchData}
        />
      )}

      <style>{`
        .tp-wrap { padding: 24px; max-width: 1200px; }
        .tp-topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .tp-title { margin: 0; font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .tp-sub { margin: 4px 0 0; font-size: 13px; color: #888; }
        .tp-tabs {
          display: flex; gap: 0; margin-bottom: 20px;
          border-bottom: 2px solid #e4e4e4;
        }
        .tp-tab {
          padding: 10px 22px; background: none; border: none; cursor: pointer;
          font-size: 14px; color: #888; border-bottom: 2px solid transparent;
          margin-bottom: -2px; transition: all .15s; display: flex; align-items: center; gap: 6px;
        }
        .tp-tab.active { color: #4f6ef7; border-bottom-color: #4f6ef7; font-weight: 600; }
        .tp-tab-icon { font-size: 14px; }
        .tp-toolbar { margin-bottom: 14px; }
        .tp-search {
          padding: 9px 14px; border: 1.5px solid #e4e4e4; border-radius: 8px;
          font-size: 13.5px; outline: none; width: 300px;
        }
        .tp-table-wrap {
          background: #fff; border-radius: 10px; overflow: hidden;
          box-shadow: 0 1px 4px rgba(0,0,0,.07); overflow-x: auto;
        }
        .tp-loading, .tp-empty {
          text-align: center; padding: 48px; color: #aaa; font-size: 14px;
        }
        .tp-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .tp-table thead tr { background: #f7f8fc; }
        .tp-table th {
          padding: 12px 14px; text-align: left; font-size: 12px; font-weight: 600;
          color: #888; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap;
        }
        .tp-table td { padding: 11px 14px; border-top: 1px solid #f0f0f0; }
        .tp-table tbody tr:hover { background: #fafbff; }
        .tp-badge {
          display: inline-block; padding: 3px 10px; border-radius: 20px;
          font-size: 12px; font-weight: 600;
        }
        .tp-muted { color: #bbb; font-size: 12px; }
        .tp-period { font-size: 12.5px; color: #555; white-space: nowrap; }
        .tp-center { text-align: center; }
        .tp-deduct { color: #dc2626; font-size: 13px; }
        .tp-method { text-transform: capitalize; color: #666; font-size: 13px; }
        .tp-btn-release {
          padding: 6px 14px; background: #4f6ef7; color: #fff; border: none;
          border-radius: 6px; cursor: pointer; font-size: 12.5px; font-weight: 600;
          white-space: nowrap;
        }
        .tp-btn-release:hover { background: #3a5ce4; }
        .tp-pager {
          display: flex; align-items: center; justify-content: space-between;
          padding: 14px 0; font-size: 13px; color: #888;
        }
        .tp-pager-btns { display: flex; gap: 8px; }
        .tp-pager-btns button {
          padding: 7px 16px; border: 1.5px solid #e4e4e4; border-radius: 7px;
          background: #fff; cursor: pointer; font-size: 13px;
        }
        .tp-pager-btns button:disabled { opacity: .4; cursor: not-allowed; }
        /* Modal */
        .tp-overlay {
          position: fixed; inset: 0; background: rgba(0,0,0,.45);
          display: flex; align-items: center; justify-content: center; z-index: 1000;
        }
        .tp-modal {
          background: #fff; border-radius: 12px; width: 440px; max-width: 95vw;
          box-shadow: 0 20px 60px rgba(0,0,0,.18); overflow: hidden;
        }
        .tp-modal-header {
          display: flex; justify-content: space-between; align-items: center;
          padding: 18px 22px 14px; border-bottom: 1px solid #f0f0f0;
        }
        .tp-modal-header h3 { margin: 0; font-size: 16px; font-weight: 600; color: #1a1a2e; }
        .tp-close { background: none; border: none; font-size: 22px; cursor: pointer; color: #888; }
        .tp-modal-body { padding: 18px 22px; display: flex; flex-direction: column; gap: 14px; }
        .tp-info-row {
          display: flex; justify-content: space-between; align-items: center;
          padding: 8px 12px; background: #f7f8fc; border-radius: 8px; font-size: 13.5px;
        }
        .tp-info-row span { color: #888; }
        .tp-field { display: flex; flex-direction: column; gap: 5px; }
        .tp-field label { font-size: 12.5px; font-weight: 500; color: #444; }
        .tp-field input, .tp-field select, .tp-field textarea {
          border: 1.5px solid #e4e4e4; border-radius: 8px; padding: 9px 12px;
          font-size: 13.5px; outline: none; font-family: inherit; background: #fff;
        }
        .tp-modal-footer {
          display: flex; gap: 10px; justify-content: flex-end;
          padding: 14px 22px; border-top: 1px solid #f0f0f0;
        }
        .tp-btn-secondary {
          padding: 9px 18px; border: 1.5px solid #e0e0e0; border-radius: 8px;
          background: #fff; cursor: pointer; font-size: 13.5px; color: #555;
        }
        .tp-btn-primary {
          padding: 9px 22px; border: none; border-radius: 8px; background: #4f6ef7;
          color: #fff; cursor: pointer; font-size: 13.5px; font-weight: 600;
        }
        .tp-btn-primary:disabled { background: #a0aec0; cursor: not-allowed; }
      `}</style>
    </div>
  );
}