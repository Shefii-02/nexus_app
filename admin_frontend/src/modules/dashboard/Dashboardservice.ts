import axios from 'axios'
import type { DashboardData, DashboardStatusResponse } from './Dashboard.types'

const API_BASE = import.meta.env.VITE_API_BASE_URL ?? '/api'

export const dashboardService = {
  async getStatus(): Promise<DashboardData> {
    const { data } = await axios.get<DashboardStatusResponse>(`${API_BASE}/dashboard-status`, {
      headers: { Accept: 'application/json' },
    })
    return data.data
  },
}