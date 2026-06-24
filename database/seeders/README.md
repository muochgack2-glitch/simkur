# Database Seeders Documentation

## 📋 Available Seeders

### 1. **ProductionSeeder.php** (RECOMMENDED FOR PRODUCTION) ⭐
Seeder minimal untuk production environment.

**Includes:**
- ✅ 3 Default Users (Admin, Kurikulum, Guru)
- ✅ 14 Activity Types (complete with icons)
- ✅ 7 Essential Settings
- ✅ Academic Year 2026/2027
- ✅ 2 Semesters (Ganjil & Genap)
- ❌ NO sample activities (clean start)

**Usage:**
```bash
# Automatic (based on APP_ENV=production)
php artisan db:seed

# Manual call
php artisan db:seed --class=ProductionSeeder
```

**Default Credentials:**
- **Admin:** admin@ekaldik.local / password
- **Kurikulum:** kurikulum@ekaldik.local / password
- **Guru:** guru@ekaldik.local / password

⚠️ **IMPORTANT:** Change passwords immediately after deployment!

---

### 2. **DatabaseSeeder.php** (SMART SEEDER)
Main seeder yang otomatis memilih seeder berdasarkan environment.

**Behavior:**
- `APP_ENV=production` → Runs **ProductionSeeder**
- `APP_ENV=local` → Runs full development seeders

**Usage:**
```bash
php artisan db:seed
```

---

### 3. **Development Seeders** (FOR TESTING ONLY)

#### **UserSeeder.php**
Creates 5 test users with various roles.

#### **ActivityTypeSeeder.php**
Creates 14 activity types (same as ProductionSeeder).

#### **SettingSeeder.php**
Creates default settings.

#### **ActivitySeeder.php** ⚠️ Development Only
Creates sample activities for testing.
**DO NOT USE IN PRODUCTION** - Empty calendar is better for real usage.

#### **TestGradeSeeder.php**
Creates test student users (Grade 10, 11, 12).

---

## 🚀 Production Deployment Guide

### Step 1: Fresh Install
```bash
# On production server
php artisan migrate:fresh
php artisan db:seed --class=ProductionSeeder
```

### Step 2: Verify Data
```bash
php artisan tinker

# Check users
User::count(); // Should be 3

# Check activity types
ActivityType::count(); // Should be 14

# Check academic year
AcademicYear::first(); // 2026/2027
```

### Step 3: Change Passwords
Login to web interface and change all default passwords:
1. Login as admin
2. Go to Profile → Change Password
3. Repeat for all accounts

---

## 🧪 Development Setup

### Full Development Data
```bash
# Reset database with sample data
php artisan migrate:fresh --seed
```

This will create:
- Multiple test users
- Activity types
- Settings
- Academic year with semesters
- **Sample activities** for testing

---

## 📝 Seeder Details

### Activity Types (14 types)

| Code | Name | Category | Color | Holiday | Exam |
|------|------|----------|-------|---------|------|
| LAP | Libur Awal Puasa | akademik | #3B82F6 | ✅ | ❌ |
| PKL | PKL | akademik | #3B82F6 | ❌ | ❌ |
| MPLS | MPLS | non_akademik | #10B981 | ❌ | ❌ |
| PTS | PTS | akademik | #F59E0B | ❌ | ✅ |
| PAS | PAS | akademik | #EF4444 | ❌ | ✅ |
| PAT | PAT | akademik | #DC2626 | ❌ | ✅ |
| ANBK | ANBK | akademik | #8B5CF6 | ❌ | ✅ |
| LIBNAS | Libur Nasional | non_akademik | #6B7280 | ✅ | ❌ |
| LIBSEM | Libur Semester | non_akademik | #3B82F6 | ✅ | ❌ |
| RAPAT | Rapat Guru | non_akademik | #14B8A6 | ❌ | ❌ |
| KEGIATAN | Kegiatan Sekolah | non_akademik | #EC4899 | ❌ | ❌ |
| UPACARA | Upacara | non_akademik | #8B5CF6 | ❌ | ❌ |
| TKA | TKA | akademik | #DC2626 | ❌ | ❌ |
| RAPOR | Pembagian Rapor | akademik | #059669 | ❌ | ❌ |

### Default Settings (7 settings)

| Key | Default Value | Type |
|-----|---------------|------|
| school_name | NAMA SEKOLAH | text |
| school_address | Alamat Sekolah | text |
| school_logo | null | file |
| principal_name | ________________ | text |
| principal_niy | ______________ | text |
| weekend_days | ["saturday","sunday"] | json |
| app_timezone | Asia/Jakarta | text |

### Academic Year 2026/2027

- **Start Date:** 13 Juli 2026
- **End Date:** 20 Juni 2027
- **Status:** Active

**Semesters:**
1. **Semester Ganjil:** 13 Jul 2026 - 20 Des 2026
2. **Semester Genap:** 5 Jan 2027 - 20 Jun 2027

---

## 🔄 Update/Refresh Seeders

### Re-seed Without Losing Data
```bash
# Only add missing data
php artisan db:seed --class=ProductionSeeder
```

### Fresh Start (⚠️ DELETES ALL DATA)
```bash
php artisan migrate:fresh --seed
```

---

## 🛠️ Custom Seeder Commands

### Seed Specific Seeder
```bash
php artisan db:seed --class=ActivityTypeSeeder
php artisan db:seed --class=SettingSeeder
php artisan db:seed --class=UserSeeder
```

### Seed with Force (Production)
```bash
php artisan db:seed --force
```

---

## 📚 Adding New Seeders

### Create New Seeder
```bash
php artisan make:seeder NewFeatureSeeder
```

### Add to DatabaseSeeder
```php
$this->call([
    ProductionSeeder::class,
    NewFeatureSeeder::class, // Add here
]);
```

---

## ⚠️ Important Notes

1. **ProductionSeeder** is safe to run multiple times (uses `create()` with checks)
2. **ActivitySeeder** should NEVER be used in production
3. Always backup database before running `migrate:fresh`
4. Change default passwords immediately after seeding
5. Verify data after seeding

---

## 🐛 Troubleshooting

### Error: Duplicate entry
```bash
# Clear database first
php artisan migrate:fresh
php artisan db:seed
```

### Error: Class not found
```bash
# Clear cache and autoload
composer dump-autoload
php artisan config:clear
```

### Wrong environment seeder
```bash
# Check APP_ENV
php artisan env

# Force specific seeder
php artisan db:seed --class=ProductionSeeder --force
```

---

**Last Updated:** 24 Juni 2026  
**Version:** 1.0  
**Status:** Production Ready ✅
