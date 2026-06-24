# ROADMAP PENGEMBANGAN - SISTEM PKL

**Project:** e-KALDIK - Modul PKL  
**Version:** 1.0  
**Date:** 2026-06-23  
**Estimated Timeline:** 8 Minggu (2 Bulan)

---

## 📅 TIMELINE OVERVIEW

```
┌─────────────────────────────────────────────────────────────────────┐
│                      8-WEEK DEVELOPMENT PLAN                         │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  Week 1-2:  [████████░░░░░░░░░░░░░░] Foundation & Database          │
│  Week 3-4:  [░░░░░░░░████████░░░░░░] Core Features                  │
│  Week 5:    [░░░░░░░░░░░░░░░░████░░] Monitoring & Integration       │
│  Week 6:    [░░░░░░░░░░░░░░░░░░░░██] Dashboard & Reports            │
│  Week 7-8:  [░░░░░░░░░░░░░░░░░░░░░░] Testing, Fixes & Launch        │
│                                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 PHASE 1: FOUNDATION & DATABASE (Week 1-2)

### **Week 1: Database Schema & Models**

**Day 1-2: Migrations**
- [ ] Create migration: `pkl_waves`
- [ ] Create migration: `pkl_placements`
- [ ] Create migration: `pkl_students`
- [ ] Create migration: `pkl_supervisors`
- [ ] Create migration: `pkl_monitorings`
- [ ] Create migration: `pkl_student_moves`
- [ ] Create migration: `pkl_calendar_links`
- [ ] Create migration: `pkl_settings`
- [ ] Run migrations, verify schema

**Day 3-4: Models & Relationships**
- [ ] Create model: `PKLWave` with relationships
- [ ] Create model: `PKLPlacement` with relationships
- [ ] Create model: `PKLStudent` with relationships
- [ ] Create model: `PKLSupervisor` with relationships
- [ ] Create model: `PKLMonitoring` with relationships
- [ ] Create model: `PKLStudentMove` with relationships
- [ ] Create model: `PKLCalendarLink` (polymorphic)
- [ ] Create model: `PKLSetting`
- [ ] Add fillable, casts, accessors/mutators
- [ ] Write unit tests for model relationships

**Day 5: Seeders & Factories**
- [ ] Create `PKLSettingSeeder` (default settings)
- [ ] Create `PKLWaveSeeder` (sample waves)
- [ ] Create `PKLPlacementSeeder` (sample companies)
- [ ] Create factories for all models
- [ ] Run seeders, verify data

**Deliverables:**
- ✅ 8 database tables created
- ✅ 8 Eloquent models with relationships
- ✅ Seeders with sample data
- ✅ Unit tests passing

---

### **Week 2: Configuration & Services**

**Day 6-7: Configuration & Policies**
- [ ] Create `config/pkl.php` configuration file
- [ ] Create `PKLWavePolicy`
- [ ] Create `PKLPlacementPolicy`
- [ ] Create `PKLStudentPolicy`
- [ ] Create `PKLMonitoringPolicy`
- [ ] Register policies in `AuthServiceProvider`
- [ ] Write authorization tests

**Day 8-9: Core Services**
- [ ] Create `PKLCapacityService` (capacity management)
- [ ] Create `PKLAssignmentService` (student assignment logic)
- [ ] Create `PKLCalendarService` (calendar integration)
- [ ] Write unit tests for services
- [ ] Test capacity tracking logic
- [ ] Test assignment validation

**Day 10: Observers & Events**
- [ ] Create `PKLWaveObserver` (auto-create calendar)
- [ ] Create `PKLStudentObserver` (update capacity)
- [ ] Create `PKLMonitoringObserver` (update supervisor stats)
- [ ] Register observers in `AppServiceProvider`
- [ ] Test observer triggers

**Deliverables:**
- ✅ Configuration file complete
- ✅ Policies for all resources
- ✅ Core services implemented
- ✅ Observers registered
- ✅ Unit tests passing (>70% coverage)

---

## 🚀 PHASE 2: CORE FEATURES (Week 3-4)

### **Week 3: Gelombang & Tempat PKL**

**Day 11-12: Gelombang PKL (Waves)**
- [ ] Create Livewire component: `PKL\Wave\Index`
- [ ] Create Livewire component: `PKL\Wave\Create`
- [ ] Create Livewire component: `PKL\Wave\Edit`
- [ ] Create Livewire component: `PKL\Wave\Show`
- [ ] Create Blade views for all components
- [ ] Implement form validation
- [ ] Implement calendar auto-creation
- [ ] Add status management (draft/active/completed)
- [ ] Write feature tests

**Day 13-15: Tempat PKL (Placements)**
- [ ] Create Livewire component: `PKL\Placement\Index`
- [ ] Create Livewire component: `PKL\Placement\Create`
- [ ] Create Livewire component: `PKL\Placement\Edit`
- [ ] Create Livewire component: `PKL\Placement\Show`
- [ ] Create Blade views with capacity display
- [ ] Implement real-time capacity tracking
- [ ] Add company type categorization
- [ ] Implement soft delete
- [ ] Add search & filter functionality
- [ ] Write feature tests

**Deliverables:**
- ✅ Gelombang PKL CRUD complete
- ✅ Tempat PKL CRUD complete
- ✅ Calendar integration working
- ✅ Capacity tracking functional
- ✅ Feature tests passing

---

### **Week 4: Penempatan Siswa**

**Day 16-17: Assign Siswa (Batch)**
- [ ] Create Livewire component: `PKL\Student\Assign` (wizard)
- [ ] Step 1: Pilih Gelombang
- [ ] Step 2: Multi-select Siswa (table with checkboxes)
- [ ] Step 3: Pilih Tempat PKL (with capacity display)
- [ ] Step 4: Set Tanggal & Preview
- [ ] Implement validation (capacity, dates, duplicates)
- [ ] Batch insert with transaction
- [ ] Success notification
- [ ] Write feature tests

**Day 18: Assign Siswa (Single) & Move**
- [ ] Create Livewire component: `PKL\Student\AssignSingle`
- [ ] Create Livewire component: `PKL\Student\Move` (modal)
- [ ] Implement move logic (history tracking)
- [ ] Update capacities on move
- [ ] Validate max moves per student
- [ ] Write feature tests

**Day 19-20: Student List & Detail**
- [ ] Create Livewire component: `PKL\Student\Index`
- [ ] Implement filters (gelombang, tempat, status)
- [ ] Add search by nama/NISN
- [ ] Create Livewire component: `PKL\Student\Show`
- [ ] Display student PKL info
- [ ] Show supervisor info
- [ ] Show monitoring history timeline
- [ ] Write feature tests

**Deliverables:**
- ✅ Batch assignment functional
- ✅ Single assignment functional
- ✅ Move student working with history
- ✅ Student list with filters
- ✅ Student detail page complete
- ✅ All validations working

---

## 🔍 PHASE 3: MONITORING & INTEGRATION (Week 5)

### **Week 5: Pembimbingan & Monitoring**

**Day 21-22: Assign Pembimbing**
- [ ] Create Livewire component: `PKL\Supervisor\Assign`
- [ ] Multi-select students to assign
- [ ] Select supervisor (with load display)
- [ ] Validate max students per supervisor
- [ ] Primary/secondary supervisor option
- [ ] Create Livewire component: `PKL\Supervisor\Index`
- [ ] List all supervisor assignments
- [ ] Create Livewire component: `PKL\Supervisor\LoadReport`
- [ ] Show load per teacher
- [ ] Identify overloaded teachers
- [ ] Write feature tests

**Day 23-24: Input Monitoring**
- [ ] Create Livewire component: `PKL\Monitoring\Create`
- [ ] Form with all fields (date, time, scores, notes)
- [ ] File upload for photos (max 5)
- [ ] Image preview before upload
- [ ] Validation (past date, required fields)
- [ ] Save files to storage
- [ ] Update supervisor stats (monitoring_count, last_visit)
- [ ] Create Livewire component: `PKL\Monitoring\MyStudents`
- [ ] List students for logged-in supervisor
- [ ] Show last visit date & status
- [ ] Quick action: Input monitoring
- [ ] Write feature tests

**Day 25: Monitoring History & Student View**
- [ ] Create Livewire component: `PKL\Monitoring\Index`
- [ ] List all monitorings (admin view)
- [ ] Filters by date, student, supervisor
- [ ] Create Livewire component: `PKL\Monitoring\History`
- [ ] Student view: My PKL history
- [ ] Timeline display with photos
- [ ] Score trend chart (optional)
- [ ] Export to PDF option
- [ ] Write feature tests

**Deliverables:**
- ✅ Supervisor assignment working
- ✅ Load management functional
- ✅ Monitoring input complete
- ✅ Photo upload working
- ✅ History view for students
- ✅ All observers updating stats

---

## 📊 PHASE 4: DASHBOARD & REPORTS (Week 6)

### **Week 6: Reporting & Analytics**

**Day 26-27: Dashboard PKL**
- [ ] Create Livewire component: `PKL\Dashboard\Index`
- [ ] Quick stats cards (4 cards)
- [ ] Pie chart: Students per placement (Chart.js)
- [ ] Bar chart: Monitoring trend
- [ ] Alerts section (capacity full, overdue monitoring)
- [ ] Recent activities feed
- [ ] Make responsive (mobile-first)
- [ ] Write feature tests

**Day 28: Laporan Penempatan Siswa**
- [ ] Create `PKLReportController`
- [ ] Method: `placements()` - List all placements
- [ ] Group by tempat PKL
- [ ] Export to Excel (Laravel Excel)
- [ ] Create PDF view: `reports/student-placements.blade.php`
- [ ] Export to PDF (DomPDF)
- [ ] Write feature tests

**Day 29: Laporan Pembimbingan & Monitoring**
- [ ] Method: `supervisors()` - Load per teacher
- [ ] Show: Name, NIP, Student count, Last visit
- [ ] Export to Excel & PDF
- [ ] Method: `monitoring()` - All monitoring records
- [ ] Filter by date range, wave, supervisor
- [ ] Export to Excel & PDF
- [ ] Write feature tests

**Day 30: Polish & Refinement**
- [ ] Add loading states (skeleton loaders)
- [ ] Add success/error toast notifications
- [ ] Optimize queries (eager loading)
- [ ] Add pagination to large tables
- [ ] Improve mobile responsiveness
- [ ] Add print-friendly CSS
- [ ] Write integration tests

**Deliverables:**
- ✅ Dashboard with charts & stats
- ✅ 3 report types (placement, supervisor, monitoring)
- ✅ Excel & PDF export working
- ✅ Responsive design complete
- ✅ Performance optimized

---

## 🧪 PHASE 5: TESTING, FIXES & LAUNCH (Week 7-8)

### **Week 7: Testing & Bug Fixes**

**Day 31-32: Comprehensive Testing**
- [ ] Run all unit tests (target >70% coverage)
- [ ] Run all feature tests
- [ ] Manual testing: End-to-end workflows
  - [ ] Admin: Create wave, add placements
  - [ ] Guru BK: Assign students (batch & single)
  - [ ] Guru BK: Move student to another placement
  - [ ] Admin: Assign supervisors
  - [ ] Guru: Input monitoring with photos
  - [ ] Student: View PKL info & history
  - [ ] Admin: View dashboard & generate reports
- [ ] Cross-browser testing (Chrome, Firefox, Edge)
- [ ] Mobile device testing (responsive)
- [ ] Accessibility testing (keyboard nav, screen readers)

**Day 33-34: Bug Fixes & Refinement**
- [ ] Fix critical bugs (P0)
- [ ] Fix high-priority bugs (P1)
- [ ] Address edge cases
- [ ] Improve error messages
- [ ] Add missing validations
- [ ] Optimize slow queries
- [ ] Refactor complex code

**Day 35: Security & Performance**
- [ ] Security audit: XSS, CSRF, SQL injection
- [ ] Check authorization (policies working?)
- [ ] Validate file uploads (size, type)
- [ ] Test with large datasets (1000+ records)
- [ ] Database indexing optimization
- [ ] Cache frequently accessed data (settings)
- [ ] Compress uploaded images

**Deliverables:**
- ✅ All tests passing (unit + feature)
- ✅ Critical bugs fixed
- ✅ Security vulnerabilities addressed
- ✅ Performance optimized

---

### **Week 8: Documentation & Deployment**

**Day 36-37: Documentation**
- [ ] Write README.md for PKL module
- [ ] Update main e-KALDIK README
- [ ] Create USER MANUAL (PDF)
  - [ ] How to create gelombang
  - [ ] How to assign students
  - [ ] How to input monitoring
  - [ ] How to generate reports
- [ ] Create ADMIN GUIDE (PDF)
- [ ] API documentation (if applicable)
- [ ] Code comments for complex logic
- [ ] Update changelog

**Day 38: Training & Handover**
- [ ] Prepare training materials (slides)
- [ ] Record video tutorials (screen recording)
  - [ ] Admin walkthrough
  - [ ] Guru BK walkthrough
  - [ ] Guru Pembimbing walkthrough
  - [ ] Student view walkthrough
- [ ] Conduct training session with users
- [ ] Collect feedback
- [ ] Create FAQ document

**Day 39: Deployment Preparation**
- [ ] Backup production database
- [ ] Review deployment checklist
- [ ] Test migrations on staging
- [ ] Verify production environment
- [ ] Configure file storage permissions
- [ ] Set up scheduled tasks (if any)
- [ ] Verify email notifications (if implemented)
- [ ] Create rollback plan

**Day 40: Launch & Monitoring**
- [ ] Deploy to production
- [ ] Run migrations
- [ ] Run seeders (settings only)
- [ ] Smoke testing in production
- [ ] Monitor error logs (Laravel Log)
- [ ] Monitor performance (response times)
- [ ] Provide immediate support to users
- [ ] Collect launch feedback
- [ ] Create post-launch bug list

**Deliverables:**
- ✅ Complete documentation
- ✅ User manual & training materials
- ✅ Successful production deployment
- ✅ Post-launch support ready

---

## 📋 DEVELOPMENT CHECKLIST

### **Backend (Laravel)**
- [ ] 8 migrations created & tested
- [ ] 8 models with relationships
- [ ] 4 policies for authorization
- [ ] 3 core services (Capacity, Assignment, Calendar)
- [ ] 3 observers for auto-updates
- [ ] 20+ Livewire components
- [ ] 3 report exports (Excel/PDF)
- [ ] 50+ unit tests
- [ ] 30+ feature tests

### **Frontend (Blade + Livewire + Alpine.js)**
- [ ] 20+ Blade views
- [ ] 5+ reusable components
- [ ] Responsive design (mobile-first)
- [ ] Form validations (client + server)
- [ ] Loading states & skeletons
- [ ] Toast notifications
- [ ] Confirmation modals
- [ ] Charts (Chart.js)

### **Integration**
- [ ] Calendar integration working
- [ ] File upload/download working
- [ ] Excel import/export working
- [ ] PDF generation working
- [ ] Real-time capacity updates
- [ ] Observer events triggering

### **Quality Assurance**
- [ ] Code review completed
- [ ] All tests passing
- [ ] Security audit passed
- [ ] Performance benchmarks met
- [ ] Accessibility compliance (WCAG AA)
- [ ] Browser compatibility verified
- [ ] Mobile responsiveness verified

### **Documentation**
- [ ] User manual complete
- [ ] Admin guide complete
- [ ] Training materials ready
- [ ] Code documented
- [ ] API docs (if applicable)

---

## ⚠️ RISK MANAGEMENT

### **Risk 1: Calendar Integration Conflicts**
**Likelihood:** Medium | **Impact:** High
**Mitigation:**
- Use polymorphic relationship (non-intrusive)
- Extensive testing with existing calendar features
- Fallback: Manual entry if auto-creation fails

### **Risk 2: Capacity Overbooking**
**Likelihood:** Medium | **Impact:** Critical
**Mitigation:**
- Database transaction with row locking
- Real-time capacity validation
- Comprehensive testing with concurrent requests

### **Risk 3: Performance with Large Datasets**
**Likelihood:** Low | **Impact:** Medium
**Mitigation:**
- Database indexing on foreign keys
- Eager loading to prevent N+1 queries
- Pagination (50 items per page)
- Load testing before launch

### **Risk 4: File Upload Issues**
**Likelihood:** Medium | **Impact:** Medium
**Mitigation:**
- Strict validation (size, type)
- Image compression before save
- Fallback to default storage
- Error handling with user-friendly messages

### **Risk 5: User Adoption Resistance**
**Likelihood:** Medium | **Impact:** High
**Mitigation:**
- Intuitive UI/UX design
- Comprehensive training
- Video tutorials
- On-demand support in first week

---

## 🎯 SUCCESS METRICS

### **Technical Metrics**
- ✅ Page load time < 2 seconds
- ✅ API response time < 500ms
- ✅ Test coverage > 70%
- ✅ Zero critical bugs in production (first week)
- ✅ Uptime 99%+ during working hours

### **Business Metrics**
- ✅ 100% siswa kelas XII ter-assign PKL
- ✅ 90% compliance monitoring schedule
- ✅ 50% reduction in admin workload
- ✅ User satisfaction score > 4/5
- ✅ Zero data loss incidents

### **Adoption Metrics**
- ✅ 100% guru BK using the system (Week 1)
- ✅ 80% guru pembimbing using the system (Week 2)
- ✅ 90% siswa accessing their PKL info (Week 3)

---

## 🚀 POST-LAUNCH PLAN

### **Week 9-10: Stabilization**
- Monitor production logs daily
- Fix reported bugs (priority queue)
- Collect user feedback
- Performance tuning based on real usage
- Create knowledge base (FAQ)

### **Week 11-12: Iteration**
- Implement quick wins from feedback
- UI/UX improvements
- Add missing features (P1 only)
- Optimize workflows based on usage patterns

### **Month 3-6: Enhancements**
**Future Features (Phase 2):**
- [ ] Google Maps integration for placement location
- [ ] WhatsApp/Email notifications
- [ ] Student self-registration (with approval)
- [ ] Industry partner portal (view assigned students)
- [ ] Mobile app (React Native/Flutter)
- [ ] Advanced analytics & predictive insights
- [ ] Automated scheduling for monitoring visits
- [ ] Integration with e-Certificate system

---

## 📞 SUPPORT PLAN

### **Week 1 (Launch Week):**
- **Support Hours:** 07:00 - 17:00 (extended)
- **Response Time:** < 1 hour
- **Support Channel:** WhatsApp Group + Email
- **On-site Support:** Available if needed

### **Week 2-4:**
- **Support Hours:** 08:00 - 16:00
- **Response Time:** < 2 hours
- **Support Channel:** Email + Support Ticket

### **Month 2+:**
- **Support Hours:** 08:00 - 16:00 (weekdays)
- **Response Time:** < 24 hours
- **Support Channel:** Support Ticket System

---

## ✅ DEFINITION OF DONE

A feature is considered **DONE** when:
- [ ] Code implemented & working
- [ ] Unit tests written & passing
- [ ] Feature tests written & passing
- [ ] Code reviewed by peer
- [ ] Documentation updated
- [ ] UI/UX reviewed
- [ ] Tested on staging environment
- [ ] Approved by product owner
- [ ] Deployed to production
- [ ] User acceptance testing passed

---

## 🎉 MILESTONES

| Week | Milestone | Completion Criteria |
|------|-----------|---------------------|
| 2 | Foundation Complete | Database, models, services ready |
| 4 | Core Features Complete | Waves, placements, students working |
| 5 | Monitoring Complete | Supervisors & monitoring functional |
| 6 | Reports Complete | Dashboard & exports working |
| 7 | Testing Complete | All tests passing, bugs fixed |
| 8 | Launch Ready | Deployed to production, users trained |

---

**STATUS:** ✅ ROADMAP COMPLETE  
**READY FOR:** Development Kickoff 🚀
