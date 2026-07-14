// src/modules/conversations/components/PinnedMessageBar.tsx
import type { Message } from '../services/chatService';

interface Props {
  pinnedMsgs: Message[];
  onScrollTo: (messageId: number) => void;
  onClose: () => void;
  onUnpin: (messageId: number) => void;   // ✅ new
}

function mediaLabel(type: string) {
  const map: Record<string, string> = {
    image: '📷 Photo', video: '🎥 Video',
    audio: '🎵 Audio', voice: '🎤 Voice message', file: '📎 File',
  };
  return map[type] ?? `📎 ${type}`;
}

export function PinnedMessageBar({ pinnedMsgs, onScrollTo, onClose, onUnpin }: Props) {
  if (pinnedMsgs.length === 0) return null;

  const latest = pinnedMsgs[pinnedMsgs.length - 1];

  const preview = latest.is_deleted
    ? 'This message was deleted'
    : latest.type !== 'text'
    ? mediaLabel(latest.type)
    : (latest.message?.substring(0, 80) ?? '');

  const count = pinnedMsgs.length;

  return (
    <div className="pinned-bar" onClick={() => onScrollTo(latest.id)}>
      <div className="pinned-bar-accent" />
      <div className="pinned-bar-body">
        <span className="pinned-bar-label">
          📌 {count > 1 ? `Pinned message (${count})` : 'Pinned message'}
        </span>
        <span className="pinned-bar-preview">{preview}</span>
      </div>

      {/* ✅ actually unpins the currently shown message */}
      <button
        className="pinned-bar-unpin"
        title="Unpin this message"
        onClick={e => { e.stopPropagation(); onUnpin(latest.id); }}
      >
        <svg width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
          <path d="M12 2 L12 12 M8 6 L16 6 M9 12 L15 12 L14 22 L10 22 Z"/>
        </svg>
      </button>

      {/* dismisses the banner without unpinning */}
      <button
        className="pinned-bar-close"
        title="Dismiss"
        onClick={e => { e.stopPropagation(); onClose(); }}
      >
        <svg width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>
  );
}