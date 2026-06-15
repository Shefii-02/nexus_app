// src/modules/conversations/components/ConversationCard.tsx
import { useState } from 'react';
import { formatTime, getInitials } from '../utils';
import type { Conversation } from '../services/chatService';

interface Props {
  conversation: Conversation;
  isActive: boolean;
  currentUserId: number;
  onSelect: () => void;
  onMute: () => void;
  onPin: () => void;
}

export function ConversationCard({ conversation: conv, isActive, currentUserId, onSelect, onMute, onPin }: Props) {
  const [showMenu, setShowMenu] = useState(false);

  const displayName = conv.type === 'single'
    ? conv.other_user?.name ?? 'Unknown'
    : conv.title ?? 'Group';

  const avatar = conv.type === 'single' ? conv.other_user?.avatar : conv.avatar;

  const lastMsg = conv.last_message;
  const lastMsgText = lastMsg?.is_deleted
    ? 'This message was deleted'
    : lastMsg?.type === 'image' ? '📷 Photo'
    : lastMsg?.type === 'video' ? '🎥 Video'
    : lastMsg?.type === 'audio' ? '🎵 Audio'
    : lastMsg?.type === 'voice' ? '🎤 Voice message'
    : lastMsg?.type === 'file'  ? `📎 ${lastMsg.media_meta?.original_name ?? 'File'}`
    : lastMsg?.message ?? '';

  return (
    <div
      className={`conv-card ${isActive ? 'active' : ''} ${conv.is_pinned ? 'pinned' : ''}`}
      onClick={onSelect}
      onContextMenu={(e) => { e.preventDefault(); setShowMenu(true); }}
    >
      {/* Avatar */}
      <div className="conv-avatar">
        {avatar ? (
          <img src={avatar} alt={displayName} />
        ) : (
          <span className="avatar-initials text-capitalize">{getInitials(displayName)}</span>
        )}
        {conv.type === 'single' && <span className="online-dot" />}
      </div>

      {/* Content */}
      <div className="conv-content">
        <div className="conv-top">
          <span className="conv-name capitalize">
         {!!conv.is_pinned && <span className="pin-icon">📌</span>}
            {displayName}
          </span>
          <span className="conv-time">{lastMsg ? formatTime(lastMsg.created_at) : ''}</span>
        </div>
        <div className="conv-bottom">
          <span className={`conv-preview ${lastMsg?.is_deleted ? 'deleted' : ''}`}>
            {lastMsg?.sender_id === currentUserId && <span className="you-label">You: </span>}
            {lastMsgText}
          </span>
          <div className="conv-badges">
            {!!conv.is_muted && <span className="mute-icon">🔇</span>}
            {conv.unread_count > 0 && (
              <span className="unread-badge">
                {conv.unread_count > 99 ? '99+' : conv.unread_count}
              </span>
            )}
          </div>
        </div>
      </div>

      {/* Context Menu */}
      {showMenu && (
        <div className="context-menu" onMouseLeave={() => setShowMenu(false)}>
          <button onClick={(e) => { e.stopPropagation(); onPin(); setShowMenu(false); }}>
            {conv.is_pinned ? '📌 Unpin' : '📌 Pin'}
          </button>
          <button onClick={(e) => { e.stopPropagation(); onMute(); setShowMenu(false); }}>
            {conv.is_muted ? '🔔 Unmute' : '🔇 Mute'}
          </button>
        </div>
      )}
    </div>
  );
}
