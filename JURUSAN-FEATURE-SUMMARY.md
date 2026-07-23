# Fitur Jurusan SMK - Implementation Summary

## Tujuan
Menambahkan sistem jurusan untuk SMK dengan 3 jurusan:
- **MPLB** - Manajemen Perkantoran dan Layanan Bisnis
- **AKL** - Akuntansi dan Keuangan Lembaga
- **BUSANA** - Tata Busana

## Progress

### ✅ 1. Database Migration
- **File**: `database/migrations/2026_07_20_123737_add_major_to_users_table.php`
- **Status**: DONE
- Added `major` enum field to `users` table
- Values: MPLB, AKL, BUSANA
- Nullable (only required for siswa)

### ✅ 2. Model Update
- **File**: `app/Models/User.php`
- **Status**: DONE
- Added `major` to fillable
- Added helper methods:
  - `getMajorLabel()` - Returns full major name
  - `getFullClassLabel()` - Returns "X MPLB" format

### ✅ 3. User Management Forms
- **Files**: 
  - `app/Livewire/User/Create.php` ✅
  - `app/Livewire/User/Edit.php` ✅
  - `resources/views/livewire/user/create.blade.php` ✅
  - `resources/views/livewire/user/edit.blade.php` ✅
- **Status**: DONE
- Added grade and major fields (shown only for siswa role)
- Validation rules updated
- Forms use wire:model.live to show/hide fields based on role

### ✅ 4. Next Steps (COMPLETED!)

#### A. Update User Index ✅
- **DONE**: Show major column for siswa
- **DONE**: Add filter by grade
- **DONE**: Add filter by major
- **DONE**: Updated table display with Kelas/Jurusan column

#### B. Update Class Report ✅
- **DONE**: Add filter by major in Class Report
- **DONE**: Update query to filter by grade + major
- **DONE**: Update DiagnosticProfileService to accept major parameter
- **DONE**: Update view with major filter dropdown

#### C. Update Assessment Target ✅
- **DONE**: Add major targeting for assessments
- **DONE**: Assessment can target specific majors (target_majors field)
- **DONE**: Update assessment create/edit forms
- **DONE**: Update Assessment model with helper methods
- **DONE**: Added `isForStudent()` method to check if assessment is for specific student

#### D. Update Seeder
- **TODO**: Update UserSeeder to include majors for siswa
- **TODO**: Ensure test data has proper majors

## Database Schema

```sql
ALTER TABLE users ADD COLUMN major 
  ENUM('MPLB', 'AKL', 'BUSANA') NULL 
  AFTER grade
  COMMENT 'Jurusan SMK: MPLB, AKL, BUSANA';
```

## Usage Examples

```php
// Create siswa with major
User::create([
    'name' => 'Budi Santoso',
    'role' => 'siswa',
    'grade' => 'X',
    'major' => 'MPLB', // Required for siswa
]);

// Get major label
$user->getMajorLabel(); // "Manajemen Perkantoran dan Layanan Bisnis"

// Get full class label
$user->getFullClassLabel(); // "X MPLB"

// Query by major
User::where('role', 'siswa')
    ->where('grade', 'X')
    ->where('major', 'MPLB')
    ->get();
```

## Notes
- Major field is nullable in database (hanya siswa yang wajib isi)
- Validation enforced di Livewire level
- Frontend form shows/hides grade+major based on selected role
- Existing users (non-siswa) akan punya major = NULL
