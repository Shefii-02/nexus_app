export interface StatMetric {
  value: number
  formatted?: string
  growth: string
  trend: number[]
}

export interface DashboardStats {
  total_courses: StatMetric
  total_students: StatMetric
  enrollments: StatMetric
  revenue: StatMetric
}

export interface ChartSeries {
  range: string
  labels: string[]
  values: number[]
}

export interface TopCourse {
  id: number
  name: string
  price: string
  sales_count: number
}

export interface NotificationItem {
  id: number
  message: string
  type: string
  created_at: string
}

export interface DashboardData {
  stats: DashboardStats
  enrollments_chart: ChartSeries
  revenue_chart: ChartSeries
  top_courses: TopCourse[]
  notifications: NotificationItem[]
}

export interface DashboardStatusResponse {
  success: boolean
  data: DashboardData
}