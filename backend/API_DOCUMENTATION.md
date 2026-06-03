# NexusBackend - Production-Ready Laravel API Documentation

## Architecture Overview

This backend implements a **Clean Architecture** pattern with strict separation of concerns:

```
Request → Validation → Controller → DTO → Service → Repository → Database
Response ← Controller ← Service ← Repository
```

### Architecture Layers

1. **Controller Layer** - HTTP request handling, response formatting
2. **Service Layer** - Business logic, transactions, workflow orchestration
3. **DTO Layer** - Data Transfer Objects for type-safe data passing
4. **Repository Layer** - Data access abstraction, query building
5. **Model Layer** - Eloquent models with relationships

---

## Project Structure

```
app/
├── DTOs/                           # Data Transfer Objects
│   ├── TeacherDTO.php
│   ├── StudentDTO.php
│   ├── StaffDTO.php
│   ├── CourseDTO.php
│   ├── CourseClassDTO.php
│   ├── PaymentDTO.php
│   ├── AnnouncementDTO.php
│   └── NotificationDTO.php
│
├── Http/
│   ├── Controllers/API/
│   │   ├── NotificationController.php
│   │   └── Admin/
│   │       ├── TeacherController.php
│   │       ├── StudentController.php
│   │       ├── StaffController.php
│   │       ├── CourseController.php
│   │       ├── CourseClassController.php
│   │       ├── PaymentController.php
│   │       └── AnnouncementController.php
│   │
│   └── Requests/
│       ├── Store*Request.php        # Validation for create
│       └── Update*Request.php       # Validation for update
│
├── Models/
│   ├── Teacher.php
│   ├── Student.php
│   ├── Staff.php
│   ├── Course.php
│   ├── CourseClass.php
│   ├── Payment.php
│   ├── Announcement.php
│   └── Notification.php
│
├── Repositories/
│   ├── BaseRepository.php           # Abstract base repository
│   ├── BaseRepositoryInterface.php  # Base interface
│   ├── Teacher/
│   ├── Student/
│   ├── Staff/
│   ├── Course/
│   ├── CourseClass/
│   ├── Payment/
│   ├── Announcement/
│   └── Notification/
│
└── Services/
    ├── BaseService.php              # Abstract base service
    ├── Teacher/
    ├── Student/
    ├── Staff/
    ├── Course/
    ├── CourseClass/
    ├── Payment/
    ├── Announcement/
    └── Notification/
```

---

## API Response Format

All API responses follow this standardized format:

```json
{
  "status": true,
  "message": "Success message",
  "data": { /* response data */ }
}
```

### Paginated Response

```json
{
  "status": true,
  "message": "Success message",
  "data": [ /* items array */ ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

### Error Response

```json
{
  "status": false,
  "message": "Error message",
  "data": null
}
```

---

## Modules & API Endpoints

### 1. TEACHER MANAGEMENT

**Base URL:** `/api/teachers`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/` | List all teachers (paginated) | Admin |
| POST | `/` | Create new teacher | Admin |
| GET | `/{id}` | Get single teacher | Admin |
| PUT | `/{id}` | Update teacher | Admin |
| DELETE | `/{id}` | Delete teacher | Admin |

**Create Teacher Request:**
```json
{
  "user_id": 1,
  "qualification": "M.Sc Computer Science",
  "subject": "Mathematics",
  "experience_years": 5,
  "phone": "9876543210",
  "address": "123 Main St"
}
```

---

### 2. STUDENT MANAGEMENT

**Base URL:** `/api/students`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/` | List all students (paginated) | Admin |
| POST | `/` | Create new student | Admin |
| GET | `/{id}` | Get single student with courses | Admin |
| PUT | `/{id}` | Update student | Admin |
| DELETE | `/{id}` | Delete student | Admin |

**Create Student Request:**
```json
{
  "user_id": 2,
  "roll_number": "STU001",
  "batch_id": 1,
  "phone": "9876543211",
  "address": "456 Oak Ave",
  "guardian_name": "John Doe",
  "guardian_phone": "9876543212"
}
```

---

### 3. STAFF MANAGEMENT

**Base URL:** `/api/staff`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/` | List all staff (paginated) | Admin |
| POST | `/` | Create new staff | Admin |
| GET | `/{id}` | Get single staff member | Admin |
| PUT | `/{id}` | Update staff | Admin |
| DELETE | `/{id}` | Delete staff | Admin |

