// src/modules/conversations/components/MessageList.tsx
// import { useRef, useCallback, useLayoutEffect, forwardRef, useImperativeHandle } from 'react';
import { useRef, useCallback, useLayoutEffect, useEffect, forwardRef, useImperativeHandle } from 'react';
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
  canSend: boolean; 
}

export interface MessageListHandle {
  scrollToMessage: (messageId: number) => void;
}

export const MessageList = forwardRef<MessageListHandle, Props>(function MessageList(
  {
    messages, loading, hasMore, currentUserId, currentUserRole,
    onLoadMore, onReply, onEdit, onDelete, onReact, onRemoveReact,
    onForward, onPin, onReport,canSend
  },
  ref,
) {
  const listRef = useRef<HTMLDivElement>(null);
  const bottomRef = useRef<HTMLDivElement>(null);
  const msgRefs = useRef<Record<number, HTMLDivElement | null>>({});
  const isFirstLoad = useRef(true);
  const firstIdRef = useRef<number | null>(null);
  const lastIdRef = useRef<number | null>(null);
  const prevScrollHeightRef = useRef(0);

  // useImperativeHandle(ref, () => ({
  //   scrollToMessage(messageId: number) {
  //     const el = msgRefs.current[messageId];
  //     if (!el) return;
  //     el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  //     el.classList.add('msg-highlight');
  //     setTimeout(() => el.classList.remove('msg-highlight'), 1500);
  //   },
  // }));
  // ✅ replace with this
  const requestLoadMore = useCallback(() => {
    const list = listRef.current;
    if (list) prevScrollHeightRef.current = list.scrollHeight;
    onLoadMore();
  }, [onLoadMore]);

  const scrollToMessage = useCallback((messageId: number, attempts = 0) => {
    const el = msgRefs.current[messageId];
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('msg-highlight');
      setTimeout(() => el.classList.remove('msg-highlight'), 1500);
      return;
    }
    // not loaded yet — page backwards and retry, up to 6 tries
    if (attempts < 6 && hasMore && !loading) {
      requestLoadMore();
      setTimeout(() => scrollToMessage(messageId, attempts + 1), 350);
    }
  }, [hasMore, loading, requestLoadMore]);

  useImperativeHandle(ref, () => ({ scrollToMessage }));

  // ── Single source of truth for scroll behaviour ─────────────────────────────
  useLayoutEffect(() => {
    const list = listRef.current;

    if (messages.length === 0) {
      firstIdRef.current = null;
      lastIdRef.current = null;
      isFirstLoad.current = true;
      return;
    }

    const newFirstId = messages[0].id;
    const newLastId = messages[messages.length - 1].id;

    if (isFirstLoad.current) {
      // Very first render of this conversation → jump to bottom
      bottomRef.current?.scrollIntoView({ behavior: 'instant' });
      isFirstLoad.current = false;
    } else if (
      firstIdRef.current !== null &&
      newFirstId !== firstIdRef.current &&
      newLastId === lastIdRef.current
    ) {
      // Older messages were PREPENDED (pagination) → restore visual position,
      // never scroll to bottom for this case
      if (list) {
        const newHeight = list.scrollHeight;
        list.scrollTop = list.scrollTop + (newHeight - prevScrollHeightRef.current);
      }
    } else if (lastIdRef.current !== null && newLastId !== lastIdRef.current) {
      // A NEW message arrived at the bottom → only auto-scroll if user was already near bottom
      if (list) {
        const distFromBottom = list.scrollHeight - list.scrollTop - list.clientHeight;
        if (distFromBottom < 200) {
          bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
        }
      }
    }

    firstIdRef.current = newFirstId;
    lastIdRef.current = newLastId;
  }, [messages]);

  // ── Intersection observer for loading older messages ────────────────────────
  // const topSentinel = useCallback(
  //   (node: HTMLDivElement | null) => {
  //     if (!node) return;
  //     const obs = new IntersectionObserver(
  //       ([e]) => {
  //         if (e.isIntersecting && hasMore && !loading) {
  //           const list = listRef.current;
  //           if (list) prevScrollHeightRef.current = list.scrollHeight; // capture BEFORE fetch
  //           onLoadMore();
  //         }
  //       },
  //       { threshold: 0.1 },
  //     );
  //     obs.observe(node);
  //     return () => obs.disconnect();
  //   },
  //   [hasMore, loading, onLoadMore],
  // );
  // ✅ new
  // ✅ add near your other refs
  const topSentinelRef = useRef<HTMLDivElement>(null);

  // ✅ replace the callback-ref logic with a proper effect
  useEffect(() => {
    const node = topSentinelRef.current;
    if (!node) return;
    const obs = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting && hasMore && !loading) requestLoadMore(); },
      { threshold: 0.1 },
    );
    obs.observe(node);
    return () => obs.disconnect();   // ✅ this is a valid useEffect cleanup, works everywhere
  }, [hasMore, loading, requestLoadMore]);

  const grouped: { date: string; messages: Message[] }[] = [];
  let curDate = '';
  messages.forEach(msg => {
    const d = new Date(msg.created_at).toDateString();
    if (d !== curDate) { curDate = d; grouped.push({ date: d, messages: [] }); }
    grouped[grouped.length - 1].messages.push(msg);
  });

  return (
    <div className="message-list" ref={listRef}>
      <div ref={topSentinelRef}>
        {loading && <div className="loading-spinner"><div className="spinner" /></div>}
      </div>

      {grouped.map(group => (
        <div key={group.date}>
          <div className="date-separator"><span>{formatDateGroup(group.date)}</span></div>
          {group.messages.map((msg, i) => {
            const isMine = msg.sender_id === currentUserId;
            const prev = group.messages[i - 1];
            const showAvatar = !isMine && (!prev || prev.sender_id !== msg.sender_id);
            return (
              <div key={msg.id} ref={el => { msgRefs.current[msg.id] = el; }}>
                <MessageBubble
                  message={msg}
                  isMine={isMine}
                  showAvatar={showAvatar}
                  currentUserId={currentUserId}
                  currentUserRole={currentUserRole ?? ''}
                  canSend={canSend} 
                  onReply={() => onReply(msg)}
                  onEdit={text => onEdit(msg.id, text)}
                  onDelete={fe => onDelete(msg.id, fe)}
                  onReact={r => onReact(msg.id, r)}
                  onRemoveReact={() => onRemoveReact(msg.id)}
                  onForward={() => onForward(msg)}
                  onPin={() => onPin(msg.id)}
                  onReport={reason => onReport(msg.id, reason)}
                  onJumpToReply={scrollToMessage}
                  
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