// src/modules/conversations/components/MessageList.tsx
import { useRef, useCallback, useEffect } from 'react';
import type { Message } from '../services/chatService';
import { MessageBubble } from './MessageBubble';
import { formatDateGroup } from '../utils';

interface Props {
  messages: Message[];
  loading: boolean;
  hasMore: boolean;
  currentUserId: number;
  onLoadMore: () => void;
  onReply: (msg: Message) => void;
  onEdit: (id: number, text: string) => void;
  onDelete: (id: number, forEveryone: boolean) => void;
  onReact: (id: number, reaction: string) => void;
  onRemoveReact: (id: number) => void;
}

export function MessageList({ messages, loading, hasMore, currentUserId, onLoadMore, onReply, onEdit, onDelete, onReact, onRemoveReact }: Props) {
  const listRef = useRef<HTMLDivElement>(null);
  const bottomRef = useRef<HTMLDivElement>(null);
  const isFirstLoad = useRef(true);
  const prevMessageCount = useRef(0);

  // Scroll to bottom on first load and new messages
  useEffect(() => {
    if (!bottomRef.current) return;

    const newMessageArrived = messages.length > prevMessageCount.current;
    const lastMsg = messages[messages.length - 1];
    const isMyMessage = lastMsg?.sender_id === currentUserId;

    if (isFirstLoad.current || isMyMessage || newMessageArrived) {
      bottomRef.current.scrollIntoView({ behavior: isFirstLoad.current ? 'instant' : 'smooth' });
      isFirstLoad.current = false;
    }

    prevMessageCount.current = messages.length;
  }, [messages, currentUserId]);

  // Reset on conversation change (messages array reset to empty then refills)
  useEffect(() => {
    if (messages.length === 0) {
      isFirstLoad.current = true;
      prevMessageCount.current = 0;
    }
  }, [messages.length]);

  // Intersection observer for infinite scroll (load older messages)
  const observerRef = useCallback((node: HTMLDivElement | null) => {
    if (!node) return;
    const observer = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting && hasMore && !loading) {
        onLoadMore();
      }
    }, { threshold: 0.1 });
    observer.observe(node);
    return () => observer.disconnect();
  }, [hasMore, loading, onLoadMore]);

  // Group messages by date
  const grouped: { date: string; messages: Message[] }[] = [];
  let currentDate = '';
  messages.forEach(msg => {
    const d = new Date(msg.created_at).toDateString();
    if (d !== currentDate) {
      currentDate = d;
      grouped.push({ date: d, messages: [] });
    }
    grouped[grouped.length - 1].messages.push(msg);
  });

  return (
    <div className="message-list" ref={listRef}>
      {/* Load more trigger at top */}
      <div ref={observerRef}>
        {loading && (
          <div className="loading-spinner">
            <div className="spinner" />
          </div>
        )}
      </div>

      {grouped.map(group => (
        <div key={group.date}>
          <div className="date-separator">
            <span>{formatDateGroup(group.date)}</span>
          </div>
          {group.messages.map((msg, i) => {
            const isMine = msg.sender_id === currentUserId;
            const prevMsg = group.messages[i - 1];
            const showAvatar = !isMine && (!prevMsg || prevMsg.sender_id !== msg.sender_id);
            return (
              <MessageBubble
                key={msg.id}
                message={msg}
                isMine={isMine}
                showAvatar={showAvatar}
                currentUserId={currentUserId}
                onReply={() => onReply(msg)}
                onEdit={(text) => onEdit(msg.id, text)}
                onDelete={(forEveryone) => onDelete(msg.id, forEveryone)}
                onReact={(reaction) => onReact(msg.id, reaction)}
                onRemoveReact={() => onRemoveReact(msg.id)}
              />
            );
          })}
        </div>
      ))}

      {/* Scroll anchor */}
      <div ref={bottomRef} />
    </div>
  );
}