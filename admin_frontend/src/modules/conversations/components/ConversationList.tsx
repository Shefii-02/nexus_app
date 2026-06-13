// src/modules/conversations/components/ConversationList.tsx

import type { Conversation } from "../services/chatService";
import { ConversationCard } from "./ConversationCard";

interface Props {
  conversations: Conversation[];
  loading: boolean;
  activeId: number | null;
  currentUserId: number;
  onSelect: (conv: Conversation) => void;
  onMute: (id: number) => void;
  onPin: (id: number) => void;
}

export function ConversationList({ conversations, loading, activeId, currentUserId, onSelect, onMute, onPin }: Props) {
  if (loading) {
    return (
      <div className="conv-list">
        {[1, 2, 3, 4, 5].map(i => (
          <div key={i} className="conv-skeleton">
            <div className="skeleton-avatar" />
            <div className="skeleton-lines">
              <div className="skeleton-line w-60" />
              <div className="skeleton-line w-40" />
            </div>
          </div>
        ))}
      </div>
    );
  }

  if (conversations.length === 0) {
    return (
      <div className="conv-empty">
        <span>No conversations found</span>
      </div>
    );
  }

  return (
    <div className="conv-list">
      {conversations.map(conv => (
        <ConversationCard
          key={conv.id}
          conversation={conv}
          isActive={conv.id === activeId}
          currentUserId={currentUserId}
          onSelect={() => onSelect(conv)}
          onMute={() => onMute(conv.id)}
          onPin={() => onPin(conv.id)}
        />
      ))}
    </div>
  );
}