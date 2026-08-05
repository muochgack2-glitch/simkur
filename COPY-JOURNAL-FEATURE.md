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

4. **Time Range Selection** (Similar to Teaching Schedule)
   - **Jam Mulai (Start Time)**: Dropdown showing available time slots for selected date
   - **Jam Selesai (End Time)**: Dropdown showing time slots >= start time
   - Format: "Jam 1 (07:00 - 08:30)"
   - Dynamically loads based on day of week
   - Shows warning if no time slots available
   - Automatically calculates total JP (Jam Pelajaran)
   - **Auto-Skip Breaks**: Order 1, 5, and 10 (istirahat) are automatically excluded from JP count
   - Shows total JP indicator when both start and end times are selected

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
- copyStartTimeSlotId: required|exists:time_slots,id
- copyEndTimeSlotId: required|exists:time_slots,id
- Start time must be <= end time (validated before processing)
```

#### Time Slot Processing
1. **Get Time Slot Range:**
   - Finds all time slots between start and end (inclusive)
   - Filters by day of week from selected date
   - Uses `forDay()` scope to include slots for specific day or 'all' days

2. **Auto-Skip Breaks:**
   - Excludes pre-class slots (order <= 1)
   - Excludes break times (order == 5 or order == 10)
   - Only teaching slots are included in the journal

3. **Create Display Names Array:**
   - Converts selected time slots to array of display_name values
   - Format: `["Jam 2 (07:30 - 08:00)", "Jam 3 (08:00 - 08:30)", ...]`
   - This array is stored in the `time_slot` JSON field

#### Duplicate Detection
- Uses `whereJsonContains()` for JSON array field checking
- Checks for overlapping time slots: if ANY slot in the range already exists for that teacher/class/date
- Checks: same teacher, same class, same date, overlapping time slot
- Skips duplicate journals with informative message

#### Copy Process
1. **For each selected target class:**
   - Check for duplicate/overlapping journals
   - Use Laravel's `replicate()` method to copy journal
   - Override: class_id, date, time_slot (array of display names)
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
- Success: "Jurnal berhasil di-copy ke {count} kelas ({jpCount} JP)"
- With skips: "Jurnal berhasil di-copy ke {count} kelas ({jpCount} JP). {skipCount} kelas dilewati: {classNames}"
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
- Format: `["Jam 1 (07:00 - 08:30)", "Jam 2 (08:30 - 09:00)", ...]`
- Uses `time_slot` array cast in TeachingJournal model
- Can store multiple consecutive time slots (similar to teaching schedule)

#### Time Slot Selection Logic
- **Jam Mulai**: Shows all active time slots for selected date's day of week
- **Jam Selesai**: Shows only slots with order >= start slot's order
- **Reactive Updates**: End time dropdown updates when start time changes
- **Day Mapping**: Converts PHP date format (Monday) to Indonesian (Senin)
- **forDay() Scope**: Includes slots marked for specific day OR 'all' days

#### Auto-Skip Break Times
- System automatically excludes break times when counting JP:
  - Order 1: Pre-class preparation
  - Order 5: First break (istirahat 1)
  - Order 10: Second break (istirahat 2)
- Only actual teaching slots (order > 1, order != 5, order != 10) are counted
- This matches the behavior in Teaching Schedule feature

#### JSON Field Querying
- **Before (Single Slot - Incorrect):**
  ```php
  ->where('time_slot', 'like', '%' . $this->copyTimeSlot . '%')
  ```
  - LIKE doesn't work properly with JSON arrays
  
- **Current (Range with Overlap Detection - Correct):**
  ```php
  foreach ($timeSlotDisplayNames as $slotName) {
      $exists = TeachingJournal::where('teacher_id', auth()->id())
          ->where('class_id', $targetClassId)
          ->where('date', $this->copyDate)
          ->whereJsonContains('time_slot', $slotName)
          ->exists();
      
      if ($exists) {
          $hasOverlap = true;
          break;
      }
  }
  ```
  - Properly checks if any slot in the range overlaps with existing journals
  - Prevents time conflicts

#### Day of Week Mapping
- Converts PHP date format to Indonesian day names
- Maps: Monday→Senin, Tuesday→Selasa, etc.
- Loads time slots using `forDay()` scope on TimeSlot model

## Benefits
1. **Time Saving**: Copy journal data instead of re-entering
2. **Consistency**: Same material taught across multiple classes
3. **Flexibility**: Different dates and time slots for each target class
4. **Multiple Time Slots**: Support for teaching the same material across multiple consecutive periods
5. **Auto-Break Handling**: Breaks are automatically excluded from teaching hours
6. **Safety**: Prevents duplicate journals and overlapping time slots
7. **Fresh Start**: Attendance and photos don't carry over (intentional)
8. **Real-time Feedback**: Shows total JP calculation as slots are selected

## Usage Flow
1. Teacher views journal list
2. Clicks green "Copy" button on desired journal
3. Modal opens showing source journal info
4. Select one or more target classes (checkboxes)
5. Choose date (defaults to same date)
6. Select **Jam Mulai** (start time)
7. Select **Jam Selesai** (end time) - only shows slots >= start time
8. View total JP calculation (breaks auto-skipped)
9. Review what gets copied vs not copied
10. Click "Copy Jurnal" button
11. System creates journals for each selected class
12. Success message shows count, total JP, and any skipped classes

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
