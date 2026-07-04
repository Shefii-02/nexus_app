
import type { DashboardData, DashboardStatusResponse } from './Dashboard.types'
import apiClient from "../../services/apiClient";


export const dashboardService = {
  async getStatus(): Promise<DashboardData> {
    const { data } = await apiClient.get<DashboardStatusResponse>(`dashboard-status`, {
      headers: { Accept: 'application/json' },
    })
    return data.data
  },
}