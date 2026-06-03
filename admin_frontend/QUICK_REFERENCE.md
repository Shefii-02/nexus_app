# Quick Reference - Toast, Error Pages & Network Detection

## 🍞 Toast Notifications

### Quick Start
```typescript
import { useToast } from '@/hooks/useToast'

const toast = useToast()

// Success
toast.success('Done!')

// Error
toast.error(error)

// Info
toast.info('Note this')

// Warning
toast.warning('Be careful')

// Loading
const toastId = toast.loading('Processing...')
toast.updateLoading(toastId, true, 'Success!')
```

### Toast Service (Direct)
```typescript
import { toastService } from '@/services/toastService'

toastService.success('Message')
toastService.error('Error')
toastService.info('Info')
toastService.warning('Warning')
toastService.loading('Loading...')
```

---

## 🔴 Error Pages

### 404 Not Found
```typescript
import NotFoundPage from '@/components/NotFoundPage'
<NotFoundPage />
```

### Generic Error Page
```typescript
import ErrorPage from '@/components/ErrorPage'

<ErrorPage
  statusCode={500}
  title="Server Error"
  message="Something went wrong"
/>
```

### Supported Codes
- 400: Bad Request
- 401: Unauthorized
- 403: Access Denied
- 404: Not Found
- 422: Validation Error
- 429: Too Many Requests
- 500: Server Error
- 503: Service Unavailable

---

## 📡 Network Detection

### Check Online Status
```typescript
import { useNetworkStatus } from '@/hooks/useNetworkStatus'

const { isOnline } = useNetworkStatus()

if (!isOnline) {
  return <NoNetworkPage />
}
```

### Show Network Indicator
```typescript
const { isOnline } = useNetworkStatus()

<span>{isOnline ? '✓ Online' : '✗ Offline'}</span>
```

---

## 🚫 No Network Page
```typescript
import NoNetworkPage from '@/components/NoNetworkPage'

// Already integrated in App.tsx - shows automatically
```

---

## 🔒 Error Boundary
```typescript
import ErrorBoundary from '@/components/ErrorBoundary'

<ErrorBoundary>
  <YourComponent />
</ErrorBoundary>

// Already wrapped in App.tsx
```

---

## 📋 Form with Notifications (Complete Example)

```typescript
import { useToast } from '@/hooks/useToast'
import { useCreateStudent } from '@/modules/students'

export function CreateStudentForm() {
  const toast = useToast()
  const createMutation = useCreateStudent()

  const handleSubmit = async (e) => {
    e.preventDefault()
    const formData = new FormData(e.currentTarget)

    try {
      const toastId = toast.loading('Creating student...')
      
      await createMutation.mutateAsync({
        name: formData.get('name'),
        email: formData.get('email'),
      })

      toast.updateLoading(toastId, true, 'Student created!')
      e.currentTarget.reset()
    } catch (error) {
      toast.error(error)
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      <input name="name" required />
      <input name="email" type="email" required />
      <button type="submit" disabled={createMutation.isPending}>
        Create
      </button>
    </form>
  )
}
```

---

## 🗑️ Delete with Confirmation

```typescript
import { useToast } from '@/hooks/useToast'
import { useDeleteStudent } from '@/modules/students'

export function DeleteButton({ studentId, name }) {
  const toast = useToast()
  const deleteMutation = useDeleteStudent()

  const handleDelete = async () => {
    if (!window.confirm(`Delete ${name}?`)) return

    try {
      const toastId = toast.loading('Deleting...')
      await deleteMutation.mutateAsync(studentId)
      toast.updateLoading(toastId, true, 'Deleted!')
    } catch (error) {
      toast.error(error)
    }
  }

  return (
    <button onClick={handleDelete} disabled={deleteMutation.isPending}>
      Delete
    </button>
  )
}
```

---

## 🌐 Offline Content

```typescript
import { useNetworkStatus } from '@/hooks/useNetworkStatus'
import NoNetworkPage from '@/components/NoNetworkPage'

export function StudentList() {
  const { isOnline } = useNetworkStatus()

  if (!isOnline) {
    return <NoNetworkPage />
  }

  // Rest of component...
}
```

---

## 📊 Batch Operations

```typescript
const handleBatchDelete = async (ids) => {
  const toastId = toast.loading(`Deleting ${ids.length} items...`)
  let success = 0, fail = 0

  for (const id of ids) {
    try {
      await deleteMutation.mutateAsync(id)
      success++
    } catch {
      fail++
    }
  }

  if (fail === 0) {
    toast.updateLoading(toastId, true, `Deleted ${success} items!`)
  } else {
    toast.updateLoading(toastId, false, `${success} deleted, ${fail} failed`)
  }
}
```

---

## 🎨 Toast Options

```typescript
toast.success('Message', {
  position: 'top-right',      // or 'bottom-left', etc.
  autoClose: 3000,             // milliseconds
  hideProgressBar: false,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
})
```

---

## ✅ Integration Checklist

- ✅ Toast library installed (`npm install react-toastify`)
- ✅ Toast service created
- ✅ useToast hook available
- ✅ Error pages created (404, generic)
- ✅ No network page integrated
- ✅ Error boundary wrapped app
- ✅ Network status hook working
- ✅ ToastContainer in App.tsx
- ✅ react-toastify CSS imported

---

## 📁 File Locations

```
src/
├── services/
│   └── toastService.ts
├── hooks/
│   ├── useToast.ts
│   └── useNetworkStatus.ts
├── components/
│   ├── ErrorPage.tsx
│   ├── NotFoundPage.tsx
│   ├── NoNetworkPage.tsx
│   ├── ErrorBoundary.tsx
│   └── EXAMPLES.tsx
└── App.tsx (includes Toast & Error setup)
```

---

## 🚀 Ready to Use!

All features are configured and ready. Start using:
- `useToast()` for notifications
- `useNetworkStatus()` for online detection
- Error pages for error handling
- Error boundaries for React errors

See `EXAMPLES.tsx` for complete code examples!
