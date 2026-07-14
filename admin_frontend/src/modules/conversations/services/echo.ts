// src/modules/conversations/services/echo.ts
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
  interface Window {
    Pusher: typeof Pusher;
    Echo: Echo<any>;
  }
}

window.Pusher = Pusher;

let echoInstance: Echo<any> | null = null;

export function getEcho(): Echo<any> {
  if (echoInstance) return echoInstance;

  const token = localStorage.getItem('token');

  echoInstance = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: import.meta.env.VITE_API_URL + '/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    },
  });

  return echoInstance;
}

export function disconnectEcho(): void {
  echoInstance?.disconnect();
  echoInstance = null;
}

export function subscribeToConversation(
  conversationId: number,
  handlers: {
    onMessage?: (data: any) => void;
    onMessageUpdated?: (data: any) => void;
    onMessageDeleted?: (data: any) => void;
    onMessageRead?: (data: any) => void;
    onTyping?: (data: any) => void;
    onReaction?: (data: any) => void;
    onReactionRemoved?: (data: any) => void; // ✅ new

  }
) {
  const echo = getEcho();
  const channel = echo.private(`conversation.${conversationId}`);

  if (handlers.onMessage)        channel.listen('.message.sent',    handlers.onMessage);
  if (handlers.onMessageUpdated) channel.listen('.message.updated', handlers.onMessageUpdated);
  if (handlers.onMessageDeleted) channel.listen('.message.deleted', handlers.onMessageDeleted);
  if (handlers.onMessageRead)    channel.listen('.message.read',    handlers.onMessageRead);
  if (handlers.onReaction)       channel.listen('.reaction.added',  handlers.onReaction);
  if (handlers.onReactionRemoved) channel.listen('.reaction.removed', handlers.onReactionRemoved); // ✅

  const presenceChannel = echo.join(`conversation.${conversationId}`);
  if (handlers.onTyping) presenceChannel.listenForWhisper('typing', handlers.onTyping);

  return () => {
    echo.leave(`conversation.${conversationId}`);
  };
}

export function whisperTyping(conversationId: number, typing: boolean, userId: number, userName: string) {
  const echo = getEcho();
  echo.join(`conversation.${conversationId}`)
      .whisper('typing', { user_id: userId, user_name: userName, typing });
}