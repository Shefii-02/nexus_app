# Quick Start Implementation Guide

## Step 1: Register Services in AppServiceProvider

Copy the content from `SERVICE_PROVIDER_BINDINGS.php` into `app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Import all interfaces and implementations
use App\Repositories\Teacher\TeacherRepositoryInterface;
use App\Repositories\Teacher\TeacherRepository;
use App\Services\Teacher\TeacherService;
// ... (import all others)

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Teacher
        $this->app->bind(TeacherRepositoryInterface::class, TeacherRepository::class);
        $this->app->singleton(TeacherService::class, function ($app) {
            return new TeacherService($app->make(TeacherRepositoryInterface::class));
        });
        
        // Repeat for all other services...
    }
}
```

---

## Step 2: Run Migrations

```bash
php artisan migrate
```

This will create all necessary tables:
- teachers
- students
- staff
- courses (updated)
- course_classes
- payments (updated)
- announcements & related tables (updated)
- notifications

---

## Step 3: Create Service Provider (Alternative - Manual Registration)

If you prefer automatic registration, create a new service provider:

```bash
php artisan make:provider ModuleServiceProvider
```

Then register it in `config/app.php`:

```php
'providers' => [
    // ... existing providers
    App\Providers\ModuleServiceProvider::class,
],
```

---

## Step 4: Test API Endpoints

### Using Postman

1. **Login to get JWT token:**
```
POST /api/auth/login
{
  "email": "admin@example.com",
  "password": "password"
}
```

2. **Add token to subsequent requests:**
   - Header: `Authorization: Bearer {token}`

3. **Test Teacher CRUD:**
```
GET /api/teachers?page=1&per_page=15
POST /api/teachers
GET /api/teachers/1
PUT /api/teachers/1
DELETE /api/teachers/1
```

### Common Patterns

#### List with Filters
```
GET /api/teachers?page=1&per_page=15&filters[status]=active&filters[search]=Math
```

#### Get Single Resource with Relations
```
GET /api/students/1
```

#### Create Resource
```
POST /api/students
{
  "user_id": 2,
  "roll_number": "STU001",
  "batch_id": 1,
  "phone": "9876543210",
  "address": "Address",
  "guardian_name": "Parent",
  "guardian_phone": "9876543211"
}
```

---

## Step 5: Common Operations

### Creating a New Module

1. **Create Model** (with relationships):
```php
<?php
namespace App\Models;

class MyModel extends Model {
    protected $fillable = ['field1', 'field2'];
    
    public function relation() {
        return $this->belongsTo(OtherModel::class);
    }
}
```

2. **Create Migration**:
```bash
php artisan make:migration create_my_models_table
```

3. **Create DTO**:
```php
<?php
namespace App\DTOs;

class MyModelDTO {
    public function __construct(
        public string $field1,
        public string $field2,
    ) {}
    
    public static function fromArray(array $data): self {
        return new self(
            field1: $data['field1'],
            field2: $data['field2'],
        );
    }
    
    public function toArray(): array {
        return [
            'field1' => $this->field1,
            'field2' => $this->field2,
        ];
    }
}
```

4. **Create Repository Interface**:
```php
<?php
namespace App\Repositories\MyModel;

use App\Repositories\BaseRepositoryInterface;

interface MyModelRepositoryInterface extends BaseRepositoryInterface {
    public function customMethod();
}
```

5. **Create Repository Implementation**:
```php
<?php
namespace App\Repositories\MyModel;

use App\Models\MyModel;
use App\Repositories\BaseRepository;

class MyModelRepository extends BaseRepository implements MyModelRepositoryInterface {
    public function __construct(MyModel $myModel) {
        parent::__construct($myModel);
    }
    
    public function customMethod() {
        return $this->model->where(...)->get();
    }
}
```

6. **Create Service**:
```php
<?php
namespace App\Services\MyModel;

use App\DTOs\MyModelDTO;
use App\Repositories\MyModel\MyModelRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class MyModelService extends BaseService {
    protected MyModelRepositoryInterface $repository;
    
    public function __construct(MyModelRepositoryInterface $repository) {
        parent::__construct($repository);
    }
    
    public function create(MyModelDTO $dto): object {
        return DB::transaction(function () use ($dto) {
            return $this->repository->create($dto->toArray());
        });
    }
}
```

7. **Create Validation Requests**:
```bash
php artisan make:request StoreMyModelRequest
php artisan make:request UpdateMyModelRequest
```

8. **Create Controller**:
```bash
php artisan make:controller API/Admin/MyModelController --api
```

9. **Add Routes**:
```php
Route::apiResource('my-models', MyModelController::class);
```

---

## Step 6: Using Services in Controllers

```php
<?php
namespace App\Http\Controllers\API\Admin;

use App\DTOs\TeacherDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Requests\StoreTeacherRequest;
use App\Services\Teacher\TeacherService;
use Illuminate\Http\JsonResponse;

class TeacherController extends Controller {
    use ApiResponse;
    
    public function __construct(private TeacherService $teacherService) {}
    
    public function index(): JsonResponse {
        $page = request()->query('page', 1);
        $perPage = request()->query('per_page', 15);
        $filters = request()->query('filters', []);
        
        $teachers = $this->teacherService->list($page, $perPage, $filters);
        
        return $this->paginatedResponse($teachers, 'Teachers retrieved');
    }
    
    public function store(StoreTeacherRequest $request): JsonResponse {
        try {
            $dto = TeacherDTO::fromArray($request->validated());
            $teacher = $this->teacherService->create($dto);
            
            return $this->successResponse(
                $teacher->load('user'),
                'Teacher created',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed', ['error' => $e->getMessage()], 500);
        }
    }
}
```

