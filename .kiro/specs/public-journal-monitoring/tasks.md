# Implementation Tasks: Public Journal Monitoring

**Feature:** Halaman Monitoring Publik Jurnal Guru Hari Ini  
**Spec Location:** `.kiro/specs/public-journal-monitoring/`

---

## 📋 Task List

### ✅ Phase 1: MVP (Must Have)

#### Task 1: Create Livewire Component
**File:** `app/Livewire/JournalMonitoring/Index.php`

**Subtasks:**
- [ ] Create component structure
- [ ] Implement `mount()` method
  - Detect today's date
  - Convert to Indonesian day name
  - Get active academic year
- [ ] Implement `categorizeTeachers()` method
  - Query schedules for today
  - Query journals for today
  - Match and categorize
  - Return categorized data
- [ ] Implement `calculateStats()` method
  - Count total teachers
  - Calculate percentages
  - Return summary data
- [ ] Implement `refresh()` method (manual refresh)
- [ ] Add wire:poll for auto-refresh (5 min)

**Acceptance Criteria:**
- Component loads data correctly
- Categorization logic works for all scenarios
- Stats calculated accurately
- Auto-refresh every 5 minutes

**Estimated Time:** 3-4 hours

---

#### Task 2: Create Blade View
**File:** `resources/views/livewire/journal-monitoring/index.blade.php`

**Subtasks:**
- [ ] Create header section
  - Display date in Indonesian format
  - Show auto-refresh countdown
  - Add manual refresh button
- [ ] Create summary stats section
  - Total teachers
  - Breakdown by category with percentages
- [ ] Create 3-column layout
  - Column 1: ✅ Sudah Isi (Green)
  - Column 2: ⚠️ Isi Sebagian (Yellow)
  - Column 3: ❌ Belum Isi (Red)
- [ ] Display teacher cards in each column
  - Teacher name
  - JP progress (filled/total)
  - Percentage bar
- [ ] Add responsive design
  - Desktop: 3 columns
  - Tablet: 2 columns + 1 row
  - Mobile: 1 column stack
- [ ] Add loading state
- [ ] Add empty state (no schedule today)

**Acceptance Criteria:**
- UI matches design mockup
- Responsive on all devices
- Colors and icons correct
- Loading and empty states work

**Estimated Time:** 3-4 hours

---

#### Task 3: Register Public Route
**File:** `routes/web.php`

**Subtasks:**
- [ ] Add public route
```php
Route::get('/monitoring/jurnal-hari-ini', 
    \App\Livewire\JournalMonitoring\Index::class)
    ->name('monitoring.journal.today');
```
- [ ] Optional: Add throttle middleware
- [ ] Test route accessible without login

**Acceptance Criteria:**
- Route accessible from browser
- No authentication required
- Correct component loaded

**Estimated Time:** 15 minutes

---

#### Task 4: Create Helper Methods
**Location:** Component or separate service class

**Subtasks:**
- [ ] Create `getDayNameInIndonesian($date)` helper
  - Convert Carbon date to Indonesian day
  - Handle edge cases
- [ ] Create `calculateJPFromTimeSlot($timeSlot)` helper
  - Handle array format
  - Handle single format
  - Return count
- [ ] Create `matchJournalToSchedule()` helper (optional)
  - Match time slots between schedule and journal

**Acceptance Criteria:**
- Helpers work for all input types
- Edge cases handled
- Well documented

**Estimated Time:** 1-2 hours

---

#### Task 5: Write Unit Tests
**File:** `tests/Unit/JournalMonitoringTest.php`

**Subtasks:**
- [ ] Test categorization with all complete
- [ ] Test categorization with partial
- [ ] Test categorization with none filled
- [ ] Test categorization with no schedule
- [ ] Test percentage calculation
- [ ] Test edge case: 0 total JP
- [ ] Test day name conversion

**Acceptance Criteria:**
- All tests pass
- Edge cases covered
- Code coverage > 80%

**Estimated Time:** 2-3 hours

