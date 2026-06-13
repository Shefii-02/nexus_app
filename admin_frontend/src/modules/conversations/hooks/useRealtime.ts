// src/modules/conversations/hooks/useRealtime.ts
import { useEffect, useRef } from 'react';
import { getEcho, disconnectEcho } from '../services/echo';

export function useRealtime(userId: number | null) {
  const connected = useRef(false);

  useEffect(() => {
    if (!userId || connected.current) return;
    connected.current = true;

    const echo = getEcho();

    // Listen to global user status channel
    echo.channel('user-status')
        .listen('.user.status', (data: { user_id: number; online: boolean; last_seen: string }) => {
          // Dispatch a custom event so any component can react
          window.dispatchEvent(new CustomEvent('user-status', { detail: data }));
        });

    return () => {
      disconnectEcho();
      connected.current = false;
    };
  }, [userId]);
}