---

## Step 7: Transaction Handling

All write operations should be wrapped in transactions:

```php
use Illuminate\Support\Facades\DB;

public function complexOperation() {
    return DB::transaction(function () {
        // Multiple database operations
        $user = User::create([...]);
        $teacher = Teacher::create([...]);
        $course = Course::create([...]);
        
        return compact('user', 'teacher', 'course');
    });
}
```

---

## Step 8: Eager Loading (Prevent N+1)

Always eager load relationships:

```php
// Bad - N+1 query problem
$teachers = Teacher::all();
foreach ($teachers as $teacher) {
    echo $teacher->user->name; // Additional query per teacher
}

// Good - Eager load
$teachers = Teacher::with('user')->get();
foreach ($teachers as $teacher) {
    echo $teacher->user->name; // No additional queries
}

// Use in repository
public function findWithCourses(int $id) {
    return $this->model->with(['user', 'courses'])->find($id);
}
```

---

## Step 9: Filtering Implementation

In your repository:

```php
protected function applyFilters($query, array $filters) {
    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }
    
    if (!empty($filters['search'])) {
        $search = $filters['search'];
        $query->where('name', 'like', "%{$search}%");
    }
    
    return $query;
}
```

Usage in controller:
```php
$page = request()->query('page', 1);
$filters = request()->query('filters', []);
$results = $this->service->list($page, 15, $filters);
```

---

## Step 10: Testing

### Unit Test Example

```php
<?php
namespace Tests\Unit;

use App\Services\Teacher\TeacherService;
use App\Repositories\Teacher\TeacherRepositoryInterface;
use Tests\TestCase;

class TeacherServiceTest extends TestCase {
    private $teacherService;
    private $teacherRepository;
    
    protected function setUp(): void {
        parent::setUp();
        $this->teacherRepository = $this->createMock(TeacherRepositoryInterface::class);
        $this->teacherService = new TeacherService($this->teacherRepository);
    }
    
    public function test_can_get_active_teachers() {
        $this->teacherRepository->expects($this->once())
            ->method('getActiveTeachers')
            ->willReturn(collect([]));
        
        $result = $this->teacherService->getActive();
        
        $this->assertNotNull($result);
    }
}
```

---

## Common Issues & Solutions

### Issue 1: "Target class does not exist"
**Solution:** Check service provider bindings are registered in AppServiceProvider

### Issue 2: "Call to undefined method"
**Solution:** Ensure you're implementing the interface correctly

### Issue 3: "Relationship not found"
**Solution:** Check model relationships are defined correctly

### Issue 4: "N+1 query detected"
**Solution:** Use eager loading with `with()` in repositories

### Issue 5: "Validation error"
**Solution:** Check Form Request rules match your DTO fields

---

## Database Seeders

Create seeders for testing:

```bash
php artisan make:seeder TeacherSeeder
```

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\User;

class TeacherSeeder extends Seeder {
    public function run(): void {
        $users = User::factory(10)->create();
        
        foreach ($users as $user) {
            Teacher::create([
                'user_id' => $user->id,
                'qualification' => 'M.Sc',
                'subject' => 'Mathematics',
                'experience_years' => rand(1, 10),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
            ]);
        }
    }
}
```

Run: `php artisan db:seed --class=TeacherSeeder`

---

## Performance Optimization Tips

1. **Use pagination** - Always paginate large datasets
2. **Eager load relationships** - Use `with()` to prevent N+1 queries
3. **Add database indexes** - Indexes on frequently filtered columns
4. **Cache results** - Use Redis for frequently accessed data
5. **Query optimization** - Use `select()` to fetch only needed columns
6. **Soft deletes** - Use soft deletes instead of hard deletes

---

## Security Best Practices

1. ✅ Validate all input using Form Requests
2. ✅ Use authorization middleware for protected routes
3. ✅ Hash passwords using bcrypt
4. ✅ Use HTTPS for all API calls
5. ✅ Implement rate limiting
6. ✅ Sanitize all user inputs
7. ✅ Use JWT tokens for authentication
8. ✅ Implement CORS properly

---

## Deployment Checklist

- [ ] All migrations run successfully
- [ ] Service provider bindings registered
- [ ] JWT secret key generated
- [ ] Environment variables set
- [ ] Database backed up
- [ ] Tests passing
- [ ] API documentation updated
- [ ] CORS configured correctly
- [ ] Rate limiting implemented
- [ ] Error logging configured

---

## Useful Commands

```bash
# Generate service classes
php artisan make:model MyModel -m

# Generate controller
php artisan make:controller API/Admin/MyModelController --api

# Generate request class
php artisan make:request StoreMyModelRequest

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Refresh migrations
php artisan migrate:refresh --seed

# Run tests
php artisan test

# Clear cache
php artisan cache:clear

# Generate API documentation
php artisan serve
```

---

## Support & Resources

- **API Documentation:** `API_DOCUMENTATION.md`
- **Module Structure:** `MODULE_STRUCTURE_SUMMARY.md`
- **Laravel Docs:** https://laravel.com/docs
- **SOLID Principles:** https://en.wikipedia.org/wiki/SOLID

