# Toast Notifications, Error Pages & Network Detection Guide

## Overview
This guide covers the implementation of toast notifications, error pages, no network detection, and error boundaries for a robust user experience.

---

## 1. Toast Notifications

### Installation
```bash
npm install react-toastify
```

### Toast Service (`src/services/toastService.ts`)

**Basic Usage:**
```typescript
import { toastService } from '@/services/toastService'

// Success toast
toastService.success('Operation completed successfully!')

// Error toast (5 second auto-close)
toastService.error('Something went wrong. Please try again.')

// Info toast
toastService.info('Here is some information.')

// Warning toast
toastService.warning('Please be careful!')

// Custom options
toastService.success('Custom toast', {
  autoClose: 2000,
  position: 'bottom-left',
})
```

### useToast Hook (Recommended)

**Simple & Easy:**
```typescript
import { useToast } from '@/hooks/useToast'

export function StudentForm() {
  const toast = useToast()

  const handleSubmit = async (data) => {
    try {
      await studentService.create(data)
      toast.success('Student created successfully!')
    } catch (error) {
      toast.error(error) // Automatically extracts error message
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      {/* form fields */}
    </form>
  )
}
```

### Advanced Toast Features

**Loading Toast:**
```typescript
const toast = useToast()

const handleLongOperation = async () => {
  const toastId = toast.loading('Processing...')
  
  try {
    await someApi.call()
    toast.updateLoading(toastId, true, 'Done!')
  } catch (error) {
    toast.updateLoading(toastId, false, 'Failed!')
  }
}
```

**Dismiss Specific Toast:**
```typescript
const toastId = toastService.success('Message')
toastService.dismiss(toastId)
```

**Clear All Toasts:**
```typescript
toastService.clearAll()
```

### Toast Positions
- `top-right` (default)
- `top-left`
- `top-center`
- `bottom-right`
- `bottom-left`
- `bottom-center`

---

## 2. Error Page Component

### Usage

**Manual Error Page:**
```typescript
import ErrorPage from '@/components/ErrorPage'
import { useRouteError } from 'react-router-dom'

export function ErrorRoute() {
  const error = useRouteError() as any
  
  return (
    <ErrorPage
      statusCode={error.status || 500}
      title={error.statusText}
      message={error.data?.message}
    />
  )
}
```

**In React Router:**
```typescript
// routes/AppRoutes.tsx
import ErrorPage from '@/components/ErrorPage'
import NotFoundPage from '@/components/NotFoundPage'

const routes = [
  {
    path: '/',
    element: <Layout />,
    errorElement: <ErrorPage />,
    children: [
      // routes
    ]
  },
  {
    path: '*',
    element: <NotFoundPage />
  }
]
```

### Error Page Features
- ✅ Automatic error code detection
- ✅ Icon-based visual indicators
- ✅ Responsive design
- ✅ Back button navigation
- ✅ Home button fallback
- ✅ Timestamp logging
- ✅ Helpful error messages

### Supported Status Codes
- 400: Bad Request
- 401: Unauthorized
- 403: Access Denied
- 404: Page Not Found
- 422: Validation Error
- 429: Too Many Requests
- 500: Server Error
- 503: Service Unavailable

---

## 3. No Network Detection

### useNetworkStatus Hook

**Basic Usage:**
```typescript
import { useNetworkStatus } from '@/hooks/useNetworkStatus'

export function MyComponent() {
  const { isOnline } = useNetworkStatus()

  if (!isOnline) {
    return <div>You are offline. Please check your connection.</div>
  }

  return <div>Online content</div>
}
```

### NoNetworkPage Component

**Automatic Display:**
The `NoNetworkPage` is automatically integrated in `App.tsx` and shows automatically when:
- Network connection is lost
- Browser is offline
- No internet connectivity

**Features:**
- ✅ Auto-detection of network status
- ✅ Modal overlay presentation
- ✅ Helpful troubleshooting tips
- ✅ Retry button with page reload
- ✅ Animated offline indicator
- ✅ Professional UI design

---

## 4. Error Boundary

### Purpose
Catches React component errors and displays a user-friendly error page instead of crashing.

### Automatic Setup
The `ErrorBoundary` is already wrapped around the entire app in `App.tsx`.

### Manual Usage
```typescript
import ErrorBoundary from '@/components/ErrorBoundary'

export function App() {
  return (
    <ErrorBoundary>
      <YourComponent />
    </ErrorBoundary>
  )
}
```

### Features
- ✅ Catches unhandled React errors
- ✅ Displays error message in development
- ✅ Provides recovery options
- ✅ Error logging capability
- ✅ User-friendly error message

---

## 5. Complete Example: CRUD with Notifications

