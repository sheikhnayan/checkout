# W-9 Form System Implementation

## Overview
Complete W-9 form collection system using the actual IRS fillable PDF form.

## Features

### 1. User Flow
- Promoters/Entertainers register
- Automatic email sent with secure W-9 form link
- User opens form with secure token
- Form displays actual IRS W-9 PDF in sidebar for reference
- User fills HTML form fields + uploads government ID
- Form data fills actual IRS PDF form fields on server
- Completed PDF stored in database

### 2. Form Components
- **HTML Form**: Clean interface matching IRS PDF structure
- **IRS PDF**: Official W-9 fillable PDF used as template
- **ID Upload**: Government-issued ID (front & back) verification
- **Certification**: Legal certification with penalty warnings

### 3. Admin Interface
- View W-9 submission details in modal
- See government ID images
- Track submission status
- Download completed PDF with all data filled in

### 4. PDF Generation
- Uses actual IRS W-9 PDF template
- FPDI library fills form field positions with user data
- Proper formatting for SSN (XXX-XX-XXXX) and EIN (XX-XXXXXXX)
- Checkboxes marked with ✓
- Page 2 includes verification details
- Clean, professional output

## Files Modified/Created

### Controllers
- `app/Http/Controllers/W9FormController.php` - Form display, submission, PDF generation

### Views
- `resources/views/w9/form-real-pdf.blade.php` - Main form with embedded IRS PDF
- `resources/views/w9/admin-modal.blade.php` - Admin review modal with download button

### Routes
- `GET /w9/{token}` - Display form
- `POST /w9/{token}/submit` - Submit form data
- `GET /admin/w9/{id}/modal` - View modal
- `GET /admin/w9/{id}/download-pdf` - Download filled PDF

### Database
- `database/migrations/2026_06_04_012303_create_w9_forms_table.php`
- Stores all form data, file paths, and status information

### Email
- `app/Mail/W9FormLink.php` - Email notification
- `resources/views/emails/w9-form-link.blade.php` - Professional email template

### PDF Template
- `storage/app/public/w9-template/fw9_template.pdf` - Official IRS W-9 (downloaded)

## Form Fields Mapped to IRS PDF

**Page 1 - Part I & II:**
- Line 1: Name of entity/individual ✓
- Line 2: Business name/disregarded entity name ✓
- Line 3a: Tax classification (checkbox) ✓
- Line 3b: Foreign partners checkbox
- Line 4: Exempt payee code ✓
- Line 4: FATCA exemption code ✓
- Line 5: Street address ✓
- Line 6: City, state, ZIP code ✓
- Line 7: Account numbers ✓
- Part I: SSN/EIN with proper formatting ✓
- Part II: Certification signature area ✓

**Page 2 - Additional:**
- Government ID verification information
- Submission details and timestamp
- IP address for audit trail

## Government ID Verification
- File type validation (JPG, PNG only)
- File size limit (5 MB each)
- Front and back images required
- Stored in: `storage/app/public/w9-documents/id-{front,back}/`
- Displayed in admin modal with zoom capability

## Technical Stack
- Laravel 12
- FPDI (PDF manipulation)
- DomPDF (backup PDF generation)
- phpwkhtmltopdf (additional PDF options)
- jQuery (form handling)
- Bootstrap (admin UI)

## Security Features
- Token-based secure access (base64 encoded JSON)
- Admin-only PDF download access
- IP address logging for certification
- File upload validation
- Database validation rules

## Status Tracking
- pending: Initial record created
- submitted: User has filled and submitted form
- approved: Admin has approved the submission
- rejected: Admin rejected with notes

## Testing Checklist
- [ ] User receives email after registration
- [ ] Secure token link works
- [ ] Form displays with embedded IRS PDF
- [ ] File uploads work with validation
- [ ] Form submission saves all data
- [ ] PDF is generated correctly with filled fields
- [ ] Admin can view W-9 modal
- [ ] Admin can download filled PDF
- [ ] PDF file includes all user data
- [ ] Admin notes can be added
- [ ] Status can be changed (approved/rejected)

## Next Steps (If Needed)
1. Test with actual user registration
2. Verify PDF field coordinates match IRS form exactly
3. Add email notifications for admin on new submissions
4. Create admin approval workflow
5. Add bulk export of approved W-9s
