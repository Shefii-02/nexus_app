import { createAsyncThunk, createSlice } from '@reduxjs/toolkit'
import type { PayloadAction } from '@reduxjs/toolkit'
import { authService } from './authService'
import type { LoginCredentials, UserProfile, LoginResponse, RefreshTokenResponse } from './authService'
import { getToken, setToken, setRefreshToken, clearAllTokens } from '../../utils/storage'

export interface AuthState {
  token: string | null
  user: UserProfile | null
  status: 'idle' | 'loading' | 'succeeded' | 'failed'
  error: string | null
}

const initialState: AuthState = {
  token: getToken(),
  user: null,
  status: 'idle',
  error: null,
}

export const login = createAsyncThunk(
  'auth/login',
  async (credentials: LoginCredentials, thunkAPI) => {
    try {
      const response = await authService.login(credentials)
      return response.data
    } catch (error: any) {
      return thunkAPI.rejectWithValue(error?.response?.data?.message || 'Login failed')
    }
  },
)

export const fetchProfile = createAsyncThunk(
  'auth/fetchProfile',
  async (_, thunkAPI) => {
    try {
      const response = await authService.getProfile()
      // return response.data
      return { status: true, user: response.data.user }  // ✅ FIX HERE
    } catch (error: any) {
      return thunkAPI.rejectWithValue(error?.response?.data?.message || 'Failed to load profile')
    }
  },
)

export const refreshToken = createAsyncThunk(
  'auth/refreshToken',
  async (_, thunkAPI) => {
    try {
      const response = await authService.refresh()
      return response.data
    } catch (error: any) {
      return thunkAPI.rejectWithValue(error?.response?.data?.message || 'Token refresh failed')
    }
  },
)

const authSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    logout(state) {
      // console.log('[Auth Logout] Clearing all auth data')
      state.token = null
      state.user = null
      state.status = 'idle'
      state.error = null
      clearAllTokens()
    },
  },
  extraReducers: (builder) => {
    builder
      .addCase(login.pending, (state) => {
        state.status = 'loading'
        state.error = null
      })
      .addCase(login.fulfilled, (state, action: PayloadAction<{ access_token: string; refresh_token?: string; user?: UserProfile }>) => {
        // console.log('[Auth Login Success] Payload:', action.payload)
        state.status = 'succeeded'
        state.token = action.payload.access_token
        state.user = action.payload.user || state.user
        state.error = null
        // console.log('[Auth State Updated] Token:', !!state.token, 'User:', state.user?.id)
        if (action.payload.access_token) {
          setToken(action.payload.access_token)
        }
        if (action.payload.refresh_token) {
          setRefreshToken(action.payload.refresh_token)
        }
      })
      .addCase(login.rejected, (state, action) => {
        // console.log('[Auth Login Failed] Error:', action.payload)
        state.status = 'failed'
        state.error = typeof action.payload === 'string' ? action.payload : 'Unable to login'
      })
      .addCase(fetchProfile.pending, (state) => {
        state.status = 'loading'
        state.error = null
      })
 // .addCase(fetchProfile.fulfilled, (state, action: PayloadAction<UserProfile>) => {
      //   console.log('[Auth Profile Fetched] User:', action.payload)
      //   state.status = 'succeeded'
      //   state.user = action.payload
      //   state.error = null
      // })
      .addCase(fetchProfile.fulfilled, (state, action: PayloadAction<{ status: boolean; user: UserProfile }>) => {
        // console.log('[Auth Profile Fetched] User:', action.payload)

        state.status = 'succeeded'
        state.user = action.payload.user   // ✅ FIX HERE
        state.error = null
      })
      .addCase(fetchProfile.rejected, (state, action) => {
        state.status = 'failed'
        state.error = typeof action.payload === 'string' ? action.payload : 'Unable to fetch profile'
      })
      .addCase(refreshToken.pending, (state) => {
        state.status = 'loading'
      })
      .addCase(refreshToken.fulfilled, (state, action: PayloadAction<RefreshTokenResponse>) => {
        // console.log('[Auth Token Refreshed] Payload:', action.payload)
        state.status = 'succeeded'
        state.token = action.payload.access_token
        state.error = null
        // console.log(action.payload.user ? `[Auth User Updated After Refresh] User ID: ${action.payload.user.id}` : '[Auth Token Refreshed] No user info in response')
        if (action.payload.user) {
          state.user = action.payload.user
          // console.log('[Auth User Updated After Refresh] User:', state.user.id)
        }
        if (action.payload.access_token) {
          setToken(action.payload.access_token)
        }
      })
      .addCase(refreshToken.rejected, (state, action) => {
        state.status = 'failed'
        state.error = typeof action.payload === 'string' ? action.payload : 'Unable to refresh token'
        clearAllTokens()
      })
  },
})

export const { logout } = authSlice.actions
export default authSlice.reducer