---

#### Task 6: Integration Testing
**File:** `tests/Feature/JournalMonitoringPageTest.php`

**Subtasks:**
- [ ] Test page loads successfully
- [ ] Test public access (no auth)
- [ ] Test with real database data
- [ ] Test auto-refresh mechanism
- [ ] Test manual refresh button
- [ ] Test responsive layout

**Acceptance Criteria:**
- All integration tests pass
- Page functional end-to-end
- No errors in console

**Estimated Time:** 2 hours

---

#### Task 7: Performance Optimization
**Location:** Component class

**Subtasks:**
- [ ] Add eager loading for relationships
```php
->with(['teacher', 'schoolClass', 'subject'])
```
- [ ] Optimize queries (max 5 queries per request)
- [ ] Add database indexes if needed
  - `teaching_schedules.day_of_week`
  - `teaching_journals.date`
- [ ] Optional: Add cache (5 min TTL)
- [ ] Test performance with large dataset (50+ teachers)

**Acceptance Criteria:**
- Page load < 1 second
- Refresh < 500ms
- Max 5 database queries

**Estimated Time:** 1-2 hours

---

#### Task 8: Documentation
**Files:** `README.md` or inline docs

**Subtasks:**
- [ ] Document component usage
- [ ] Document query logic
- [ ] Add inline code comments
- [ ] Update project README (if needed)
- [ ] Create user guide (optional)

**Acceptance Criteria:**
- Code well documented
- Logic explained
- Easy to maintain

**Estimated Time:** 1 hour

---

### 🚀 Phase 2: Enhancements (Nice to Have)

#### Task 9: Export to Excel Feature
**Estimated Time:** 3-4 hours

**Subtasks:**
- [ ] Add "Export to Excel" button
- [ ] Create export logic (Laravel Excel)
- [ ] Include all teacher data
- [ ] Format Excel with colors
- [ ] Add download functionality

---

#### Task 10: Filter Features
**Estimated Time:** 2-3 hours

**Subtasks:**
- [ ] Add filter by subject dropdown
- [ ] Add filter by class dropdown
- [ ] Update queries based on filters
- [ ] Add "Clear filters" button

---

#### Task 11: Detail Modal
**Estimated Time:** 2-3 hours

**Subtasks:**
- [ ] Add expand button per teacher
- [ ] Create modal component
- [ ] Show detailed schedule breakdown
- [ ] Show which JP filled/not filled

---

#### Task 12: History View
**Estimated Time:** 4-5 hours

**Subtasks:**
- [ ] Add date picker
- [ ] Load data for selected date
- [ ] Update UI to show historical data
- [ ] Add navigation (previous/next day)

---

#### Task 13: Notification Integration
**Estimated Time:** 5-6 hours

**Subtasks:**
- [ ] WhatsApp notification via API
- [ ] Email reminder via Laravel Mail
- [ ] Scheduled job for reminders
- [ ] Configure notification settings

---

## 📊 Progress Tracking

| Phase | Tasks Complete | Status |
|-------|---------------|--------|
| Phase 1 MVP | 0/8 | ⏳ Not Started |
| Phase 2 Enhancements | 0/5 | 📅 Planned |

---

## 🎯 Definition of Done

**MVP is complete when:**
- [x] All Phase 1 tasks completed
- [x] All tests pass
- [x] Page accessible publicly
- [x] Auto-refresh works
- [x] Responsive on all devices
- [x] Performance targets met
- [x] Code reviewed and documented

**Phase 2 is complete when:**
- [ ] At least 3/5 enhancement tasks done
- [ ] Export to Excel works
- [ ] Filters functional
- [ ] Tests updated

---

## 🐛 Known Issues / Blockers

None yet.

---

## 📝 Notes

- Prioritas utama: MVP Phase 1
- Phase 2 bisa dikerjakan incremental
- Koordinasi dengan stakeholder untuk validasi UI
- Testing di production-like environment sebelum deploy

---

**Last Updated:** 2026-08-04
