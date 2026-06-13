// src/modules/conversations/components/ConversationTabs.tsx
type TabType = 'all' | 'unread' | 'groups';

interface TabsProps {
  active: TabType;
  onChange: (t: TabType) => void;
}

export function ConversationTabs({ active, onChange }: TabsProps) {
  return (
    <div className="conv-tabs">
      {(['all', 'unread', 'groups'] as TabType[]).map(tab => (
        <button
          key={tab}
          className={`conv-tab ${active === tab ? 'active' : ''}`}
          onClick={() => onChange(tab)}
        >
          {tab.charAt(0).toUpperCase() + tab.slice(1)}
        </button>
      ))}
    </div>
  );
}
