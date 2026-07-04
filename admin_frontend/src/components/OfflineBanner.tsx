// src/components/OfflineBanner.tsx
import React from 'react'
import { useSelector } from 'react-redux'
import type { RootState } from '../store'

export const OfflineBanner: React.FC = () => {
  const isOnline = useSelector((state: RootState) => state.network.isOnline)

  if (isOnline) return null

  return (
    <div style={{
      backgroundColor: '#E63946',
      color: '#FFF',
      textAlign: 'center',
      padding: '8px',
      fontSize: '14px',
      fontWeight: 'bold',
      width: '100%'
    }}>
      You are currently offline. Displaying cached dashboard data.
    </div>
  )
}
