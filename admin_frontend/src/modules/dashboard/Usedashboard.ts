import { useCallback, useEffect, useState } from 'react'
import { dashboardService } from './Dashboardservice'
import type { DashboardData } from './Dashboard.types'

export const useDashboard = () => {
  const [data, setData] = useState<DashboardData | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const fetchDashboard = useCallback(async () => {
    setIsLoading(true)
    setError(null)
    try {
      const result = await dashboardService.getStatus()
      setData(result)
    } catch {
      setError('Could not load dashboard data. Try refreshing.')
    } finally {
      setIsLoading(false)
    }
  }, [])

  useEffect(() => {
    fetchDashboard()
  }, [fetchDashboard])

  return { data, isLoading, error, refetch: fetchDashboard }
}