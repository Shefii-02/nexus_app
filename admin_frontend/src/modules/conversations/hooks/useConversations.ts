// src/modules/conversations/hooks/useConversations.ts
import { useState, useEffect, useCallback } from 'react';
import {
  getConversations,
  createIndividualChat,
  createGroupChat,
  toggleMute,
  togglePin,
  type Conversation,
} from '../services/chatService';
import { getEcho } from '../services/echo';

export function useConversations() {
  const [conversations, setConversations] = useState<Conversation[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const loadConversations = useCallback(async () => {
    try {
      setLoading(true);
      const data = await getConversations();
      // Sort: pinned first, then by last message time
      const sorted = (data.data ?? []).sort((a, b) => {
        if (a.is_pinned !== b.is_pinned) return a.is_pinned ? -1 : 1;
        const aTime = a.last_message?.created_at ?? a.updated_at;
        const bTime = b.last_message?.created_at ?? b.updated_at;
        return new Date(bTime).getTime() - new Date(aTime).getTime();
      });
      setConversations(sorted);
    } catch (e) {
      setError('Failed to load conversations');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadConversations();
  }, [loadConversations]);

  // Listen for new messages to update conversation list
  useEffect(() => {
    const echo = getEcho();
    // Listen on each conversation channel for updates
    conversations.forEach((conv) => {
      echo.private(`conversation.${conv.id}`)
          .listen('.message.sent', (data: any) => {
            setConversations(prev => prev.map(c =>
              c.id === conv.id
                ? { ...c, last_message: data.message, unread_count: c.unread_count + 1 }
                : c
            ));
          });
    });

    return () => {
      conversations.forEach(conv => echo.leave(`conversation.${conv.id}`));
    };
  }, [conversations.length]);

  const startIndividualChat = useCallback(async (userId: number, moduleId?: number) => {
    const conv = await createIndividualChat(userId, moduleId);
    setConversations(prev => {
      const exists = prev.find(c => c.id === conv.id);
      if (exists) return prev;
      return [conv, ...prev];
    });
    return conv;
  }, []);

  const startGroupChat = useCallback(async (title: string, userIds: number[], moduleId?: number) => {
    const conv = await createGroupChat(title, userIds, moduleId);
    setConversations(prev => [conv, ...prev]);
    return conv;
  }, []);

  const handleToggleMute = useCallback(async (conversationId: number) => {
    const result = await toggleMute(conversationId);
    setConversations(prev => prev.map(c =>
      c.id === conversationId ? { ...c, is_muted: result.is_muted } : c
    ));
  }, []);

  const handleTogglePin = useCallback(async (conversationId: number) => {
    const result = await togglePin(conversationId);
    setConversations(prev => {
      const updated = prev.map(c =>
        c.id === conversationId ? { ...c, is_pinned: result.is_pinned } : c
      );
      return updated.sort((a, b) => {
        if (a.is_pinned !== b.is_pinned) return a.is_pinned ? -1 : 1;
        return 0;
      });
    });
  }, []);

  const markConversationRead = useCallback((conversationId: number) => {
    setConversations(prev => prev.map(c =>
      c.id === conversationId ? { ...c, unread_count: 0 } : c
    ));
  }, []);

  return {
    conversations,
    loading,
    error,
    loadConversations,
    startIndividualChat,
    startGroupChat,
    handleToggleMute,
    handleTogglePin,
    markConversationRead,
  };
}
