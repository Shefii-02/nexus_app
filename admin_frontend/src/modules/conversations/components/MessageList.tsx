// src/modules/conversations/components/MessageList.tsx
import { useRef, useCallback, useEffect, forwardRef, useImperativeHandle } from 'react';
import type { Message } from '../services/chatService';
import { MessageBubble } from './MessageBubble';
import { formatDateGroup } from '../utils';

interface Props {
  messages: Message[];
  loading: boolean;
  hasMore: boolean;
  currentUserId: number;
  currentUserRole?: string;
  onLoadMore: () => void;
  onReply: (msg: Message) => void;
  onEdit: (id: number, text: string) => void;
  onDelete: (id: number, forEveryone: boolean) => void;
  onReact: (id: number, reaction: string) => void;
  onRemoveReact: (id: number) => void;
  onForward: (msg: Message) => void;
  onPin: (id: number) => void;
  onReport: (id: number, reason: string) => void;
}

export interface MessageListHandle {
  scrollToMessage: (messageId: number) => void;
}

export const MessageList = forwardRef<MessageListHandle, Props>(function MessageList(
  {
    messages, loading, hasMore, currentUserId, currentUserRole,
    onLoadMore, onReply, onEdit, onDelete, onReact, onRemoveReact,
    onForward, onPin, onReport,
  },
  ref,
) {
  const listRef          = useRef<HTMLDivElement>(null);
  const bottomRef        = useRef<HTMLDivElement>(null);
  const msgRefs          = useRef<Record<number, HTMLDivElement | null>>({});
  const isFirstLoad      = useRef(true);
  const prevCount        = useRef(0);

  // Expose scrollToMessage to parent (for pinned bar click)
  useImperativeHandle(ref, () => ({
    scrollToMessage(messageId: number) {
      const el = msgRefs.current[messageId];
      if (!el) return;
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      // Flash highlight
      el.classList.add('msg-highlight');
      setTimeout(() => el.classList.remove('msg-highlight'), 1500);
    },
  }));

  // Auto-scroll to bottom
  useEffect(() => {
    const newCount = messages.length;
    if (newCount === 0) return;

    if (isFirstLoad.current) {
      bottomRef.current?.scrollIntoView({ behavior: 'instant' });
      isFirstLoad.current = false;
    } else if (newCount > prevCount.current) {
      // Only scroll if user is near bottom
      const list = listRef.current;
      if (list) {
        const distFromBottom = list.scrollHeight - list.scrollTop - list.clientHeight;
        if (distFromBottom < 200) {
          bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
        }
      }
    }
    prevCount.current = newCount;
  }, [messages.length]);

  // Reset on conversation change
  useEffect(() => {
    if (messages.length === 0) {
      isFirstLoad.current = true;
      prevCount.current   = 0;
    }
  }, [messages.length]);

  // Intersection observer for loading older messages
  const topSentinel = useCallback(
    (node: HTMLDivElement | null) => {
      if (!node) return;
      const obs = new IntersectionObserver(
        ([e]) => { if (e.isIntersecting && hasMore && !loading) onLoadMore(); },
        { threshold: 0.1 },
      );
      obs.observe(node);
      return () => obs.disconnect();
    },
    [hasMore, loading, onLoadMore],
  );

  // Group by calendar date
  const grouped: { date: string; messages: Message[] }[] = [];
  let curDate = '';
  messages.forEach(msg => {
    const d = new Date(msg.created_at).toDateString();
    if (d !== curDate) { curDate = d; grouped.push({ date: d, messages: [] }); }
    grouped[grouped.length - 1].messages.push(msg);
  });

  return (
    <div className="message-list" ref={listRef}>
      {/* Top sentinel */}
      <div ref={topSentinel}>
        {loading && <div className="loading-spinner"><div className="spinner" /></div>}
      </div>

      {grouped.map(group => (
        <div key={group.date}>
          <div className="date-separator"><span>{formatDateGroup(group.date)}</span></div>

          {group.messages.map((msg, i) => {
            const isMine     = msg.sender_id === currentUserId;
            const prev       = group.messages[i - 1];
            const showAvatar = !isMine && (!prev || prev.sender_id !== msg.sender_id);

            return (
              <div
                key={msg.id}
                ref={el => { msgRefs.current[msg.id] = el; }}
              >
                <MessageBubble
                  message={msg}
                  isMine={isMine}
                  showAvatar={showAvatar}
                  currentUserId={currentUserId}
                  currentUserRole={currentUserRole}
                  onReply={() => onReply(msg)}
                  onEdit={text => onEdit(msg.id, text)}
                  onDelete={fe => onDelete(msg.id, fe)}
                  onReact={r => onReact(msg.id, r)}
                  onRemoveReact={() => onRemoveReact(msg.id)}
                  onForward={() => onForward(msg)}
                  onPin={() => onPin(msg.id)}
                  onReport={reason => onReport(msg.id, reason)}
                />
              </div>
            );
          })}
        </div>
      ))}

      <div ref={bottomRef} />
    </div>
  );
});