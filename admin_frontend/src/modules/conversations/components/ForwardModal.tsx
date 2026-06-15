// src/modules/conversations/components/ForwardModal.tsx
import { useState, useMemo } from 'react';
import type { Conversation, Message } from '../services/chatService';
import { forwardMessage } from '../services/chatService';
import { getInitials } from '../utils';

interface Props {
  message: Message;
  conversations: Conversation[];
  currentUserId: number;
  onClose: () => void;
}

type Tab = 'individual' | 'group';

export function ForwardModal({ message, conversations, currentUserId, onClose }: Props) {
  const [tab, setTab]         = useState<Tab>('individual');
  const [search, setSearch]   = useState('');
  const [selected, setSelected] = useState<Set<number>>(new Set());
  const [sending, setSending]  = useState(false);
  const [done, setDone]        = useState(false);

  const filtered = useMemo(() => {
    const byType = conversations.filter(c =>
      tab === 'individual' ? c.type === 'single' : c.type === 'group',
    );
    const q = search.toLowerCase().trim();
    if (!q) return byType;
    return byType.filter(c => {
      const name =
        c.type === 'single'
          ? c.other_user?.name ?? ''
          : c.title ?? '';
      return name.toLowerCase().includes(q);
    });
  }, [conversations, tab, search]);

  const toggle = (id: number) => {
    setSelected(prev => {
      const next = new Set(prev);
      next.has(id) ? next.delete(id) : next.add(id);
      return next;
    });
  };

  const handleSend = async () => {
    if (selected.size === 0) return;
    setSending(true);
    try {
      await forwardMessage(message.id, [...selected]);
      setDone(true);
      setTimeout(onClose, 800);
    } catch {
      setSending(false);
    }
  };

  const msgPreview =
    message.type !== 'text'
      ? mediaLabel(message.type)
      : (message.message?.substring(0, 60) ?? '');

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-box forward-modal" onClick={e => e.stopPropagation()}>
        {/* Header */}
        <div className="modal-header">
          <button className="icon-btn" onClick={onClose}>
            <svg width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
              <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
          </button>
          <h3>Forward message</h3>
        </div>

        {/* Message preview */}
        <div className="forward-preview">
          <span className="forward-preview-icon">↪</span>
          <span className="forward-preview-text">{msgPreview}</span>
        </div>

        {/* Tabs */}
        <div className="conv-tabs">
          {(['individual', 'group'] as Tab[]).map(t => (
            <button
              key={t}
              className={`conv-tab ${tab === t ? 'active' : ''}`}
              onClick={() => setTab(t)}
            >
              {t === 'individual' ? 'Chats' : 'Groups'}
            </button>
          ))}
        </div>

        {/* Search */}
        <div className="search-box">
          <svg width="15" height="15" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input
            placeholder="Search…"
            value={search}
            onChange={e => setSearch(e.target.value)}
            autoFocus
          />
          {search && <button className="clear-btn" onClick={() => setSearch('')}>✕</button>}
        </div>

        {/* List */}
        <div className="forward-list">
          {filtered.length === 0 && (
            <div className="conv-empty"><span>No conversations found</span></div>
          )}
          {filtered.map(conv => {
            const name =
              conv.type === 'single'
                ? conv.other_user?.name ?? 'Unknown'
                : conv.title ?? 'Group';
            const avatar =
              conv.type === 'single' ? conv.other_user?.avatar : conv.avatar;
            const isSelected = selected.has(conv.id);

            return (
              <div
                key={conv.id}
                className={`forward-row ${isSelected ? 'selected' : ''}`}
                onClick={() => toggle(conv.id)}
              >
                <div className="conv-avatar sm">
                  {avatar
                    ? <img src={avatar} alt={name} />
                    : <span className="avatar-initials">{getInitials(name)}</span>}
                </div>
                <span className="forward-row-name">{name}</span>
                <div className={`forward-tick ${isSelected ? 'checked' : ''}`}>
                  {isSelected && (
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                      <path d="M1.5 6l3 3L10.5 2" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                    </svg>
                  )}
                </div>
              </div>
            );
          })}
        </div>

        {/* Footer */}
        <div className="forward-footer">
          {selected.size > 0 && (
            <span className="forward-selected-count">{selected.size} selected</span>
          )}
          <button
            className="send-btn primary"
            onClick={handleSend}
            disabled={selected.size === 0 || sending}
          >
            {done ? '✓ Sent' : sending ? 'Sending…' : (
              <>
                <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                  <path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/>
                </svg>
                Send
              </>
            )}
          </button>
        </div>
      </div>
    </div>
  );
}

function mediaLabel(type: string) {
  if (type === 'image') return '📷 Photo';
  if (type === 'video') return '🎥 Video';
  if (type === 'audio') return '🎵 Audio';
  if (type === 'voice') return '🎤 Voice message';
  return '📎 File';
}