// src/store/pwaSlice.js
import { createSlice } from '@reduxjs/toolkit'

const pwaSlice = createSlice({
  name: 'pwa',
  initialState: {
    needRefresh: false,
    offlineReady: false,
  },
  reducers: {
    setNeedRefresh: (state, action) => {
      state.needRefresh = action.payload
    },
    setOfflineReady: (state, action) => {
      state.offlineReady = action.payload
    },
  },
})

export const { setNeedRefresh, setOfflineReady } = pwaSlice.actions
export default pwaSlice.reducer
