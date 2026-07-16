// src/modules/conversations/components/MessageBubble.tsx
import { useState, useRef, useEffect } from 'react';
import type { Message } from '../services/chatService';
import { formatMessageTime, getInitials } from '../utils';

interface Props {
  message: Message;
  isMine: boolean;
  showAvatar: boolean;
  currentUserId: number;
  currentUserRole: string;
  onReply: () => void;
  onEdit: (text: string) => void;
  onDelete: (forEveryone: boolean) => void;
  onReact: (emoji: string) => void;
  onRemoveReact: () => void;
  onPin: () => void;
  onForward: () => void;
  onReport: (reason: string) => void;
  onJumpToReply?: (messageId: number) => void;
  canSend?: boolean;
}

const EMOJI_REACTIONS = ['👍', '❤️', '😂', '😮', '😢', '🙏'];

export function MessageBubble({
  message: msg,
  isMine,
  showAvatar,
  currentUserId,
  currentUserRole,
  onReply,
  onEdit,
  onDelete,
  onReact,
  onRemoveReact,
  onForward,
  onPin,
  onReport,
  onJumpToReply,
  canSend = true,
}: Props) {
  const [showActions, setShowActions] = useState(false);
  const [showEmoji, setShowEmoji] = useState(false);
  const [showDropdown, setShowDropdown] = useState(false);
  const [editing, setEditing] = useState(false);
  const [editText, setEditText] = useState(msg.message ?? '');
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [showReportModal, setShowReportModal] = useState(false);
  const [reportReason, setReportReason] = useState('');
  const [reporting, setReporting] = useState(false);
  const [lightbox, setLightbox] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  const isPrivileged = currentUserRole === 'admin' || currentUserRole === 'staff';
  const canDeleteForEveryone = isMine || isPrivileged;

  const reactions = msg.reactions ?? [];
  const myReaction = reactions.find(r => r.user_id === currentUserId);

  // Group reactions: emoji → { count, names[] }
  const reactionGroups: Record<string, { count: number; names: string[] }> = {};
  reactions.forEach(r => {
    if (!reactionGroups[r.reaction]) reactionGroups[r.reaction] = { count: 0, names: [] };
    reactionGroups[r.reaction].count++;
    if (r.user?.name) reactionGroups[r.reaction].names.push(r.user.name);
  });

  const isRead = msg.reads?.length > 0;
  const hasReacts = Object.keys(reactionGroups)?.length > 0;

  // Close dropdown when clicking outside
  useEffect(() => {
    if (!showDropdown) return;
    const handler = (e: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target as Node)) {
        setShowDropdown(false);
      }
    };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, [showDropdown]);

  const handleReport = async () => {
    if (!reportReason.trim()) return;
    setReporting(true);
    try {
      await onReport(reportReason.trim());
      setShowReportModal(false);
      setReportReason('');
    } finally {
      setReporting(false);
    }
  };

  const closeAll = () => {
    setShowEmoji(false);
    setShowDropdown(false);
  };

  // ── Deleted ──────────────────────────────────────────────────────────────────
  if (msg.is_deleted) {
    return (
      <div className={`msg-row ${isMine ? 'mine' : 'theirs'}`}>
        {!isMine && <div className="msg-avatar-spacer" />}
        <div className="msg-bubble deleted">
          <span className="deleted-icon">🚫</span>
          <span>This message was deleted</span>
        </div>
      </div>
    );
  }

  // ── Normal ───────────────────────────────────────────────────────────────────
  return (
    <>
      <div
        className={`msg-row ${isMine ? 'mine' : 'theirs'}`}
        onMouseEnter={() => setShowActions(true)}
        onMouseLeave={() => { setShowActions(false); closeAll(); }}
      >
        {/* ── Avatar column (others only) ── */}
        {!isMine && (
          <div className="msg-avatar">
            {showAvatar ? (
              msg.sender?.avatar
                ? <img src={msg.sender?.avatar} alt={msg.sender?.name} />
                : <span className="avatar-initials sm">{getInitials(msg.sender?.name)}</span>
            ) : (
              <div className="avatar-placeholder" />
            )}
          </div>
        )}

        {/* ── Hover action bar ── */}
        {showActions && (
          <div className={`msg-action-bar ${isMine ? 'mine' : 'theirs'}`}>
            {/* Emoji — always available, regardless of send permission */}
            <div className="action-bar-item">
              <button
                className="action-bar-btn"
                onClick={() => { setShowEmoji(v => !v); setShowDropdown(false); }}
                title="React"
              >
                😊
              </button>

              {showEmoji && (
                <div className={`emoji-picker floating ${isMine ? 'right' : 'left'}`}>
                  {EMOJI_REACTIONS.map(e => (
                    <button
                      key={e}
                      className={`emoji-btn ${myReaction?.reaction === e ? 'active' : ''}`}
                      onClick={() => {
                        myReaction?.reaction === e ? onRemoveReact() : onReact(e);
                        setShowEmoji(false);
                      }}
                      title={e}
                    >
                      {e}
                    </button>
                  ))}
                </div>
              )}
            </div>

            {/* More — dropdown itself always available; individual items gated below */}
            <div className="ab-item" ref={dropdownRef}>
              <button
                className="ab-btn"
                title="More"
                onClick={() => { setShowDropdown(v => !v); setShowEmoji(false); }}
              >
                <svg width="4" height="16" viewBox="0 0 4 20" fill="currentColor">
                  <circle cx="2" cy="2" r="2" />
                  <circle cx="2" cy="10" r="2" />
                  <circle cx="2" cy="18" r="2" />
                </svg>
              </button>

              {showDropdown && (
                <ul className={`msg-menu ${isMine ? 'right' : 'left'}`}>
                  {canSend && (
                    <li onClick={() => { onReply(); closeAll(); }}>
                      <span className="mm-icon">↩</span> Reply
                    </li>
                  )}
                  {canSend && msg.type == 'text' && (
                    <li onClick={() => { onForward(); closeAll(); }}>
                      <span className="mm-icon">↪</span> Forward
                    </li>
                  )}
                  {/* {canSend && isMine && msg.type === 'text' && ( */}
                    {/* // <li onClick={() => { setEditing(true); closeAll(); }}>
                    //   <span className="mm-icon">✏️</span> Edit
                    // </li> */}
                  {/* // )} */}
                  {/* Pin/Unpin — not a "send" action, always available */}
                  <li onClick={() => { onPin(); closeAll(); }}>
                    <span className="mm-icon">{msg.is_pinned ? '📌' : '📍'}</span>
                    {msg.is_pinned ? 'Unpin' : 'Pin'}
                  </li>
                  {!isMine && (
                    <li onClick={() => { setShowReportModal(true); closeAll(); }}>
                      <span className="mm-icon">🚩</span> Report
                    </li>
                  )}
                  {/* Delete for me — always available */}
                  <li className="danger" onClick={() => { onDelete(false); closeAll(); }}>
                    <span className="mm-icon">🗑️</span> Delete for me
                  </li>
                  {/* Delete for everyone — own messages OR admin/staff */}
                  {canDeleteForEveryone && (
                    <li className="danger" onClick={() => { setShowDeleteModal(true); closeAll(); }}>
                      <span className="mm-icon">🗑️</span> Delete for everyone
                    </li>
                  )}
                </ul>
              )}
            </div>
          </div>
        )}

        {/* ── Bubble ── */}
        <div className={`msg-bubble ${isMine ? 'mine' : 'theirs'} type-${msg.type}`}>

          {/* Sender name (groups, first in run) */}
          {!isMine && showAvatar && (
            <span className="msg-sender-name">{msg.sender?.name}</span>
          )}

          {/* ── WhatsApp-style reply quote ── */}
          {msg.reply_to_message && (
            <div
              className="reply-quote"
              onClick={e => {
                e.stopPropagation();
                if (!msg.reply_to_message?.is_deleted) {
                  onJumpToReply?.(msg.reply_to_message!.id);
                }
              }}
            >
              <div className="reply-quote-bar" />
              <div className="reply-quote-body">
                <span className="reply-quote-sender">
                  {msg.reply_to_message.sender?.name ?? 'Unknown'}
                </span>
                <span className="reply-quote-text">
                  {msg.reply_to_message.is_deleted
                    ? 'This message was deleted'
                    : msg.reply_to_message.type !== 'text'
                      ? mediaLabel(msg.reply_to_message.type)
                      : (msg.reply_to_message.message?.substring(0, 100) ?? '')}
                </span>
              </div>
              {msg.reply_to_message.type === 'image' && msg.reply_to_message.media_url && (
                <img
                  className="reply-quote-thumb"
                  src={msg.reply_to_message.media_url}
                  alt="reply"
                />
              )}
            </div>
          )}

          {/* ── Content ── */}
          {msg.type === 'text' && (
            editing ? (
              <div className="edit-area">
                <textarea
                  value={editText}
                  onChange={e => setEditText(e.target.value)}
                  autoFocus
                  onKeyDown={e => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                      e.preventDefault();
                      onEdit(editText);
                      setEditing(false);
                    }
                    if (e.key === 'Escape') setEditing(false);
                  }}
                />
                <div className="edit-actions">
                  <button onClick={() => setEditing(false)}>Cancel</button>
                  <button className="primary" onClick={() => { onEdit(editText); setEditing(false); }}>
                    Save
                  </button>
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
                <span className="file-size">
                  {msg.media_meta?.size
                    ? `${(msg.media_meta.size / 1024).toFixed(1)} KB`
                    : ''}
                </span>
              </div>
              <span className="file-download">⬇</span>
            </a>
          )}

          {/* ── Meta row (edited · time · ticks) ── */}
          <div className="msg-meta">
            {msg.is_edited && <span className="edited-label">edited</span>}
            <span className="msg-time">{formatMessageTime(msg.created_at)}</span>
            {isMine && (
              <span className="read-receipt" title={isRead ? 'Read' : 'Delivered'}>
                {isRead ? (
                  <svg width="18" height="11" viewBox="0 0 18 11" fill="none">
                    <path d="M1 6l3.5 3.5L12 2" stroke="#53bdeb" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" />
                    <path d="M6 6l3.5 3.5L17 2" stroke="#53bdeb" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" />
                  </svg>
                ) : (
                  <svg width="12" height="11" viewBox="0 0 12 11" fill="none">
                    <path d="M1 6l3.5 3.5L11 2" stroke="#999" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" />
                  </svg>
                )}
              </span>
            )}
          </div>

          {/* ── Reaction chips (WhatsApp style) — always available, regardless of canSend ── */}
          {hasReacts && (
            <div className="reaction-row">
              {Object.entries(reactionGroups).map(([emoji, { count, names }]) => (
                <button
                  key={emoji}
                  className={`reaction-chip ${myReaction?.reaction === emoji ? 'mine' : ''}`}
                  onClick={() => myReaction?.reaction === emoji ? onRemoveReact() : onReact(emoji)}
                  title={names.join(', ')}
                >
                  <span className="reaction-emoji">{emoji}</span>
                  {count > 1 && <span className="reaction-count">{count}</span>}
                </button>
              ))}
            </div>
          )}
        </div>
      </div>

      {/* ── Delete modal ── */}
      {showDeleteModal && (
        <div className="modal-overlay" onClick={() => setShowDeleteModal(false)}>
          <div className="modal-box sm" onClick={e => e.stopPropagation()}>
            <h3>Delete message?</h3>
            <div className="modal-actions col">
              <button onClick={() => { onDelete(false); setShowDeleteModal(false); }}>
                Delete for me
              </button>
              <button className="danger" onClick={() => { onDelete(true); setShowDeleteModal(false); }}>
                Delete for everyone
              </button>
              <button className="ghost" onClick={() => setShowDeleteModal(false)}>Cancel</button>
            </div>
          </div>
        </div>
      )}

      {/* ── Report modal ── */}
      {showReportModal && (
        <div className="modal-overlay" onClick={() => setShowReportModal(false)}>
          <div className="modal-box sm" onClick={e => e.stopPropagation()}>
            <h3>Report message</h3>
            <p style={{ fontSize: 13, color: 'var(--text-secondary)', margin: '4px 0 12px' }}>
              Tell us why you're reporting this message.
            </p>
            <div className="form-group">
              <label>Reason</label>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 6, marginBottom: 12 }}>
                {['Spam', 'Harassment', 'Inappropriate content', 'Misinformation', 'Other'].map(r => (
                  <label key={r} style={{ display: 'flex', alignItems: 'center', gap: 8, cursor: 'pointer', fontSize: 14 }}>
                    <input
                      type="radio"
                      name="report-reason"
                      value={r}
                      checked={reportReason === r}
                      onChange={() => setReportReason(r)}
                    />
                    {r}
                  </label>
                ))}
              </div>
              {reportReason === 'Other' && (
                <textarea
                  placeholder="Describe the issue…"
                  rows={3}
                  style={{
                    width: '100%', resize: 'vertical', padding: '8px 10px', borderRadius: 8,
                    background: 'var(--surface2)', border: '1px solid var(--border)',
                    color: 'var(--text)', fontSize: 13, boxSizing: 'border-box'
                  }}
                  onChange={e => setReportReason(e.target.value)}
                />
              )}
            </div>
            <div className="modal-actions">
              <button onClick={() => { setShowReportModal(false); setReportReason(''); }}>Cancel</button>
              <button
                className="danger"
                onClick={handleReport}
                disabled={!reportReason.trim() || reporting}
              >
                {reporting ? 'Sending…' : 'Submit Report'}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ── Lightbox ── */}
      {lightbox && msg.media_url && (
        <div className="lightbox" onClick={() => setLightbox(false)}>
          <img src={msg.media_url} alt="Preview" />
          <button className="lightbox-close" onClick={() => setLightbox(false)}>✕</button>
        </div>
      )}
    </>
  );
}

function mediaLabel(type: string) {
  if (type === 'image') return '📷 Photo';
  if (type === 'video') return '🎥 Video';
  if (type === 'audio') return '🎵 Audio';
  if (type === 'voice') return '🎤 Voice message';
  if (type === 'file') return '📎 File';
  return `📎 ${type}`;
}