// src/modules/conversations/components/ConversationSidebar.tsx
import { useState } from 'react';
import { ConversationList } from './ConversationList';
import { ConversationSearch } from './ConversationSearch';
import { ConversationTabs } from './ConversationTabs';
import { CreateConversationModal } from './CreateConversationModal';
import { CreateGroupModal } from './CreateGroupModal';
import type { Conversation } from '../services/chatService';

interface Props {
  conversations: Conversation[];
  loading: boolean;
  activeId: number | null;
  currentUserId: number;
  onSelect: (conv: Conversation) => void;
  onCreateIndividual: (userId: number, moduleId?: number) => Promise<Conversation>;
  onCreateGroup: (title: string, userIds: number[], moduleId?: number) => Promise<Conversation>;
  onMute: (id: number) => void;
  onPin: (id: number) => void;
}

export function ConversationSidebar(props: Props) {
  const [search, setSearch] = useState('');
  const [tab, setTab] = useState<'all' | 'unread' | 'groups'>('all');
  const [showNewChat, setShowNewChat] = useState(false);
  const [showNewGroup, setShowNewGroup] = useState(false);

  const filtered = props.conversations.filter(conv => {
    const name = conv.type === 'single'
      ? conv.other_user?.name ?? ''
      : conv.title ?? '';

    const matchesSearch = name.toLowerCase().includes(search.toLowerCase()) ||
      (conv.last_message?.message ?? '').toLowerCase().includes(search.toLowerCase());

    if (tab === 'unread') return matchesSearch && conv.unread_count > 0;
    if (tab === 'groups') return matchesSearch && conv.type === 'group';
    return matchesSearch;
  });

  return (
    <div className="sidebar">
      {/* Header */}
      <div className="sidebar-header">
        <h2 className="sidebar-title">Chats</h2>
        <div className="sidebar-actions">
          <button className="icon-btn" onClick={() => setShowNewGroup(true)} title="New Group">
            <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </button>
          <button className="icon-btn" onClick={() => setShowNewChat(true)} title="New Chat">
            <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
              <line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
          </button>
        </div>
      </div>

      <ConversationSearch value={search} onChange={setSearch} />
      <ConversationTabs active={tab} onChange={setTab} />

      <ConversationList
        conversations={filtered}
        loading={props.loading}
        activeId={props.activeId}
        currentUserId={props.currentUserId}
        onSelect={props.onSelect}
        onMute={props.onMute}
        onPin={props.onPin}
      />

      {showNewChat && (
        <CreateConversationModal
          onClose={() => setShowNewChat(false)}
          onCreate={async (userId, moduleId) => {
            const conv = await props.onCreateIndividual(userId, moduleId);
            props.onSelect(conv);
            setShowNewChat(false);
          }}
        />
      )}

      {showNewGroup && (
        <CreateGroupModal
          onClose={() => setShowNewGroup(false)}
          onCreate={async (title, userIds, moduleId) => {
            const conv = await props.onCreateGroup(title, userIds, moduleId);
            props.onSelect(conv);
            setShowNewGroup(false);
          }}
        />
      )}
    </div>
  );
}
