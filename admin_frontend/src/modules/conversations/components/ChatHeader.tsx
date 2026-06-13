// src/modules/conversations/components/ChatHeader.tsx

import type { Conversation } from '../services/chatService';
import { getInitials } from '../utils';

interface Props {
  conversation: Conversation;
  currentUserId: number;
  typingUsers: { user_id: number; user_name: string }[];
  onBack: () => void;
  onInfoOpen: () => void;
}

export function ChatHeader({ conversation, typingUsers, onBack, onInfoOpen }: Props) {
  const isGroup = conversation.type === 'group';
  const displayName = isGroup
    ? conversation.title ?? 'Group'
    : conversation.other_user?.name ?? 'Unknown';
  const avatar = isGroup ? conversation.avatar : conversation.other_user?.avatar;

  const subtitle = typingUsers.length > 0
    ? typingUsers.length === 1
      ? `${typingUsers[0].user_name} is typing...`
      : `${typingUsers.map(u => u.user_name).join(', ')} are typing...`
    : isGroup
    ? `${conversation.participants?.filter(p => p.status === 'active').length ?? 0} members`
    : 'Online';

  return (
    <div className="chat-header">
      <button className="back-btn" onClick={onBack}>
        <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
      </button>

      <div className="header-avatar" onClick={onInfoOpen} style={{ cursor: 'pointer' }}>
        {avatar ? (
          <img src={avatar} alt={displayName} />
        ) : (
          <span className="avatar-initials">{getInitials(displayName)}</span>
        )}
      </div>

      <div className="header-info" onClick={onInfoOpen} style={{ cursor: 'pointer', flex: 1 }}>
        <span className="header-name">{displayName}</span>
        <span className={`header-subtitle ${typingUsers.length > 0 ? 'typing' : ''}`}>
          {subtitle}
        </span>
      </div>

      {/* <div className="header-actions">
        <button className="icon-btn" title="Voice call">
          <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.61 4.38 2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.1 6.1l.9-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
          </svg>
        </button>
        <button className="icon-btn" title="Video call">
          <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
            <polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
          </svg>
        </button>
        <button className="icon-btn" onClick={onInfoOpen} title="Info">
          <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </button>
      </div> */}
    </div>
  );
}
