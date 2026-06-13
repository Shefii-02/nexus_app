// src/hooks/useAuthUser.ts
import { useSelector } from 'react-redux'
import type { RootState } from '../store'

export function useAuthUser() {
  const user = useSelector((state: RootState) => state.auth.user)

  return {
    id: user?.id ?? 0,
    name: user?.name ?? '',
    avatar: user?.avatar ?? undefined,
  }
}