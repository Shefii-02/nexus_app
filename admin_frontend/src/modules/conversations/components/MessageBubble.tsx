// src/modules/conversations/components/MessageBubble.tsx
import { useState, useRef } from 'react';
import type { Message } from '../services/chatService';
import { formatMessageTime, getInitials } from '../utils';

interface Props {
  message: Message;
  isMine: boolean;
  showAvatar: boolean;
  currentUserId: number;
  onReply: () => void;
  onEdit: (text: string) => void;
  onDelete: (forEveryone: boolean) => void;
  onReact: (emoji: string) => void;
  onRemoveReact: () => void;
}

const EMOJI_REACTIONS = ['👍', '❤️', '😂', '😮', '😢', '🙏'];

export function MessageBubble({ message: msg, isMine, showAvatar, currentUserId, onReply, onEdit, onDelete, onReact, onRemoveReact }: Props) {
  const [showActions, setShowActions] = useState(false);
  const [showEmoji, setShowEmoji] = useState(false);
  const [showDropdown, setShowDropdown] = useState(false);
  const [editing, setEditing] = useState(false);
  const [editText, setEditText] = useState(msg.message ?? '');
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [lightbox, setLightbox] = useState(false);

  const myReaction = msg.reactions.find(r => r.user_id === currentUserId);

  const reactionGroups: Record<string, number> = {};
  msg.reactions.forEach(r => {
    reactionGroups[r.reaction] = (reactionGroups[r.reaction] ?? 0) + 1;
  });

  const isRead = msg.reads.length > 0;

  if (msg.is_deleted) {
    return (
      <div className={`msg-row ${isMine ? 'mine' : 'theirs'}`}>
        <div className="msg-bubble deleted">
          <span>🚫 This message was deleted</span>
        </div>
      </div>
    );
  }

  return (
    <>
      <div
        className={`msg-row ${isMine ? 'mine' : 'theirs'}`}
        onMouseEnter={() => setShowActions(true)}
        onMouseLeave={() => { setShowActions(false); setShowEmoji(false); setShowDropdown(false); }}
      >
        {/* Avatar (other users) */}
        {!isMine && (
          <div className="msg-avatar">
            {showAvatar ? (
              msg.sender.avatar
                ? <img src={msg.sender.avatar} alt={msg.sender.name} />
                : <span className="avatar-initials sm">{getInitials(msg.sender.name)}</span>
            ) : (
              <div className="avatar-placeholder" />
            )}
          </div>
        )}

        {/* Hover action bar — positioned between avatar and bubble */}
        {showActions && (
          <div className={`msg-action-bar ${isMine ? 'mine' : 'theirs'}`}>
            {/* Emoji reaction button */}
            <div className="action-bar-item emoji-trigger">
              <button
                className="action-bar-btn"
                onClick={() => { setShowEmoji(!showEmoji); setShowDropdown(false); }}
                title="React"
              >
                😊
              </button>
              {showEmoji && (
                <div className={`emoji-picker floating ${isMine ? 'right' : 'left'}`}>
                  {EMOJI_REACTIONS.map(e => (
                    <button
                      key={e}
                      className={myReaction?.reaction === e ? 'active' : ''}
                      onClick={() => { onReact(e); setShowEmoji(false); }}
                    >
                      {e}
                    </button>
                  ))}
                </div>
              )}
            </div>

            {/* Reply */}
            <button className="action-bar-btn" onClick={onReply} title="Reply">↩️</button>

            {/* More dropdown */}
            <div className="action-bar-item dropdown-trigger">
              <button
                className="action-bar-btn"
                onClick={() => { setShowDropdown(!showDropdown); setShowEmoji(false); }}
                title="More"
              >
                ⋮
              </button>
              {showDropdown && (
                <div className={`msg-dropdown ${isMine ? 'right' : 'left'}`}>
                  <button onClick={() => { onReply(); setShowDropdown(false); }}>↩ Reply</button>
                  {isMine && msg.type === 'text' && (
                    <button onClick={() => { setEditing(true); setShowDropdown(false); }}>✏️ Edit</button>
                  )}
                  {isMine && (
                    <button className="danger" onClick={() => { setShowDeleteModal(true); setShowDropdown(false); }}>🗑️ Delete</button>
                  )}
                  {!isMine && (
                    <button className="danger" onClick={() => { onDelete(false); setShowDropdown(false); }}>🗑️ Delete for me</button>
                  )}
                </div>
              )}
            </div>
          </div>
        )}

        <div className={`msg-bubble ${isMine ? 'mine' : 'theirs'} type-${msg.type}`}>
          {/* Sender name (groups) */}
          {!isMine && showAvatar && (
            <span className="msg-sender-name">{msg.sender.name}</span>
          )}

          {/* Reply Preview */}
          {msg.reply_message && (
            <div className="reply-preview">
              <div className="reply-accent" />
              <div className="reply-content">
                <span className="reply-sender">{msg.reply_message.sender?.name ?? 'Unknown'}</span>
                <span className="reply-text">
                  {msg.reply_message.type !== 'text'
                    ? `📎 ${msg.reply_message.type}`
                    : msg.reply_message.message?.substring(0, 80)}
                </span>
              </div>
            </div>
          )}

          {/* Message Content */}
          {msg.type === 'text' && (
            editing ? (
              <div className="edit-area">
                <textarea
                  value={editText}
                  onChange={e => setEditText(e.target.value)}
                  autoFocus
                  onKeyDown={e => {
                    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); onEdit(editText); setEditing(false); }
                    if (e.key === 'Escape') setEditing(false);
                  }}
                />
                <div className="edit-actions">
                  <button onClick={() => setEditing(false)}>Cancel</button>
                  <button className="primary" onClick={() => { onEdit(editText); setEditing(false); }}>Save</button>
                </div>
              </div>
            ) : (
              <p className="msg-text">{msg.message}</p>
            )
          )}

          {msg.type === 'image' && msg.media_url && (
            <div className="msg-image" onClick={() => setLightbox(true)}>
              <img src={msg.media_url} alt="Image" loading="lazy" />
              {msg.message && <p className="msg-caption">{msg.message}</p>}
            </div>
          )}

          {msg.type === 'video' && msg.media_url && (
            <div className="msg-video">
              <video controls src={msg.media_url} />
              {msg.message && <p className="msg-caption">{msg.message}</p>}
            </div>
          )}

          {(msg.type === 'audio' || msg.type === 'voice') && msg.media_url && (
            <div className="msg-audio">
              <span>{msg.type === 'voice' ? '🎤' : '🎵'}</span>
              <audio controls src={msg.media_url} />
            </div>
          )}

          {msg.type === 'file' && msg.media_url && (
            <a className="msg-file" href={msg.media_url} target="_blank" rel="noreferrer" download>
              <div className="file-icon">📎</div>
              <div className="file-info">
                <span className="file-name">{msg.media_meta?.original_name ?? 'File'}</span>
                <span className="file-size">{msg.media_meta?.size ? `${(msg.media_meta.size / 1024).toFixed(1)} KB` : ''}</span>
              </div>
              <span className="file-download">⬇</span>
            </a>
          )}

          {/* Meta */}
          <div className="msg-meta">
            {msg.is_edited && <span className="edited-label">edited</span>}
            <span className="msg-time">{formatMessageTime(msg.created_at)}</span>
            {isMine && (
              <span className="read-receipt" title={isRead ? 'Read' : 'Delivered'}>
                {isRead ? (
                  <svg width="16" height="10" viewBox="0 0 16 10" fill="#53bdeb">
                    <path d="M1 5l3 3L11 1M6 5l3 3L16 1" stroke="#53bdeb" strokeWidth="1.5" fill="none"/>
                  </svg>
                ) : (
                  <svg width="12" height="10" viewBox="0 0 12 10" fill="none">
                    <path d="M1 5l3 3L11 1" stroke="#aaa" strokeWidth="1.5"/>
                  </svg>
                )}
              </span>
            )}
          </div>

          {/* Reactions */}
          {Object.keys(reactionGroups).length > 0 && (
            <div className="reactions">
              {Object.entries(reactionGroups).map(([emoji, count]) => (
                <button
                  key={emoji}
                  className={`reaction-chip ${myReaction?.reaction === emoji ? 'mine' : ''}`}
                  onClick={() => myReaction ? onRemoveReact() : onReact(emoji)}
                >
                  {emoji} {count}
                </button>
              ))}
            </div>
          )}
        </div>
      </div>

      {/* Delete Modal */}
      {showDeleteModal && (
        <div className="modal-overlay" onClick={() => setShowDeleteModal(false)}>
          <div className="modal-box" onClick={e => e.stopPropagation()}>
            <h3>Delete Message</h3>
            <p>Who do you want to delete this message for?</p>
            <div className="modal-actions">
              <button onClick={() => setShowDeleteModal(false)}>Cancel</button>
              <button onClick={() => { onDelete(false); setShowDeleteModal(false); }}>Delete for me</button>
              <button className="danger" onClick={() => { onDelete(true); setShowDeleteModal(false); }}>Delete for everyone</button>
            </div>
          </div>
        </div>
      )}

      {/* Lightbox */}
      {lightbox && msg.media_url && (
        <div className="lightbox" onClick={() => setLightbox(false)}>
          <img src={msg.media_url} alt="Preview" />
          <button className="lightbox-close" onClick={() => setLightbox(false)}>✕</button>
        </div>
      )}
    </>
  );
}