// src/modules/conversations/components/ConversationSearch.tsx

interface Props {
  value: string;
  onChange: (v: string) => void;
}

export function ConversationSearch({ value, onChange }: Props) {
  return (
    <div className="search-box">
      <svg width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input
        type="text"
        placeholder="Search conversations..."
        value={value}
        onChange={e => onChange(e.target.value)}
      />
      {value && (
        <button className="clear-btn" onClick={() => onChange('')}>✕</button>
      )}
    </div>
  );
}
