// src/modules/conversations/components/PinnedMessageBar.tsx
import type { Message } from '../services/chatService';

interface Props {
  pinnedMsgs: Message[];
  onScrollTo: (messageId: number) => void;
  onClose: () => void;
}

function mediaLabel(type: string) {
  const map: Record<string, string> = {
    image: '📷 Photo', video: '🎥 Video',
    audio: '🎵 Audio', voice: '🎤 Voice message', file: '📎 File',
  };
  return map[type] ?? `📎 ${type}`;
}

export function PinnedMessageBar({ pinnedMsgs, onScrollTo, onClose }: Props) {
  if (pinnedMsgs.length === 0) return null;

  // Show the most recently pinned (last in array)
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
      <button
        className="pinned-bar-close"
        title="Close"
        onClick={e => { e.stopPropagation(); onClose(); }}
      >
        <svg width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>
  );
}