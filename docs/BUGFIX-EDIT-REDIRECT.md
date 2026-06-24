# 🐛 BUGFIX: Edit Activity Redirect from Calendar

**Date:** 2026-06-23  
**Issue:** When editing activity from calendar, after save there's no success message and no redirect back to activities page  
**Status:** ✅ FIXED

---

## 📋 PROBLEM DESCRIPTION

### Symptoms:
- User clicks event on calendar → opens edit page
- User changes activity data → clicks "Simpan"
- **Problem:**
  - No success message appears
  - No redirect to activities page
  - Page stays on edit form

### Root Cause:
When activity is opened from calendar using JavaScript navigation:
```javascript
window.location.href = '/activities/' + event.id + '/edit'
```

The Laravel redirect in Edit.php doesn't work properly:
```php
return redirect()->route('activities.index'); // ❌ Doesn't work
```

This is because the page was loaded via JavaScript navigation, not Livewire's SPA navigation, so traditional Laravel redirects don't trigger properly.

---

## ✅ SOLUTION

Changed from Laravel redirect to **Livewire navigation**:

### Before (Broken):
```php
session()->flash('success', 'Kegiatan berhasil diperbarui!');
return redirect()->route('activities.index');
```

### After (Fixed):
```php
session()->flash('success', 'Kegiatan berhasil diperbarui!');
return $this->redirect(route('activities.index'), navigate: true);
```

---

## 📁 FILES CHANGED

### `app/Livewire/Activity/Edit.php`
**Method:** `update()`  
**Line:** ~267  
**Change:** `redirect()->route()` → `$this->redirect(route(), navigate: true)`

### `app/Livewire/Activity/Create.php`
**Method:** `store()`  
**Line:** ~276  
**Change:** `redirect()->route()` → `$this->redirect(route(), navigate: true)`  
**Note:** Fixed for consistency, even though Create is typically accessed via normal links

---

## 🧪 TESTING

### Test Scenario:
1. Login as **Admin**
2. Go to **Kegiatan** → **Month View** (Calendar)
3. Click any event on calendar
4. Make changes to the activity
5. Click **Simpan**

### Expected Result After Fix:
- ✅ Success message appears: "Kegiatan berhasil diperbarui!"
- ✅ Redirects to activities list page
- ✅ Updated data visible in list
- ✅ Works when editing from both calendar and list view

### Before Fix:
- ❌ No success message
- ❌ No redirect (stays on edit page)
- ❌ User confused whether save worked

---

## 🔍 TECHNICAL DETAILS

### Why `$this->redirect()` works:
- Livewire's `redirect()` method works across all navigation contexts
- The `navigate: true` parameter uses Livewire's SPA navigation
- Compatible with both:
  - Livewire wire:navigate links
  - JavaScript `window.location.href` navigation
  - Direct URL access

### Alternative Considered:
Could also change calendar to use Livewire navigation:
```javascript
// Instead of:
window.location.href = '/activities/' + event.id + '/edit';

// Use:
Livewire.navigate('/activities/' + event.id + '/edit');
```

But changing Edit.php is better because:
- Single file change (not multiple calendar handlers)
- More consistent with Livewire best practices
- Works regardless of how page is accessed

---

## 📚 RELATED ISSUES

This fix also improves:
1. **Consistency:** Create.php likely uses `$this->redirect()` already
2. **User Experience:** Success message now always appears
3. **Navigation:** Smooth transition back to list

---

## ✅ VERIFICATION CHECKLIST

- [x] Code changed in Edit.php
- [x] Code changed in Create.php (for consistency)
- [x] Solution tested with calendar navigation
- [x] Success message appears after save
- [x] Redirect works properly
- [x] Works from both calendar and list view edit

---

## 📝 NOTES

- No migration needed
- No asset rebuild needed
- No database changes
- Just single line code change
- Backward compatible (doesn't break existing functionality)

---

**Status:** Ready for production ✅
