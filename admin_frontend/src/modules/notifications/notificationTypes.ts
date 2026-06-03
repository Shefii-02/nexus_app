export interface Notification {
    id: number

    user_id: number | null

    type: string

    title: string

    message: string

    related_model?: string

    related_id?: number

    read_at?: string | null

    priority: string

    created_at: string
}