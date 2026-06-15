// src/modules/conversations/hooks/useMessages.ts
import { useState, useEffect, useCallback, useRef } from 'react';
import {
  type Message,
  getMessages,
  getPinnedMessages,
  sendMessage as apiSend,
  editMessage as apiEdit,
  deleteMessage as apiDelete,
  markRead,
  addReaction as apiReact,
  removeReaction as apiRemoveReact,
  togglePinMessage,
  reportMessage as apiReport,
  sendTyping,
} from '../services/chatService';
import { subscribeToConversation } from '../services/echo';

export function useMessages(
  conversationId: number | null,
  onNewMessage?: (conversationId: number, message: Message) => void,
) {
  const [messages,     setMessages]     = useState<Message[]>([]);
  const [pinnedMsgs,   setPinnedMsgs]   = useState<Message[]>([]);
  const [loading,      setLoading]      = useState(false);
  const [hasMore,      setHasMore]      = useState(true);
  const [typingUsers,  setTypingUsers]  = useState<{ user_id: number; user_name: string }[]>([]);

  // cursor in a ref so it's never stale inside loadMessages callback
  const cursorRef     = useRef<string | undefined>(undefined);
  const typingTimers  = useRef<Record<number, ReturnType<typeof setTimeout>>>({});

  // ── Load ────────────────────────────────────────────────────────────────────
  const loadMessages = useCallback(async (reset = false) => {
    if (!conversationId) return;
    setLoading(true);
    try {
      const data    = await getMessages(conversationId, reset ? undefined : cursorRef.current);
      const newMsgs: Message[] = data.data ?? [];
      setMessages(prev =>
        reset ? [...newMsgs].reverse() : [...[...newMsgs].reverse(), ...prev],
      );
      setHasMore(!!data.next_cursor);
      cursorRef.current = data.next_cursor;
      if (reset) markRead(conversationId).catch(() => {});
    } catch { /* silent */ } finally {
      setLoading(false);
    }
  }, [conversationId]);

  const loadPinned = useCallback(async () => {
    if (!conversationId) return;
    try {
      const msgs = await getPinnedMessages(conversationId);
      setPinnedMsgs(msgs);
    } catch { setPinnedMsgs([]); }
  }, [conversationId]);

  // Reset on conversation change
  useEffect(() => {
    if (!conversationId) return;
    setMessages([]);
    setPinnedMsgs([]);
    cursorRef.current = undefined;
    setHasMore(true);
    setTypingUsers([]);
    loadMessages(true);
    loadPinned();
  }, [conversationId]);           // intentionally not including loadMessages/loadPinned

  // ── Realtime ─────────────────────────────────────────────────────────────────
  useEffect(() => {
    if (!conversationId) return;
    const unsub = subscribeToConversation(conversationId, {
      onMessage: (data: { message: Message }) => {
        setMessages(prev =>
          prev.some(m => m.id === data.message.id) ? prev : [...prev, data.message],
        );
        markRead(conversationId).catch(() => {});
        setTypingUsers(prev => prev.filter(u => u.user_id !== data.message.sender_id));
        onNewMessage?.(conversationId, data.message);
      },
      onMessageUpdated: (data: { message: Message }) => {
        setMessages(prev => prev.map(m => m.id === data.message.id ? data.message : m));
        // also refresh pinned if this message is pinned
        setPinnedMsgs(prev => prev.map(m => m.id === data.message.id ? data.message : m));
      },
      onMessageDeleted: (data: { message_id: number }) => {
        setMessages(prev =>
          prev.map(m =>
            m.id === data.message_id
              ? { ...m, is_deleted: true, message: null, media_url: null }
              : m,
          ),
        );
        setPinnedMsgs(prev => prev.filter(m => m.id !== data.message_id));
      },
      onMessageRead: (data: { user_id: number; read_at: string }) => {
        setMessages(prev =>
          prev.map(m => ({
            ...m,
            reads: m.reads.some(r => r.user_id === data.user_id)
              ? m.reads.map(r =>
                  r.user_id === data.user_id ? { ...r, read_at: data.read_at } : r,
                )
              : [...m.reads, { message_id: m.id, user_id: data.user_id, read_at: data.read_at }],
          })),
        );
      },
      onReaction: (data: { message_id: number; user_id: number; reaction: string }) => {
        setMessages(prev =>
          prev.map(m => {
            if (m.id !== data.message_id) return m;
            const idx = m.reactions.findIndex(r => r.user_id === data.user_id);
            const reactions = [...m.reactions];
            if (idx >= 0) reactions[idx] = { ...reactions[idx], reaction: data.reaction };
            else
              reactions.push({
                id: Date.now(),
                message_id: m.id,
                user_id: data.user_id,
                reaction: data.reaction,
                user: { id: data.user_id, name: '' },
              });
            return { ...m, reactions };
          }),
        );
      },
      onTyping: (data: { user_id: number; user_name: string; typing: boolean }) => {
        if (data.typing) {
          setTypingUsers(prev =>
            prev.find(u => u.user_id === data.user_id)
              ? prev
              : [...prev, { user_id: data.user_id, user_name: data.user_name }],
          );
          clearTimeout(typingTimers.current[data.user_id]);
          typingTimers.current[data.user_id] = setTimeout(
            () => setTypingUsers(p => p.filter(u => u.user_id !== data.user_id)),
            3000,
          );
        } else {
          setTypingUsers(prev => prev.filter(u => u.user_id !== data.user_id));
        }
      },
    });
    return unsub;
  }, [conversationId]);

  // ── Actions ──────────────────────────────────────────────────────────────────
  const sendMsg = useCallback(async (params: {
    message?: string;
    type?: string;
    media?: File;
    reply_to?: number;
  }) => {
    if (!conversationId) return;
    const msg = await apiSend(conversationId, {
      message:  params.message,
      type:     params.type ?? 'text',
      media:    params.media,
      reply_to: params.reply_to,
    });
    setMessages(prev => (prev.some(m => m.id === msg.id) ? prev : [...prev, msg]));
    return msg;
  }, [conversationId]);

  const editMsg = useCallback(async (messageId: number, text: string) => {
    if (!conversationId) return;
    const updated = await apiEdit(conversationId, messageId, text);
    setMessages(prev => prev.map(m => m.id === messageId ? updated : m));
  }, [conversationId]);

  const deleteMsg = useCallback(async (messageId: number, forEveryone: boolean) => {
    if (!conversationId) return;
    await apiDelete(conversationId, messageId, forEveryone);
    if (forEveryone) {
      setMessages(prev =>
        prev.map(m =>
          m.id === messageId
            ? { ...m, is_deleted: true, message: null, media_url: null }
            : m,
        ),
      );
      setPinnedMsgs(prev => prev.filter(m => m.id !== messageId));
    } else {
      setMessages(prev => prev.filter(m => m.id !== messageId));
    }
  }, [conversationId]);

  const reactToMsg = useCallback(async (messageId: number, reaction: string) => {
    if (!conversationId) return;
    await apiReact(conversationId, messageId, reaction);
  }, [conversationId]);

  const removeReact = useCallback(async (messageId: number) => {
    if (!conversationId) return;
    await apiRemoveReact(conversationId, messageId);
  }, [conversationId]);

  const pinMsg = useCallback(async (messageId: number) => {
    if (!conversationId) return;
    await togglePinMessage(conversationId, messageId);
    // Optimistic toggle + refresh pinned list
    setMessages(prev =>
      prev.map(m => m.id === messageId ? { ...m, is_pinned: !m.is_pinned } : m),
    );
    await loadPinned();
  }, [conversationId, loadPinned]);

  const reportMsg = useCallback(async (messageId: number, reason: string) => {
    if (!conversationId) return;
    await apiReport(conversationId, messageId, reason);
  }, [conversationId]);

  const sendTypingSignal = useCallback((typing: boolean) => {
    if (!conversationId) return;
    sendTyping(conversationId, typing).catch(() => {});
  }, [conversationId]);

  return {
    messages,
    pinnedMsgs,
    loading,
    hasMore,
    typingUsers,
    loadMore:         () => loadMessages(false),
    sendMsg,
    editMsg,
    deleteMsg,
    reactToMsg,
    removeReact,
    pinMsg,
    reportMsg,
    sendTypingSignal,
  };
}