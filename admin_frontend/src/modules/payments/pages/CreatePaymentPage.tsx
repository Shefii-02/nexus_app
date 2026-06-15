import { useState } from "react";

const PAYMENT_METHODS = ["cash", "bank_transfer", "cheque", "online", "other"];

const EXPENSE_CATEGORIES = [
  { value: "default", label: "General Expense" },
  // { value: "teacher_payment", label: "Teacher Payment" },
  // { value: "staff_salary", label: "Staff Salary" },
  { value: "office_expense", label: "Office Expense" },
  { value: "other", label: "Other (Custom)" },
];

const TYPE_OPTIONS = [
  { value: "income", label: "Income", color: "#16a34a" },
  { value: "expense", label: "Expense", color: "#dc2626" },
  { value: "refund", label: "Refund", color: "#d97706" },
];

export default function CreatePayment({ onClose, onSuccess }) {
  const [form, setForm] = useState({
    type: "income",
    category: "",
    amount: "",
    payment_method: "cash",
    transaction_date: new Date().toISOString().split("T")[0],
    description: "",
    reference_type: "",
    reference_id: "",
    custom_category: "",
  });
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});

  const set = (key, val) => {
    setForm((f) => ({ ...f, [key]: val }));
    setErrors((e) => ({ ...e, [key]: undefined }));
  };

  const validate = () => {
    const e = {};
    if (!form.amount || isNaN(form.amount) || Number(form.amount) <= 0)
      e.amount = "Enter a valid amount";
    if (!form.transaction_date) e.transaction_date = "Date is required";
    if (form.type === "expense" && !form.category)
      e.category = "Select a category";
    if (form.category === "other" && !form.custom_category.trim())
      e.custom_category = "Enter custom category";
    return e;
  };

  const handleSubmit = async () => {
    const e = validate();
    if (Object.keys(e).length) { setErrors(e); return; }
    setLoading(true);
    try {
      const payload = {
        ...form,
        amount: parseFloat(form.amount),
        category:
          form.category === "other" ? form.custom_category : form.category,
      };
      delete payload.custom_category;
  
    
      onSuccess?.();
      onClose?.();
    } finally {
      setLoading(false);
    }
  };

  return (
    <div >
      <div className="cp-modal">
        <div className="cp-header">
          <h2>Create Payment</h2>
          
        </div>

        {/* Type selector */}
        <div className="cp-type-row">
          {TYPE_OPTIONS.map((t) => (
            <button
              key={t.value}
              className={`cp-type-btn ${form.type === t.value ? "active" : ""}`}
              style={form.type === t.value ? { borderColor: t.color, color: t.color, background: t.color + "12" } : {}}
              onClick={() => { set("type", t.value); set("category", ""); }}
            >
              {t.label}
            </button>
          ))}
        </div>

        <div className="cp-body">
          {/* Expense category */}
          {form.type === "expense" && (
            <div className="cp-field">
              <label>Expense Category <span className="req">*</span></label>
              <select value={form.category} onChange={(e) => set("category", e.target.value)}>
                <option value="">Select category</option>
                {EXPENSE_CATEGORIES.map((c) => (
                  <option key={c.value} value={c.value}>{c.label}</option>
                ))}
              </select>
              {errors.category && <span className="cp-err">{errors.category}</span>}
            </div>
          )}

          {/* Custom category input */}
          {form.type === "expense" && form.category === "other" && (
            <div className="cp-field">
              <label>Custom Category Name <span className="req">*</span></label>
              <input
                type="text"
                placeholder="e.g. Maintenance, Marketing..."
                value={form.custom_category}
                onChange={(e) => set("custom_category", e.target.value)}
              />
              {errors.custom_category && <span className="cp-err">{errors.custom_category}</span>}
            </div>
          )}

          {/* Amount */}
          <div className="cp-field">
            <label>Amount <span className="req">*</span></label>
            <div className="cp-amount-wrap">
              <span className="cp-currency">₹</span>
              <input
                type="number"
                min="0"
                step="0.01"
                placeholder="0.00"
                value={form.amount}
                onChange={(e) => set("amount", e.target.value)}
              />
            </div>
            {errors.amount && <span className="cp-err">{errors.amount}</span>}
          </div>

          {/* Payment Method */}
          <div className="cp-field">
            <label>Payment Method</label>
            <select value={form.payment_method} onChange={(e) => set("payment_method", e.target.value)}>
              {PAYMENT_METHODS.map((m) => (
                <option key={m} value={m}>{m.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase())}</option>
              ))}
            </select>
          </div>

          {/* Transaction Date */}
          <div className="cp-field">
            <label>Transaction Date <span className="req">*</span></label>
            <input
              type="date"
              value={form.transaction_date}
              onChange={(e) => set("transaction_date", e.target.value)}
            />
            {errors.transaction_date && <span className="cp-err">{errors.transaction_date}</span>}
          </div>

          {/* Description */}
          <div className="cp-field">
            <label>Description / Notes</label>
            <textarea
              rows={3}
              placeholder="Optional notes..."
              value={form.description}
              onChange={(e) => set("description", e.target.value)}
            />
          </div>

          {/* Reference (optional) */}
          <div className="cp-row-2">
            <div className="cp-field">
              <label>Reference Type</label>
              <input
                type="text"
                placeholder="e.g. invoice, receipt"
                value={form.reference_type}
                onChange={(e) => set("reference_type", e.target.value)}
              />
            </div>
            <div className="cp-field">
              <label>Reference ID</label>
              <input
                type="text"
                placeholder="ID or number"
                value={form.reference_id}
                onChange={(e) => set("reference_id", e.target.value)}
              />
            </div>
          </div>
        </div>

        <div className="cp-footer">
          <button className="cp-btn-submit" onClick={handleSubmit} disabled={loading}>
            {loading ? "Saving…" : "Create Transaction"}
          </button>
        </div>
      </div>

      <style>{`
        .cp-overlay {
          position: fixed; inset: 0; background: rgba(0,0,0,.45);
          display: flex; align-items: center; justify-content: center; z-index: 1000;
        }
        .cp-modal {
          background: #fff; border-radius: 12px; width: 75vw;
          max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;
          box-shadow: 0 20px 60px rgba(0,0,0,.18);
        }
        .cp-header {
          display: flex; align-items: center; justify-content: space-between;
          padding: 20px 24px 16px; border-bottom: 1px solid #f0f0f0;
        }
        .cp-header h2 { margin: 0; font-size: 17px; font-weight: 600; color: #1a1a2e; }
        .cp-close {
          background: none; border: none; font-size: 22px; cursor: pointer;
          color: #888; line-height: 1; padding: 0 4px;
        }
        .cp-type-row {
          display: flex; gap: 8px; padding: 16px 24px 0;
        }
        .cp-type-btn {
          flex: 1; padding: 8px; border: 1.5px solid #e0e0e0; border-radius: 8px;
          background: #fafafa; cursor: pointer; font-size: 13px; font-weight: 500;
          color: #555; transition: all .15s;
        }
        .cp-type-btn.active { font-weight: 600; }
        .cp-body {
          flex: 1; overflow-y: auto; padding: 16px 24px;
          display: flex; flex-direction: column; gap: 14px;
        }
        .cp-field { display: flex; flex-direction: column; gap: 5px; }
        .cp-field label { font-size: 12.5px; font-weight: 500; color: #444; }
        .cp-field input, .cp-field select, .cp-field textarea {
          border: 1.5px solid #e4e4e4; border-radius: 8px; padding: 9px 12px;
          font-size: 13.5px; color: #1a1a2e; outline: none; transition: border .15s;
          background: #fff; font-family: inherit;
        }
        .cp-field input:focus, .cp-field select:focus, .cp-field textarea:focus {
          border-color: #4f6ef7;
        }
        .cp-amount-wrap { position: relative; }
        .cp-currency {
          position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
          color: #888; font-size: 14px; pointer-events: none;
        }
        .cp-amount-wrap input { padding-left: 28px; width: 100%; box-sizing: border-box; }
        .cp-err { font-size: 11.5px; color: #dc2626; margin-top: 2px; }
        .req { color: #dc2626; }
        .cp-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .cp-footer {
          display: flex; gap: 10px; padding: 16px 24px;
          border-top: 1px solid #f0f0f0; justify-content: flex-end;
        }
        .cp-btn-cancel {
          padding: 9px 20px; border: 1.5px solid #e0e0e0; border-radius: 8px;
          background: #fff; cursor: pointer; font-size: 13.5px; color: #555;
        }
        .cp-btn-submit {
          padding: 9px 24px; border: none; border-radius: 8px;
          background: #4f6ef7; color: #fff; cursor: pointer; font-size: 13.5px;
          font-weight: 600; transition: background .15s;
        }
        .cp-btn-submit:hover { background: #3a5ce4; }
        .cp-btn-submit:disabled { background: #a0aec0; cursor: not-allowed; }
      `}</style>
    </div>
  );
}