```typescript
import { useToast } from '@/hooks/useToast'
import { useStudents, useCreateStudent, useUpdateStudent, useDeleteStudent } from '@/modules/students'
import { useNetworkStatus } from '@/hooks/useNetworkStatus'

export function StudentManagement() {
  const toast = useToast()
  const { isOnline } = useNetworkStatus()
  
  const { data: students, isLoading } = useStudents()
  const createMutation = useCreateStudent()
  const updateMutation = useUpdateStudent()
  const deleteMutation = useDeleteStudent()

  if (!isOnline) {
    return <NoNetworkPage />
  }

  const handleCreate = async (formData) => {
    try {
      await createMutation.mutateAsync(formData)
      toast.success('Student created successfully!')
    } catch (error) {
      toast.error(error)
    }
  }

  const handleUpdate = async (id, formData) => {
    try {
      await updateMutation.mutateAsync({ id, payload: formData })
      toast.success('Student updated successfully!')
    } catch (error) {
      toast.error(error)
    }
  }

  const handleDelete = async (id) => {
    const confirmed = window.confirm('Are you sure?')
    if (!confirmed) return

    try {
      const toastId = toast.loading('Deleting...')
      await deleteMutation.mutateAsync(id)
      toast.updateLoading(toastId, true, 'Student deleted!')
    } catch (error) {
      toast.error(error)
    }
  }

  return (
    <div>
      {/* Your UI */}
    </div>
  )
}
```

---

## 6. Integration with API Client

### Automatic Error Display
The API client automatically shows toast errors for:
- Network failures
- API errors (handled in error interceptor if configured)
- Validation errors (extracted from response)

### Error Message Extraction
```typescript
import { getErrorMessage } from '@/services/apiClient'

try {
  await someApi.call()
} catch (error) {
  const message = getErrorMessage(error)
  // Message is automatically formatted from:
  // 1. error.response.data.message
  // 2. error.response.data.errors
  // 3. error.message
  // 4. Default fallback
}
```

---

## 7. Styling Customization

### Toast Styling
The toasts use Tailwind CSS styling. Default positions and colors are:
- Success: Green background
- Error: Red background
- Info: Blue background
- Warning: Yellow background

### Custom Toast Style
```typescript
import { ToastContainer } from 'react-toastify'

<ToastContainer
  position="top-right"
  autoClose={3000}
  hideProgressBar={false}
  newestOnTop={true}
  closeOnClick
  rtl={false}
  pauseOnFocusLoss
  draggable
  pauseOnHover
/>
```

---

## 8. Production Checklist

- [ ] Toast notifications displaying correctly
- [ ] Error pages showing for various status codes
- [ ] No network page appearing when offline
- [ ] Error boundary catching React errors
- [ ] Error messages are user-friendly (no sensitive data)
- [ ] Logging configured for production errors
- [ ] Toast positioning doesn't interfere with UI
- [ ] Mobile responsive design tested
- [ ] Error recovery flows working
- [ ] Network detection working on slow networks

---

## 9. Common Issues & Solutions

### Issue: Toasts not appearing
**Solution:** Ensure `ToastContainer` is included in App.tsx

### Issue: Toast messages repeating
**Solution:** Check for duplicate API calls or mutation triggers

### Issue: Network page stuck
**Solution:** Ensure the `isOnline` state updates correctly; test with browser DevTools offline mode

### Issue: Error boundary not catching errors
**Solution:** Error boundaries only catch render errors, not event handlers; wrap event handlers in try-catch

### Issue: No Network page appearing even when online
**Solution:** Check browser's online/offline event listeners; test with actual network disconnection

---

## 10. File Structure

```
src/
├── components/
│   ├── ErrorPage.tsx          # Generic error page
│   ├── NotFoundPage.tsx       # 404 page
│   ├── NoNetworkPage.tsx      # No internet page
│   └── ErrorBoundary.tsx      # Error boundary wrapper
├── hooks/
│   ├── useToast.ts            # Toast notification hook
│   └── useNetworkStatus.ts    # Network detection hook
├── services/
│   └── toastService.ts        # Toast service utilities
└── App.tsx                    # App with ErrorBoundary & ToastContainer
```

---

## Summary

✅ **Toast Notifications**
- Easy to use with `useToast()` hook
- Automatic error message extraction
- Support for loading states
- Customizable positions and durations

✅ **Error Pages**
- Automatic error code detection
- User-friendly messages
- Navigation options
- Professional design

✅ **Network Detection**
- Automatic no-internet display
- Auto-recovery on reconnect
- Helpful troubleshooting tips

✅ **Error Boundary**
- Catches unhandled React errors
- Recovery options
- Development error details
- User-friendly fallback

**Ready for production!** 🚀