**Create Staff Request:**
```json
{
  "user_id": 3,
  "department": "Administration",
  "designation": "Registrar",
  "phone": "9876543213",
  "address": "789 Pine Rd"
}
```

---

### 4. COURSE MANAGEMENT

**Base URL:** `/api/courses`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/` | List all courses (paginated) | Admin |
| POST | `/` | Create new course | Admin |
| GET | `/{id}` | Get course with students | Admin |
| PUT | `/{id}` | Update course | Admin |
| DELETE | `/{id}` | Delete course (soft) | Admin |
| POST | `/{id}/attach-student/{studentId}` | Add student to course | Admin |
| DELETE | `/{id}/detach-student/{studentId}` | Remove student from course | Admin |

**Create Course Request:**
```json
{
  "code": "CS101",
  "name": "Introduction to Programming",
  "description": "Learn the basics of programming",
  "teacher_id": 1,
  "batch_id": 1,
  "fee_type": "one_time",
  "fee_amount": 5000,
  "duration_months": 3
}
```

**Fee Types:** `monthly` or `one_time`

---

### 5. COURSE CLASSES & MATERIALS

**Base URL:** `/api/course-classes`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/` | List all classes (paginated) | Admin |
| POST | `/` | Create new class | Admin |
| GET | `/{id}` | Get class with materials | Admin |
| PUT | `/{id}` | Update class | Admin |
| DELETE | `/{id}` | Delete class | Admin |
| GET | `/upcoming` | Get upcoming classes | Admin |
| GET | `/by-date-range` | Get classes in date range | Admin |

**Create Course Class Request:**
```json
{
  "course_id": 1,
  "teacher_id": 1,
  "title": "Class 1 - Introduction",
  "description": "Class description",
  "class_number": 1,
  "scheduled_date": "2026-05-25 10:00:00",
  "duration_minutes": 60,
  "room_location": "Room 101"
}
```

---

### 6. COURSE PAYMENTS (MANUAL ENTRY ONLY)

**Base URL:** `/api/payments`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/` | List all payments (paginated) | Admin |
| POST | `/` | Record new payment (manual) | Admin |
| GET | `/{id}` | Get payment details | Admin |
| PUT | `/{id}` | Update payment | Admin |
| DELETE | `/{id}` | Delete payment | Admin |
| POST | `/{id}/verify` | Verify payment | Admin |
| POST | `/{id}/reject` | Reject payment | Admin |
| GET | `/student/{studentId}` | Get student payments | Admin |

**Record Payment Request:**
```json
{
  "student_id": 1,
  "course_id": 1,
  "amount": 5000,
  "payment_date": "2026-05-20",
  "payment_method": "cash",
  "reference_number": "REC001",
  "notes": "Payment received in cash"
}
```

**Payment Methods:** `cash`, `check`, `bank_transfer`, `other`

**Payment Status:** `pending`, `verified`, `rejected`

---

### 7. ANNOUNCEMENTS (ROLE/USER/BATCH BASED)

**Base URL:** `/api/announcements`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/` | List announcements (paginated) | Admin |
| POST | `/` | Create announcement | Admin |
| GET | `/{id}` | Get announcement | Admin |
| PUT | `/{id}` | Update announcement | Admin |
| DELETE | `/{id}` | Delete announcement | Admin |
| POST | `/{id}/publish` | Publish announcement | Admin |
| POST | `/{id}/archive` | Archive announcement | Admin |
| GET | `/published` | Get all published | Admin |
| GET | `/active` | Get announcements for user | Any Auth |

**Create Announcement Request:**
```json
{
  "title": "Important Notice",
  "content": "Announcement content here",
  "target_type": "specific",
  "start_date": "2026-05-20 00:00:00",
  "end_date": "2026-05-30 23:59:59",
  "priority": "high",
  "status": "published",
  "user_ids": [1, 2, 3],
  "role_ids": [1],
  "batch_ids": [1]
}
```

**Target Types:** `all`, `users`, `roles`, `batches`, `specific`

