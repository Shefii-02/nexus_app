import { useState, useEffect, useCallback } from "react";
import CreatePayment from "./CreatePaymentPage";

const TYPE_BADGE = {
  income:  { label: "Income",  bg: "#dcfce7", color: "#16a34a" },
  expense: { label: "Expense", bg: "#fee2e2", color: "#dc2626" },
  refund:  { label: "Refund",  bg: "#fef3c7", color: "#d97706" },
};

const fmt = (n) => "₹" + parseFloat(n || 0).toLocaleString("en-IN", { minimumFractionDigits: 2 });
const fmtDate = (d) => d ? new Date(d).toLocaleDateString("en-IN", { day: "2-digit", month: "short", year: "numeric" }) : "—";

export default function AllTransactions() {
  const [rows, setRows]           = useState([]);
  const [meta, setMeta]           = useState({ total: 0, current_page: 1, last_page: 1 });
  const [loading, setLoading]     = useState(false);
  const [showCreate, setShowCreate] = useState(false);
  const [filters, setFilters]     = useState({
    type: "", search: "", from: "", to: "", page: 1, per_page: 15,
  });

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams(
        Object.entries(filters).filter(([, v]) => v !== "")
      );
      const res = await fetch(`/api/transactions?${params}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });
      const data = await res.json();
      setRows(data.data);
      setMeta({ total: data.total, current_page: data.current_page, last_page: data.last_page });
    } finally {
      setLoading(false);
    }
  }, [filters]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const setF = (key, val) => setFilters((f) => ({ ...f, [key]: val, page: 1 }));

  const summary = rows.reduce(
    (acc, r) => {
      if (r.type === "income")  acc.income  += +r.amount;
      if (r.type === "expense") acc.expense += +r.amount;
      if (r.type === "refund")  acc.refund  += +r.amount;
      return acc;
    },
    { income: 0, expense: 0, refund: 0 }
  );

  return (
    <div className="at-wrap">
      {/* Header */}
      <div className="at-topbar">
        <div>
          <h1 className="at-title">All Transactions</h1>
          <p className="at-sub">{meta.total} records</p>
        </div>
        <button className="at-btn-create" onClick={() => setShowCreate(true)}>
          + Create Payment
        </button>
      </div>

      {/* Summary cards */}
      <div className="at-cards">
        {[
          { label: "Total Income",  value: summary.income,  ...TYPE_BADGE.income  },
          { label: "Total Expenses",value: summary.expense, ...TYPE_BADGE.expense },
          { label: "Total Refunds", value: summary.refund,  ...TYPE_BADGE.refund  },
          { label: "Net Balance",
            value: summary.income - summary.expense - summary.refund,
            bg: "#eff6ff", color: "#2563eb" },
        ].map((c) => (
          <div key={c.label} className="at-card" style={{ borderTop: `3px solid ${c.color}` }}>
            <span className="at-card-label">{c.label}</span>
            <span className="at-card-val" style={{ color: c.color }}>{fmt(c.value)}</span>
          </div>
        ))}
      </div>

      {/* Filters */}
      <div className="at-filters">
        <input
          className="at-search"
          placeholder="Search transaction no, description…"
          value={filters.search}
          onChange={(e) => setF("search", e.target.value)}
        />
        <select value={filters.type} onChange={(e) => setF("type", e.target.value)}>
          <option value="">All Types</option>
          <option value="income">Income</option>
          <option value="expense">Expense</option>
          <option value="refund">Refund</option>
        </select>
        <input type="date" value={filters.from} onChange={(e) => setF("from", e.target.value)} />
        <input type="date" value={filters.to}   onChange={(e) => setF("to",   e.target.value)} />
        <button className="at-btn-clear" onClick={() => setFilters({ type: "", search: "", from: "", to: "", page: 1, per_page: 15 })}>
          Clear
        </button>
      </div>

      {/* Table */}
      <div className="at-table-wrap">
        {loading ? (
          <div className="at-loading">Loading…</div>
        ) : (
          <table className="at-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Transaction No</th>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Method</th>
                <th>Description</th>
                <th className="at-num">Amount</th>
              </tr>
            </thead>
            <tbody>
              {rows.length === 0 ? (
                <tr><td colSpan={8} className="at-empty">No transactions found.</td></tr>
              ) : rows.map((r, i) => {
                const b = TYPE_BADGE[r.type] || {};
                return (
                  <tr key={r.id}>
                    <td className="at-muted">{(filters.page - 1) * filters.per_page + i + 1}</td>
                    <td className="at-mono">{r.transaction_no || "—"}</td>
                    <td>{fmtDate(r.transaction_date)}</td>
                    <td>
                      <span className="at-badge" style={{ background: b.bg, color: b.color }}>
                        {b.label}
                      </span>
                    </td>
                    <td>{r.category || "—"}</td>
                    <td className="at-method">{r.payment_method?.replace(/_/g, " ")}</td>
                    <td className="at-desc">{r.description || "—"}</td>
                    <td className="at-num" style={{ color: b.color, fontWeight: 600 }}>
                      {fmt(r.amount)}
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        )}
      </div>

      {/* Pagination */}
      <div className="at-pager">
        <span className="at-pager-info">
          Page {meta.current_page} of {meta.last_page}
        </span>
        <div className="at-pager-btns">
          <button
            disabled={meta.current_page <= 1}
            onClick={() => setFilters((f) => ({ ...f, page: f.page - 1 }))}
          >‹ Prev</button>
          <button
            disabled={meta.current_page >= meta.last_page}
            onClick={() => setFilters((f) => ({ ...f, page: f.page + 1 }))}
          >Next ›</button>
        </div>
      </div>

      {showCreate && (
        <CreatePayment
          onClose={() => setShowCreate(false)}
          onSuccess={() => { fetchData(); }}
        />
      )}

      <style>{`
        .at-wrap { padding: 24px; max-width: 1200px; }
        .at-topbar { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .at-title { margin: 0; font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .at-sub { margin: 4px 0 0; font-size: 13px; color: #888; }
        .at-btn-create {
          padding: 10px 20px; background: #4f6ef7; color: #fff; border: none;
          border-radius: 8px; cursor: pointer; font-size: 13.5px; font-weight: 600;
        }
        .at-btn-create:hover { background: #3a5ce4; }
        .at-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        .at-card {
          background: #fff; border-radius: 10px; padding: 16px 18px;
          box-shadow: 0 1px 4px rgba(0,0,0,.07);
        }
        .at-card-label { display: block; font-size: 12px; color: #888; margin-bottom: 6px; }
        .at-card-val { font-size: 20px; font-weight: 700; }
        .at-filters {
          display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap;
        }
        .at-search {
          flex: 1; min-width: 200px; padding: 9px 12px; border: 1.5px solid #e4e4e4;
          border-radius: 8px; font-size: 13.5px; outline: none;
        }
        .at-filters select, .at-filters input[type=date] {
          padding: 9px 12px; border: 1.5px solid #e4e4e4; border-radius: 8px;
          font-size: 13.5px; outline: none; background: #fff;
        }
        .at-btn-clear {
          padding: 9px 16px; border: 1.5px solid #e4e4e4; border-radius: 8px;
          background: #fff; cursor: pointer; font-size: 13px; color: #666;
        }
        .at-table-wrap {
          background: #fff; border-radius: 10px; overflow: hidden;
          box-shadow: 0 1px 4px rgba(0,0,0,.07);
        }
        .at-loading, .at-empty {
          text-align: center; padding: 48px; color: #aaa; font-size: 14px;
        }
        .at-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .at-table thead tr { background: #f7f8fc; }
        .at-table th {
          padding: 12px 14px; text-align: left; font-size: 12px; font-weight: 600;
          color: #888; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap;
        }
        .at-table td { padding: 11px 14px; border-top: 1px solid #f0f0f0; }
        .at-table tbody tr:hover { background: #fafbff; }
        .at-badge {
          display: inline-block; padding: 3px 10px; border-radius: 20px;
          font-size: 12px; font-weight: 600;
        }
        .at-num { text-align: right; }
        .at-mono { font-family: monospace; font-size: 12.5px; color: #555; }
        .at-muted { color: #bbb; font-size: 12px; }
        .at-method { text-transform: capitalize; color: #555; }
        .at-desc { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #666; }
        .at-pager {
          display: flex; align-items: center; justify-content: space-between;
          padding: 14px 0; font-size: 13px; color: #888;
        }
        .at-pager-btns { display: flex; gap: 8px; }
        .at-pager-btns button {
          padding: 7px 16px; border: 1.5px solid #e4e4e4; border-radius: 7px;
          background: #fff; cursor: pointer; font-size: 13px;
        }
        .at-pager-btns button:disabled { opacity: .4; cursor: not-allowed; }
        @media (max-width: 768px) {
          .at-cards { grid-template-columns: 1fr 1fr; }
          .at-table-wrap { overflow-x: auto; }
        }
      `}</style>
    </div>
  );
}