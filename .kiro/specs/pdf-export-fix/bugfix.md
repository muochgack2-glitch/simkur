# Bugfix Requirements Document

## Introduction

This document defines the requirements for fixing the PDF export and preview functionality in admin export pages. The application has three admin export pages (yearly calendar, monthly calendar, and activity list) with Download and Preview buttons. Currently, these buttons are broken:

1. **Download buttons** show loading spinner indefinitely but never trigger a download
2. **Preview buttons** open a new tab but may have similar hanging issues
3. **PDF styling** in admin exports lacks proper formatting compared to the working public calendar PDF

The public calendar PDF export (`PublicCalendarController::downloadPdf`) works correctly and serves as the reference implementation. The admin export pages (`ExportController`) have broken behavior that needs to be fixed.

**Impact:** Admin users cannot export calendars or activity lists, preventing distribution of official documents and reporting.

**Scope:** This bugfix addresses:
- Frontend button implementation (Livewire component and blade template)
- Backend response handling (ExportController methods)
- PDF styling consistency (ExportPdfService and view templates)

---

## Bug Analysis

### Current Behavior (Defect)

**Section 1: Button Functionality Issues**

1.1 WHEN the Download button is clicked on the yearly calendar export page (/activities/export with exportType='yearly') THEN the button shows a loading spinner indefinitely and no PDF download occurs

1.2 WHEN the Download button is clicked on the monthly calendar export page (/activities/export with exportType='monthly') THEN the button shows a loading spinner indefinitely and no PDF download occurs

1.3 WHEN the Download button is clicked on the activity list export page (/activities/export with exportType='list') THEN the button shows a loading spinner indefinitely and no PDF download occurs

1.4 WHEN the Preview button is clicked on any export page THEN it opens a new tab but may experience similar hanging issues as the download button

1.5 WHEN the blade template uses `<a href="{{ route(...) }}" target="_blank">` for download buttons THEN the browser navigation is triggered but the response handling may cause the page to hang

**Section 2: PDF Styling Issues**

1.6 WHEN the admin yearly calendar PDF is generated THEN the header table for "Perhitungan Hari Efektif" (effective days calculation) has no borders and no background colors

1.7 WHEN the admin yearly calendar PDF is generated THEN the calendar table headers (month names) are white text on white background, making them invisible

1.8 WHEN the admin yearly calendar PDF displays weekend cells THEN they are not colored red as expected

1.9 WHEN the admin yearly calendar PDF displays LIBNAS (national holidays) that fall on weekends THEN they are not colored red as expected

1.10 WHEN the ExportPdfService generates PDF using `exportYearly()` THEN the styling from the public calendar PDF template is not applied properly

---

### Expected Behavior (Correct)

**Section 2: Button Functionality**

2.1 WHEN the Download button is clicked on the yearly calendar export page THEN the system SHALL immediately trigger a PDF download without opening a new tab or hanging

2.2 WHEN the Download button is clicked on the monthly calendar export page THEN the system SHALL immediately trigger a PDF download without opening a new tab or hanging

2.3 WHEN the Download button is clicked on the activity list export page THEN the system SHALL immediately trigger a PDF download without opening a new tab or hanging

2.4 WHEN the Preview button is clicked on any export page THEN the system SHALL open the PDF in a new browser tab with inline disposition for immediate viewing

2.5 WHEN the blade template download button is implemented THEN the system SHALL use proper response headers (Content-Type: application/pdf, Content-Disposition: attachment) that trigger browser download behavior

2.6 WHEN the ExportController methods return PDF responses THEN the system SHALL set Content-Length header and appropriate cache headers to ensure proper download completion

**Section 3: PDF Styling**

2.7 WHEN the admin yearly calendar PDF is generated THEN the system SHALL apply the same paper size (F4 portrait: 215mm x 330mm, dpi: 150) as the public calendar PDF

2.8 WHEN the admin yearly calendar PDF displays the effective days calculation table THEN the system SHALL apply borders to all cells and background colors to header rows (matching public calendar styling)

2.9 WHEN the admin yearly calendar PDF displays calendar table headers THEN the system SHALL use blue background (#2563eb) with white text for month names (matching .month-header class)

2.10 WHEN the admin yearly calendar PDF displays weekend cells without activities THEN the system SHALL apply red background with 50% opacity (rgba(254, 226, 226, 0.5)) using the .weekend-empty class

2.11 WHEN the admin yearly calendar PDF displays weekend cells with LIBNAS activities THEN the system SHALL apply red background or activity color background (using .weekend-with-activity class logic)

2.12 WHEN the ExportPdfService generates yearly calendar PDF THEN the system SHALL use the same view template ('kaldik.pdf') and styling as PublicCalendarController to ensure consistency

---

### Unchanged Behavior (Regression Prevention)

**Section 3: Preservation of Working Features**

3.1 WHEN the public calendar PDF download is accessed via `/kaldik/download` THEN the system SHALL CONTINUE TO generate and download PDFs correctly with proper styling

3.2 WHEN the public calendar PDF preview is accessed via `/kaldik/download?preview=1` THEN the system SHALL CONTINUE TO open PDFs inline in a new tab correctly

3.3 WHEN the ExportController's excel export method is called THEN the system SHALL CONTINUE TO download Excel files correctly without being affected by PDF fixes

3.4 WHEN the admin export page displays the UI with tabs and filters THEN the system SHALL CONTINUE TO render the Livewire component interface correctly

3.5 WHEN users navigate between different export types (yearly, monthly, list, excel) using tabs THEN the system SHALL CONTINUE TO switch between forms correctly

3.6 WHEN the ExportPdfService generates monthly calendar or activity list PDFs THEN the system SHALL CONTINUE TO use appropriate paper sizes (A4 landscape for monthly, A4 portrait for list)

3.7 WHEN authentication middleware checks user permissions for export pages THEN the system SHALL CONTINUE TO enforce role-based access control correctly

3.8 WHEN the ActivityLog records export operations THEN the system SHALL CONTINUE TO log activities correctly (excluding preview mode)
