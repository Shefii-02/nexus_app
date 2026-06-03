<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard',

            'teacher',
            'student',
            'staff',
            'role',

            'course',
            'class_link',
            'class_material',

            'group',
            'payment',

            'announcement',
            'chat',
            'notification',
        ];

        $actions = ['view', 'create', 'update', 'delete'];

        $permissions = [];

        // 🔹 Generate CRUD permissions
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[] = $module . '.' . $action;
            }
        }

        // 🔹 Special permissions
        $permissions = array_merge($permissions, [
            'group.manage_participants',

            'payment.admission',
            'payment.renewal',
            'payment.transactions',

            'chat.send',
            'chat.receive',

            'notification.send',
        ]);

        // 🔹 Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }

        // 🔥 ROLES

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'api']);
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'api']);
        $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'api']);

        // 🔹 Admin → all permissions
        $admin->syncPermissions($permissions);

        $user = User::where('acc_type', 'admin')->first();


        $user->assignRole('admin');


        // 🔹 Staff → limited permissions
        $staff->syncPermissions([
            'dashboard.view',

            'teacher.view',
            'student.view',
            'course.view',

            'announcement.view',
            'notification.view',

            'payment.view',
            'payment.transactions',
        ]);
    }
}
