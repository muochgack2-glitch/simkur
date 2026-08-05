# Copy Journal to Other Classes Feature

## Overview
This feature allows teachers to quickly copy an existing teaching journal to multiple other classes with different dates and time slots. This is useful when teaching the same material to multiple classes.

## Feature Details

### User Interface
- **Copy Button**: Green copy button appears in the action column of each journal entry (between Edit and Delete buttons)
- **Access**: Available for journal owners and admins only
- **Icon**: Copy/duplicate icon (two overlapping rectangles)

### Copy Modal Components

1. **Source Journal Info Box**
   - Shows material being copied
   - Displays original class, subject, and date
   - Blue background for easy identification

2. **Target Classes Selection**
   - Multiple checkbox selection
   - Shows student count for each class
   - Excludes source class from the list
   - Scrollable list with max height
   - Shows count of selected classes

3. **Date Picker**
   - Default: Same date as source journal
   - Validates that date is required
   - Changes available time slots when date changes

4. **Time Slot Dropdown**
   - Single selection (one time slot per copy)
   - Shows time slots available for the selected date
   - Format: "Jam 1 (07:00 - 08:30)"
   - Dynamically loads based on day of week
   - Shows warning if no time slots available

5. **Information Box**
   - Clearly shows what gets copied
   - Clearly shows what doesn't get copied

### What Gets Copied
✅ **Copied Fields:**
- Subject (Mata Pelajaran)
- Learning Objective (Tujuan Pembelajaran)
- Topic (Materi Pokok)
- Teaching Method (Metode Pembelajaran)
- Notes (Catatan)

❌ **NOT Copied:**
- Activity Photo → Set to `null`
- Attendance Data → Reset to all "hadir" (present) for all students in target class

### Backend Logic

#### Validation Rules
```php
- copyTargetClasses: required|array|min:1
- copyDate: required|date
- copyTimeSlot: required|string
```

#### Duplicate Detection
- Uses `whereJsonContains()` for JSON array field checking
- Checks: same teacher, same class, same date, same time slot
- Skips duplicate journals with informative message

#### Copy Process
1. **For each selected target class:**
   - Check for duplicate journal
   - Use Laravel's `replicate()` method to copy journal
   - Override: class_id, date, time_slot
   - Set activity_photo to null
   - Save new journal

2. **Create Default Attendance:**
   - Query all active students in target class
   - Create attendance record with status "hadir" for each student
   - Update attendance statistics

3. **Error Handling:**
   - Try-catch for each class copy operation
   - Collect errors with class names
   - Continue with other classes if one fails

#### Success Messages
- Success: "Jurnal berhasil di-copy ke {count} kelas"
- With skips: "Jurnal berhasil di-copy ke {count} kelas. {skipCount} kelas dilewati: {classNames}"
- All failed: "Gagal copy jurnal: {errors}"

## Technical Implementation

### Files Modified
1. **app/Livewire/TeachingJournal/Index.php**
   - Added copy modal properties
   - Added `openCopyModal()` method
   - Added `closeCopyModal()` method
   - Added `executeCopy()` method
   - Added `updatedCopyDate()` method
   - Added `loadTimeSlots()` helper method

2. **resources/views/livewire/teaching-journal/index.blade.php**
   - Added copy button in actions column
   - Added copy modal HTML
   - Added time slot dropdown
   - Added info box for copy behavior

### Key Technical Details

#### Time Slot Storage
- Time slots are stored as JSON array in database
- Format: `["Jam 1 (07:00 - 08:30)", "Jam 2 (08:30 - 09:00)"]`
- Uses `time_slot` array cast in TeachingJournal model

#### JSON Field Querying
- **Before (Incorrect):**
  ```php
  ->where('time_slot', 'like', '%' . $this->copyTimeSlot . '%')
  ```
  - LIKE doesn't work properly with JSON arrays
  
- **After (Correct):**
  ```php
  ->whereJsonContains('time_slot', $this->copyTimeSlot)
  ```
  - Properly checks if JSON array contains the value

#### Day of Week Mapping
- Converts PHP date format to Indonesian day names
- Maps: Monday→Senin, Tuesday→Selasa, etc.
- Loads time slots using `forDay()` scope on TimeSlot model

## Benefits
1. **Time Saving**: Copy journal data instead of re-entering
2. **Consistency**: Same material taught across multiple classes
3. **Flexibility**: Different dates and time slots for each target class
4. **Safety**: Prevents duplicate journals for same class/date/time
5. **Fresh Start**: Attendance and photos don't carry over (intentional)

## Usage Flow
1. Teacher views journal list
2. Clicks green "Copy" button on desired journal
3. Modal opens showing source journal info
4. Select one or more target classes (checkboxes)
5. Choose date (defaults to same date)
6. Select time slot (single dropdown)
7. Review what gets copied vs not copied
8. Click "Copy Jurnal" button
9. System creates journals for each selected class
10. Success message shows count and any skipped classes

## Database Schema
- `teaching_journals.time_slot`: JSON array field
- `teaching_journals.activity_photo`: nullable string field
- `student_attendances.status`: enum('hadir','sakit','izin','alpha')

## Notes
- Only shows copy button to journal owner and admin
- Modal uses Livewire wire:model for reactive updates
- Time slots load dynamically based on selected date
- Student list fetched from enrollments relationship
- Uses Laravel's replicate() for clean object copying
