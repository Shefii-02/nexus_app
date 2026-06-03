const TOKEN_KEY = 'nexus_admin_jwt_token'
const REFRESH_TOKEN_KEY = 'nexus_admin_refresh_token'

export const setToken = (token: string) => {
  localStorage.setItem(TOKEN_KEY, token)
}

export const getToken = () => localStorage.getItem(TOKEN_KEY)

export const clearToken = () => {
  localStorage.removeItem(TOKEN_KEY)
}

export const setRefreshToken = (token: string) => {
  localStorage.setItem(REFRESH_TOKEN_KEY, token)
}

export const getRefreshToken = () => localStorage.getItem(REFRESH_TOKEN_KEY)

export const clearRefreshToken = () => {
  localStorage.removeItem(REFRESH_TOKEN_KEY)
}

export const clearAllTokens = () => {
  clearToken()
  clearRefreshToken()
}
