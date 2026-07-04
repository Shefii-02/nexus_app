// src/store/networkSlice.ts
import  { createSlice, type PayloadAction } from '@reduxjs/toolkit'

interface NetworkState {
  isOnline: boolean
}

const initialState: NetworkState = {
  isOnline: navigator.onLine,
}

const networkSlice = createSlice({
  name: 'network',
  initialState,
  reducers: {
    setOnlineStatus: (state, action: PayloadAction<boolean>) => {
      state.isOnline = action.payload
    },
  },
})

export const { setOnlineStatus } = networkSlice.actions
export default networkSlice.reducer
