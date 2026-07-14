// src/modules/conversations/components/ChatWindow.tsx
import { useRef, useState } from 'react';
import { useMessages } from '../hooks/useMessages';
import { MessageList, type MessageListHandle } from './MessageList';
import { MessageComposer } from './MessageComposer';
import { ChatHeader } from './ChatHeader';
import { GroupInfoDrawer } from './GroupInfoDrawer';
import { UserInfoDrawer } from './UserInfoDrawer';
import { ForwardModal } from './ForwardModal';
import { PinnedMessageBar } from './PinnedMessageBar';
import type { Conversation, Message } from '../services/chatService';
import { canUserSend } from '../utils';


interface Props {
  conversation: Conversation;
  conversations: Conversation[];
  currentUserId: number;
  currentUserName: string;
  currentUserRole?: string;           // 'admin' | 'staff' | 'student' …
  onBack: () => void;
  onNewMessage?: (conversationId: number, message: Message) => void;
}

export function ChatWindow({
  conversation,
  conversations,
  currentUserId,
  currentUserName,
  currentUserRole,
  onBack,
  onNewMessage,
}: Props) {
  const [replyTo, setReplyTo] = useState<Message | null>(null);
  const [forwardMsg, setForwardMsg] = useState<Message | null>(null);
  const [showInfo, setShowInfo] = useState(false);
  const [showPinnedBar, setShowPinnedBar] = useState(true);

  const msgListRef = useRef<MessageListHandle>(null);

  const {
    messages,
    pinnedMsgs,
    loading,
    hasMore,
    typingUsers,
    loadMore,
    sendMsg,
    editMsg,
    deleteMsg,
    reactToMsg,
    removeReact,
    pinMsg,
    reportMsg,
    sendTypingSignal,
  } = useMessages(conversation.id, currentUserId, onNewMessage);
  //  = useMessages(conversation.id, onNewMessage);
  const canSend = canUserSend(currentUserRole, conversation.reply_permission);

  const handleSend = async (params: {
    message?: string;
    type?: string;
    media?: File;
    reply_to?: number;
  }) => {
    // reply_to is always set from replyTo state here — composer also passes it
    // but this is the authoritative source
    await sendMsg({
      ...params,
      reply_to: replyTo?.id ?? params.reply_to,
    });
    setReplyTo(null);
  };

  const handleScrollToPin = (messageId: number) => {
    msgListRef.current?.scrollToMessage(messageId);
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

      {/* Pinned message banner */}
      {showPinnedBar && pinnedMsgs.length > 0 && (
        <PinnedMessageBar
          pinnedMsgs={pinnedMsgs}
          onScrollTo={handleScrollToPin}
          onClose={() => setShowPinnedBar(false)}
          onUnpin={pinMsg}
        />
      )}

      <MessageList
        ref={msgListRef}
        messages={messages}
        loading={loading}
        hasMore={hasMore}
        currentUserId={currentUserId}
        currentUserRole={currentUserRole}
        canSend={canSend}
        onLoadMore={loadMore}
        onReply={setReplyTo}
        onEdit={editMsg}
        onDelete={deleteMsg}
        onReact={reactToMsg}
        onRemoveReact={removeReact}
        onForward={setForwardMsg}
        onPin={pinMsg}
        onReport={reportMsg}
      />

      {canSend ? (
        <MessageComposer
          replyTo={replyTo}
          onCancelReply={() => setReplyTo(null)}
          onSend={handleSend}
          onTyping={sendTypingSignal}
        />
      ) : (
        <div className="composer-locked">
          🔒 Only {conversation.reply_permission === 'admin' ? 'admins'
            : conversation.reply_permission === 'staff' ? 'admins and staff'
              : 'admins, staff and teachers'} can send messages here. You can still react to messages.
        </div>
      )}

      {/* <MessageComposer
        replyTo={replyTo}
        onCancelReply={() => setReplyTo(null)}
        onSend={handleSend}
        onTyping={sendTypingSignal}
      /> */}

      {showInfo && conversation.type === 'group' && (
        <GroupInfoDrawer
          conversation={conversation}
          currentUserId={currentUserId}
          onClose={() => setShowInfo(false)}
        />
      )}
      {showInfo && conversation.type === 'single' && (
        <UserInfoDrawer
          user={conversation.other_user!}
          onClose={() => setShowInfo(false)}
        />
      )}

      {forwardMsg && (
        <ForwardModal
          message={forwardMsg}
          conversations={conversations}
          currentUserId={currentUserId}
          onClose={() => setForwardMsg(null)}
        />
      )}
    </div>
  );
}