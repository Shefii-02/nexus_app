// src/modules/conversations/services/chatService.ts

import apiClient from "../../../services/apiClient";
import type { Course } from "../../courses/courseService";

export interface User {
  id: number;
  name: string;
  avatar?: string;
  email?: string;
}

export interface MediaMeta {
  original_name: string;
  mime_type: string;
  size: number;
  extension: string;
}

export interface Message {
  id: number;
  conversation_id: number;
  sender_id: number;
  sender: User;
  message: string | null;
  type: 'text' | 'image' | 'video' | 'audio' | 'file' | 'voice';
  media_url: string | null;
  media_meta: MediaMeta | null;
  reply_to: number | null;
  reply_message?: Message | null;
  is_deleted: boolean;
  is_edited: boolean;
  is_pinned: boolean;
  reactions: Reaction[];
  reads: MessageRead[];
  created_at: string;
}

export interface Reaction {
  id: number;
  message_id: number;
  user_id: number;
  user: User;
  reaction: string;
}

export interface MessageRead {
  message_id: number;
  user_id: number;
  read_at: string;
}

export interface Participant {
  id: number;
  conversation_id: number;
  user_id: number;
  user: User;
  last_read_at: string | null;
  is_muted: boolean;
  is_pinned: boolean;
  status: 'active' | 'left' | 'removed';
}

export interface Conversation {
  id: number;
  type: 'single' | 'group';
  title: string | null;
  created_by: number;
  avatar: string | null;
  module_id: number | null;
  status: 'active' | 'archived' | 'blocked';
  participants: Participant[];
  other_user?: User;
  last_message?: Message;
  unread_count: number;
  is_muted: boolean;
  is_pinned: boolean;
  created_at: string;
  updated_at: string;
}

const BASE = '/chat';

// ─── Conversations ────────────────────────────────────────────────────────────

export const getConversations = () =>
  apiClient.get<{ data: Conversation[] }>(`${BASE}/conversations`).then(r => r.data);

export const getConversation = (id: number) =>
  apiClient.get<{ conversation: Conversation }>(`${BASE}/conversations/${id}`).then(r => r.data.conversation);

export const createIndividualChat = (userId: number, moduleId?: number) =>
  apiClient.post<{ conversation: Conversation }>(`${BASE}/conversations/individual`, { user_id: userId, module_id: moduleId })
     .then(r => r.data.conversation);

export const createGroupChat = (title: string, userIds: number[], moduleId?: number, avatar?: string) =>
  apiClient.post<{ conversation: Conversation }>(`${BASE}/conversations/group`, {
    title, user_ids: userIds, module_id: moduleId, avatar,
  }).then(r => r.data.conversation);

export const updateGroup = (id: number, data: Partial<{ title: string; avatar: string; status: string }>) =>
  apiClient.put<{ conversation: Conversation }>(`${BASE}/conversations/${id}`, data).then(r => r.data.conversation);

export const leaveGroup = (id: number) =>
  apiClient.delete(`${BASE}/conversations/${id}/leave`);

export const addParticipants = (id: number, userIds: number[]) =>
  apiClient.post<{ conversation: Conversation }>(`${BASE}/conversations/${id}/participants`, { user_ids: userIds })
     .then(r => r.data.conversation);

export const toggleMute = (id: number) =>
  apiClient.post<{ is_muted: boolean }>(`${BASE}/conversations/${id}/mute`).then(r => r.data);

export const togglePin = (id: number) =>
  apiClient.post<{ is_pinned: boolean }>(`${BASE}/conversations/${id}/pin`).then(r => r.data);

export const reportConversation = (id: number, reason: string) =>
  apiClient.post(`${BASE}/conversations/${id}/report`, { reason });

// ─── Messages ─────────────────────────────────────────────────────────────────

export const getMessages = (conversationId: number, cursor?: string) =>
  apiClient.get(`${BASE}/conversations/${conversationId}/messages`, { params: { cursor } }).then(r => r.data);

export const sendMessage = (
  conversationId: number,
  data: { message?: string; type: string; media?: File; reply_to?: number }
) => {
  const form = new FormData();
  if (data.message) form.append('message', data.message);
  form.append('type', data.type);
  if (data.media) form.append('media', data.media);
  if (data.reply_to) form.append('reply_to', String(data.reply_to));

  return apiClient.post<{ message: Message }>(`${BASE}/conversations/${conversationId}/messages`, form, {
    headers: { 'Content-Type': 'multipart/form-data' },
  }).then(r => r.data.message);
};

 
export const forwardMessage = (messageId: number, conversationIds: number[]) =>
  apiClient
    .post<{ messages: Message[] }>('/messages/forward', {
      message_id: messageId,
      conversation_ids: conversationIds,
    })
    .then(r => r.data.messages);

export const editMessage = (conversationId: number, messageId: number, message: string) =>
  apiClient.put<{ message: Message }>(`${BASE}/conversations/${conversationId}/messages/${messageId}`, { message })
     .then(r => r.data.message);

export const deleteMessage = (conversationId: number, messageId: number, forEveryone = false) =>
  apiClient.delete(`${BASE}/conversations/${conversationId}/messages/${messageId}`, {
    data: { for_everyone: forEveryone },
  });

export const markRead = (conversationId: number) =>
  apiClient.post(`${BASE}/conversations/${conversationId}/messages/read`);

export const addReaction = (conversationId: number, messageId: number, reaction: string) =>
  apiClient.post(`${BASE}/conversations/${conversationId}/messages/${messageId}/react`, { reaction });

export const removeReaction = (conversationId: number, messageId: number) =>
  apiClient.delete(`${BASE}/conversations/${conversationId}/messages/${messageId}/react`);

export const sendTyping = (conversationId: number, typing: boolean) =>
  apiClient.post(`${BASE}/conversations/${conversationId}/typing`, { typing });

export const getPinnedMessages = (conversationId: number) =>
  apiClient.get<{ messages: Message[] }>(`${BASE}/conversations/${conversationId}/messages/pinned`).then(r => r.data.messages);

export const togglePinMessage = (conversationId: number, messageId: number) =>
  apiClient.post(`${BASE}/conversations/${conversationId}/messages/${messageId}/pin`);

export const reportMessage = (conversationId: number, messageId: number, reason: string) =>
  apiClient.post(`${BASE}/conversations/${conversationId}/messages/${messageId}/report`, { reason });


// ─── User Search ──────────────────────────────────────────────────────────────
 
export const searchUsers = (query: string) =>
  apiClient
    .get<{ data: User[] }>('/users/search', { params: { q: query } })
    .then(r => r.data.data ?? []);
 
// ─── Courses ──────────────────────────────────────────────────────────────────
 
export const getCourses = () =>
  apiClient.get<{ data: Course[] }>('/courses_search').then(r => r.data.data ?? []);
 

export default apiClient;