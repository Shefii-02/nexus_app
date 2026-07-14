import axios, { type AxiosRequestConfig } from 'axios'
import { getToken, setToken, getRefreshToken, setRefreshToken, clearAllTokens } from '../utils/storage'

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'https://api.nexus-connect.in/api',
    headers: {
        'Content-Type': 'application/json',
    },
    timeout: 30000, // 30 seconds timeout
})

let isRefreshing = false
let failedQueue: Array<{
    resolve: (token: string) => void
    reject: (err: any) => void
}> = []

const processQueue = (error: any, token: string | null = null) => {
    failedQueue.forEach((prom) => {
        if (error) {
            prom.reject(error)
        } else {
            prom.resolve(token || '')
        }
    })
    failedQueue = []
}

// Request interceptor - Add token and logging
apiClient.interceptors.request.use(
    (config) => {
        console.log('API:', config.url)
        const token = getToken()
        if (token && config.headers) {
            config.headers.Authorization = `Bearer ${token}`
        }

        // Log request in development
        if (import.meta.env.DEV) {
            // console.log(`[API Request] ${config.method?.toUpperCase()} ${config.url}`)
        }

        return config
    },
    (error) => {
        console.error('[API Request Error]', error)
        return Promise.reject(error)
    },
)

// Response interceptor - Handle token refresh, errors, and logging
apiClient.interceptors.response.use(
    (response) => {
        // Log successful response in development
        if (import.meta.env.DEV) {
            // console.log(`[API Response] ${response.status} ${response.config.url}`)
        }
        return response
    },

    async (error) => {
        const status = error?.response?.status
        const originalRequest = error.config as AxiosRequestConfig & { _retry?: boolean }

        // Handle 401 Unauthorized with token refresh
        if (status === 401 && !originalRequest._retry) {
            if (isRefreshing) {
                try {
                    const token = await new Promise((resolve, reject) => {
                        failedQueue.push({ resolve, reject })
                    })
                    originalRequest.headers = originalRequest.headers || {}
                    originalRequest.headers.Authorization = `Bearer ${token}`
                    return await apiClient(originalRequest)
                } catch (err) {
                    return await Promise.reject(err)
                }
            }

            isRefreshing = true
            originalRequest._retry = true

            const refreshToken = getRefreshToken()

            if (!refreshToken) {
                clearAllTokens()
                window.dispatchEvent(new Event('unauthorized'))
                window.location.href = '/login'
                return Promise.reject(error)
            }

            try {
                try {
                    const { data } = await apiClient
                        .post('/auth/refresh')
                    const newAccessToken = data.access_token
                    setToken(newAccessToken)
                    if (data.refresh_token) {
                        setRefreshToken(data.refresh_token)
                    }
                    apiClient.defaults.headers.common.Authorization = `Bearer ${newAccessToken}`
                    originalRequest.headers = originalRequest.headers || {}
                    originalRequest.headers.Authorization = `Bearer ${newAccessToken}`
                    processQueue(null, newAccessToken)
                    return await apiClient(originalRequest)
                } catch (err_1) {
                    processQueue(err_1, null)
                    clearAllTokens()
                    window.dispatchEvent(new Event('unauthorized'))
                    window.location.href = '/login'
                    console.error('[Token Refresh Failed]', err_1)
                    return await Promise.reject(err_1)
                }
            } finally {
                isRefreshing = false
            }
        }

        // Handle 401 without retry
        if (status === 401) {
            clearAllTokens()
            window.dispatchEvent(new Event('unauthorized'))
            window.location.href = '/login'
        }

        // Log error in development
        if (import.meta.env.DEV) {
            console.error(`[API Error] ${status} ${error?.config?.url}`, error?.response?.data)
        }

        return Promise.reject(error)
    },
)

/**
 * Helper function to handle API errors consistently
 * @param error - The error object
 * @returns Formatted error message
 */
export const getErrorMessage = (error: any): string => {
    if (error?.response?.data?.message) {
        return error.response.data.message
    }
    if (error?.response?.data?.errors) {
        const errors = error.response.data.errors
        if (typeof errors === 'object') {
            return Object.values(errors).flat().join(', ') as string
        }
    }
    if (error?.message) {
        return error.message
    }
    return 'An error occurred. Please try again.'
}

export default apiClient
