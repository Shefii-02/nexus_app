// src/modules/conversations/components/ChatWindow.tsx
import { useEffect, useRef, useState } from 'react';
import { useMessages } from '../hooks/useMessages';
import { MessageList } from './MessageList';
import { MessageComposer } from './MessageComposer';
import { ChatHeader } from './ChatHeader';
import { GroupInfoDrawer } from './GroupInfoDrawer';
import { UserInfoDrawer } from './UserInfoDrawer';
import type { Conversation, Message } from '../services/chatService';

interface Props {
  conversation: Conversation;
  currentUserId: number;
  currentUserName: string;
  onBack: () => void;
}

export function ChatWindow({ conversation, currentUserId, onBack }: Props) {
  const [replyTo, setReplyTo] = useState<Message | null>(null);
  const [showInfo, setShowInfo] = useState(false);
  const bottomRef = useRef<HTMLDivElement>(null);
  const isFirstLoad = useRef(true);

  const {
    messages,
    loading,
    hasMore,
    typingUsers,
    loadMore,
    sendMsg,
    editMsg,
    deleteMsg,
    reactToMsg,
    removeReact,
    sendTypingSignal,
  } = useMessages(conversation.id);

  // Scroll to bottom on new messages
  useEffect(() => {
    if (isFirstLoad.current || messages.length > 0) {
      bottomRef.current?.scrollIntoView({ behavior: isFirstLoad.current ? 'auto' : 'smooth' });
      isFirstLoad.current = false;
    }
  }, [messages.length]);

  const handleSend = async (params: { message?: string; type?: string; media?: File; reply_to?: number }) => {
    await sendMsg({ ...params, reply_to: replyTo?.id ?? params.reply_to });
    setReplyTo(null);
  };

  return (
    <div className="chat-window">
      <ChatHeader
        conversation={conversation}
        currentUserId={currentUserId}
        typingUsers={typingUsers}
        onBack={onBack}
        onInfoOpen={() => setShowInfo(true)}
      />

      <MessageList
        messages={messages}
        loading={loading}
        hasMore={hasMore}
        currentUserId={currentUserId}
        onLoadMore={loadMore}
        onReply={setReplyTo}
        onEdit={editMsg}
        onDelete={deleteMsg}
        onReact={reactToMsg}
        onRemoveReact={removeReact}
      />

      {/* Bottom anchor */}
      <div ref={bottomRef} />

      <MessageComposer
        replyTo={replyTo}
        onCancelReply={() => setReplyTo(null)}
        onSend={handleSend}
        onTyping={sendTypingSignal}
      />

      {/* Info Drawers */}
      {showInfo && conversation.type === 'group' && (
        <GroupInfoDrawer
          conversation={conversation}
          currentUserId={currentUserId}
          onClose={() => setShowInfo(false)}
        />
      )}
      {showInfo && conversation.type === 'individual' && (
        <UserInfoDrawer
          user={conversation.other_user!}
          onClose={() => setShowInfo(false)}
        />
      )}
    </div>
  );
}
