/**
 * Route Error Handling Example
 * Shows how to integrate error pages with React Router
 */

import { useRouteError } from 'react-router-dom'
import ErrorPage from '@/components/ErrorPage'
import NotFoundPage from '@/components/NotFoundPage'

/**
 * Root error element - catches all routing errors
 */
export function RootErrorElement() {
  const error = useRouteError() as any

  // Log error in development
  if (import.meta.env.DEV) {
    console.error('Route error:', error)
  }

  // Handle specific error types
  if (error?.status === 404) {
    return <NotFoundPage />
  }

  return (
    <ErrorPage
      statusCode={error?.status || 500}
      title={error?.statusText || 'Error'}
      message={error?.data?.message || 'An unexpected error occurred'}
    />
  )
}

/**
 * Example route configuration
 * Add this to your AppRoutes.tsx
 */
export const exampleRouteConfig = [
  {
    path: '/',
    element: <div>Home</div>,
    errorElement: <RootErrorElement />,
    children: [
      {
        path: 'dashboard',
        element: <div>Dashboard</div>,
      },
      {
        path: 'students',
        element: <div>Students</div>,
        errorElement: <RootErrorElement />,
      },
      {
        path: 'courses',
        element: <div>Courses</div>,
        errorElement: <RootErrorElement />,
      },
      // More routes...
    ],
  },
  {
    path: '*',
    element: <NotFoundPage />,
  },
]

/**
 * Throwing custom errors in route loaders/actions
 */
export function exampleThrowError() {
  // Example: Throw 404 when data not found
  throw new Response('Not Found', {
    status: 404,
    statusText: 'Student not found',
  })
}

// Example: Throw 403 when unauthorized
export function exampleThrowUnauthorized() {
  throw new Response('Forbidden', {
    status: 403,
    statusText: 'You do not have permission',
  })
}

// Example: Throw 500 server error
export function exampleThrowServerError() {
  throw new Response('Internal Server Error', {
    status: 500,
    statusText: 'Something went wrong',
  })
}

/**
 * Usage in a route loader
 */
export function exampleStudentLoader({ params }: { params: { id: string } }) {
  const studentId = params.id

  // Simulate data fetching
  const student = null // Imagine fetching from API

  if (!student) {
    throw new Response('Not Found', {
      status: 404,
      statusText: `Student ${studentId} not found`,
    })
  }

  return student
}

/**
 * Usage in a route action
 */
export async function exampleStudentAction({ request, params }: any) {
  if (request.method === 'DELETE') {
    try {
      // Simulate deletion
      // const response = await fetch(`/api/students/${params.id}`, { method: 'DELETE' })
      // if (!response.ok) throw new Error('Failed to delete')
    } catch (error) {
      throw new Response('Failed to delete student', {
        status: 500,
        statusText: 'Server error',
      })
    }
  }

  return null
}
