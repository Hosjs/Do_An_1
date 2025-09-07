<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UsersRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        // Tạo user Admin
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
        ]);

        // Gán role
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // Tạo user Teacher
        $teacher = User::firstOrCreate([
            'email' => 'teacher@example.com',
        ], [
            'name' => 'Mr. Teacher',
            'password' => Hash::make('password'),
        ]);
        $teacher->roles()->syncWithoutDetaching([$teacherRole->id]);

        // Tạo user Student
        $student = User::firstOrCreate([
            'email' => 'student@example.com',
        ], [
            'name' => 'Student One',
            'password' => Hash::make('password'),
        ]);
        $student->roles()->syncWithoutDetaching([$studentRole->id]);
    }
}