**Priority Levels:** `low`, `medium`, `high`

---

### 8. NOTIFICATIONS

**Base URL:** `/api/notifications`

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/` | Get user notifications (paginated) | Any Auth |
| GET | `/unread` | Get unread notifications | Any Auth |
| GET | `/unread-count` | Get unread count | Any Auth |
| POST | `/{id}/mark-as-read` | Mark notification as read | Any Auth |
| POST | `/mark-all-as-read` | Mark all as read | Any Auth |
| DELETE | `/{id}` | Delete notification | Any Auth |

**Notification Response:**
```json
{
  "id": 1,
  "user_id": 1,
  "type": "announcement",
  "title": "New Announcement",
  "message": "Announcement text",
  "related_model": "Announcement",
  "related_id": 5,
  "priority": "high",
  "read_at": null,
  "created_at": "2026-05-20 10:00:00"
}
```

---

## Key Design Patterns

### 1. Repository Pattern
- **Purpose:** Abstraction of data access logic
- **Location:** `/app/Repositories/`
- **Benefits:** Easy testing, swappable data sources

### 2. Service Layer Pattern
- **Purpose:** Encapsulate business logic
- **Location:** `/app/Services/`
- **Benefits:** Reusable logic, transaction handling

### 3. DTO (Data Transfer Object)
- **Purpose:** Type-safe data passing between layers
- **Location:** `/app/DTOs/`
- **Benefits:** Clear contracts, validation, immutability

### 4. Base Classes for DRY Code
- **BaseRepository:** Common CRUD operations
- **BaseService:** Common service methods
- **ApiResponse:** Consistent response formatting

---

## Query Filtering

All list endpoints support filtering via query parameters:

```
GET /api/teachers?page=1&per_page=15&filters[status]=active&filters[subject]=Mathematics
```

### Available Filters by Module

**Teacher:**
- `status` - active, inactive, suspended
- `subject` - subject name
- `search` - search by name or email

**Student:**
- `status` - active, inactive, graduated, suspended
- `batch_id` - batch ID
- `search` - search by roll number or name

**Course:**
- `status` - active, inactive, archived
- `teacher_id` - teacher ID
- `batch_id` - batch ID
- `fee_type` - monthly, one_time
- `search` - search by code or name

**Payment:**
- `student_id` - student ID
- `course_id` - course ID
- `status` - pending, verified, rejected
- `payment_method` - cash, check, bank_transfer, other
- `start_date` & `end_date` - date range filtering

---

## Authentication

All protected endpoints require JWT token in Authorization header:

```
Authorization: Bearer {token}
```

Obtain token by logging in:

```
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "password"
}
```

---

## Transaction Safety

All write operations (create, update, delete) are wrapped in database transactions:
- Ensures data consistency
- Automatic rollback on errors
- ACID compliance

---

## Pagination

Default pagination: 15 items per page

Query parameters:
- `page` - page number (default: 1)
- `per_page` - items per page (default: 15)

---

## Error Handling

All errors return appropriate HTTP status codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request (validation errors)
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Internal Server Error

---

## Best Practices Used

1. ✅ SOLID Principles
2. ✅ DRY (Don't Repeat Yourself)
3. ✅ RESTful API Design
4. ✅ Soft Deletes for data safety
5. ✅ Eager Loading to prevent N+1 queries
6. ✅ Database transactions for consistency
7. ✅ Validation at request level
8. ✅ Clear separation of concerns
9. ✅ Type-safe DTOs
10. ✅ Comprehensive error handling

---

## Service Provider Registration

Register services in `app/Providers/AppServiceProvider.php`:

```php
// Teacher
$this->app->bind(\App\Repositories\Teacher\TeacherRepositoryInterface::class, 
                 \App\Repositories\Teacher\TeacherRepository::class);
$this->app->singleton(\App\Services\Teacher\TeacherService::class);

// Similar for other services...
```

---

## Running Migrations

```bash
php artisan migrate
```

---

## Notes

- All timestamps use UTC
- Soft deletes are enabled on applicable models
- Relationships are eager-loaded to prevent N+1 queries
- Services handle all business logic
- Controllers remain thin and focused on HTTP concerns
- Validation is centralized in Form Request classes

