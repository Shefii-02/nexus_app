// src/modules/conversations/ConversationPage.tsx
import { useState } from 'react';
import { ConversationSidebar } from './components/ConversationSidebar';
import { ChatWindow } from './components/ChatWindow';
import { useConversations } from './hooks/useConversations';
import { useRealtime } from './hooks/useRealtime';
import './styles/chat.css';
import type { Conversation } from './services/chatService';
import { useAuthUser } from '../../hooks/useAuthUser';

export default function ConversationPage() {
  const { id: currentUserId, name: currentUserName, avatar: currentUserAvatar } = useAuthUser();

  const [activeConversation, setActiveConversation] = useState<Conversation | null>(null);
  const [mobileView, setMobileView] = useState<'list' | 'chat'>('list');

  useRealtime(currentUserId);

  const {
    conversations,
    loading,
    startIndividualChat,
    startGroupChat,
    handleToggleMute,
    handleTogglePin,
    markConversationRead,
  } = useConversations();

  const handleSelectConversation = (conv: Conversation) => {
    setActiveConversation(conv);
    markConversationRead(conv.id);
    setMobileView('chat');
  };

  const handleBack = () => {
    setMobileView('list');
    setActiveConversation(null);
  };

  return (
    <div className="chat-root">
      <div className={`chat-sidebar ${mobileView === 'chat' ? 'mobile-hidden' : ''}`}>
        <ConversationSidebar
          conversations={conversations}
          loading={loading}
          activeId={activeConversation?.id ?? null}
          currentUserId={currentUserId}
          onSelect={handleSelectConversation}
          onCreateIndividual={startIndividualChat}
          onCreateGroup={startGroupChat}
          onMute={handleToggleMute}
          onPin={handleTogglePin}
        />
      </div>

      <div className={`chat-main ${mobileView === 'list' ? 'mobile-hidden' : ''}`}>
        {activeConversation ? (
          <ChatWindow
            conversation={activeConversation}
            currentUserId={currentUserId}
            currentUserName={currentUserName}
            onBack={handleBack}
          />
        ) : (
          <div className="chat-empty-state">
            <div className="chat-empty-icon">💬</div>
            <h3>Select a conversation</h3>
            <p>Choose from your existing conversations or start a new one</p>
          </div>
        )}
      </div>
    </div>
  );
}