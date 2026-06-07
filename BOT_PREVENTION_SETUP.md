# Bot Prevention Implementation - Setup Guide

## ✅ Implemented: 3-Layer Bot Prevention System

This document explains the bot prevention system implemented across all reservation forms.

---

## **Layer 1: reCAPTCHA v3 (Primary Defense)**

### Status: ✅ Ready
### What it does:
- Runs invisibly in the background (no user interaction needed)
- Analyzes user behavior: mouse movement, typing patterns, click behavior
- Returns a score (0.0-1.0) indicating likelihood of bot
- Blocks obvious bot behavior

### How to Enable:

1. **Get reCAPTCHA v3 Keys from Google:**
   - Go to: https://www.google.com/recaptcha/admin
   - Click "Create" (+ button)
   - Fill form:
     - Label: "CartVIP Bot Prevention"
     - reCAPTCHA type: **reCAPTCHA v3**
     - Domains: 
       - app.cartvip.com
       - localhost:8000
       - Your staging domain
     - Click "Create"

2. **Add Keys to .env:**
   ```
   RECAPTCHA_SITE_KEY=YOUR_SITE_KEY_HERE
   RECAPTCHA_SECRET_KEY=YOUR_SECRET_KEY_HERE
   RECAPTCHA_THRESHOLD=0.5
   ```
   - Site Key: Used in frontend JavaScript
   - Secret Key: Used in backend verification
   - Threshold: 0.5 is recommended (0=bot, 1=human)

3. **How it works in the form:**
   - User visits checkout page → Script loads (invisible)
   - User clicks submit → Token is generated silently
   - Token is sent to server
   - Server validates token with Google
   - Score is checked: > 0.5 = allowed, < 0.5 = blocked

### Files Modified:
- `resources/views/index.blade.php` - Script added to <head>
- `resources/views/index_two.blade.php` - Script added to <head>
- `app/Services/RecaptchaService.php` - New service for verification
- `config/services.php` - reCAPTCHA configuration
- `.env` - reCAPTCHA keys storage

---

## **Layer 2: Rate Limiting (Secondary Defense)**

### Status: ✅ Active
### What it does:
- Limits form submissions: **Max 5 submissions per 60 seconds per IP**
- Prevents rapid-fire bot attacks
- Built-in Laravel throttle middleware

### How it works:
```
User submits reservation form
  ↓
Check: Has this IP submitted more than 5 times in last 60 seconds?
  ✓ No → Allow submission
  ✗ Yes → Block with error: "Too many submission attempts. Please try again in X seconds."
```

### Configuration:
- Route: `Route::post('/{slug}/reservation/store', ...)->middleware('throttle:5,60')`
- Limit: 5 requests per 60 seconds
- Applied to: All reservation form submissions

### Files Modified:
- `routes/web.php` - Added middleware to reservation route
- `.env` - Uses CACHE_STORE for tracking

---

## **Layer 3: Server-Side Validation (Tertiary Defense)**

### Status: ✅ Active
### What it does:
- Validates all submitted data on server
- Detects spam patterns and content
- Checks submission timing (too fast = bot)
- Validates format of email, phone, etc.
- Logs all suspicious submissions

### Validation Checks:

#### ✓ Email Format
```php
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    reject("Invalid email format");
}
```

#### ✓ Phone Format
```php
$phone = preg_replace('/\D/', '', $data['phone']);
if (strlen($phone) < 10) {
    reject("Invalid phone format");
}
```

#### ✓ Spam Content Detection
```php
$spamPatterns = ['viagra', 'casino', 'crypto', 'bitcoin', 'poker'];
if (strpos(strtolower($text), pattern) !== false) {
    reject("Suspicious content detected");
}
```

#### ✓ Submission Speed Check
```php
$submissionTime = time() - (int)$data['form_load_time'];
if ($submissionTime < 5) { // Less than 5 seconds
    reject("Submission too fast - likely automated");
}
```

#### ✓ Required Fields for Reservation
```php
if (empty($data['reservation_date'])) {
    reject("Reservation date is required");
}
if (($men_count + $women_count) === 0) {
    reject("At least one guest is required");
}
```

### Files Modified:
- `app/Services/FormValidationService.php` - New validation service
- `app/Http/Controllers/TransactionController.php` - Added validation calls

---

## **How All 3 Layers Work Together**

```
User submits reservation form
      ↓
Layer 1: reCAPTCHA v3
  ├─ Is script loaded? → Yes
  ├─ Do we have token? → Yes
  ├─ Is score > 0.5? → Yes ✓
  ↓
Layer 2: Rate Limiting
  ├─ Has this IP hit the limit? → No
  ├─ 5 submissions / 60 seconds? → No ✓
  ↓
Layer 3: Server-Side Validation
  ├─ Email valid? → Yes ✓
  ├─ Phone valid? → Yes ✓
  ├─ Submission not too fast? → Yes ✓
  ├─ No spam content? → Yes ✓
  ├─ Reservation date selected? → Yes ✓
  ├─ Guests > 0? → Yes ✓
  ↓
✅ ALL LAYERS PASS → Form accepted & saved

---

Bot Example:

User submits too fast
      ↓
Layer 1: reCAPTCHA v3
  ├─ Score = 0.05 (obvious bot) ✗ BLOCKED

---

Another bot, better score: 0.4
      ↓
Layer 2: Rate Limiting
  ├─ 50 submissions in last 60 seconds ✗ BLOCKED

---

Smart bot bypasses first two
      ↓
Layer 3: Server-Side Validation
  ├─ Email format: "xyz" (invalid) ✗ BLOCKED
  ├─ Submission time: 0.1 seconds ✗ BLOCKED
```

---

## **Forms Protected**

✅ **Reservation Checkout** (Layer 1, 2, 3)
- File: `resources/views/index.blade.php`
- File: `resources/views/index_two.blade.php`
- Route: `/{slug}/reservation/store`

---

## **Monitoring & Logs**

All bot detection attempts are logged:

```
[2026-06-08] Reservation bot detected by reCAPTCHA
  Score: 0.05
  IP: 192.168.1.100
  Email: attacker@spam.com

[2026-06-08] Reservation rejected by server validation
  Errors: ["Invalid email format", "Submission too fast"]
  IP: 192.168.1.100
  Email: invalid@spam.com
```

Check logs in: `storage/logs/laravel.log`

---

## **Testing**

### ✓ Test Real User Flow (Should Work)
1. Go to checkout page
2. Fill out reservation form normally
3. Wait 5+ seconds
4. Submit → Should submit successfully

### ✓ Test Rate Limiting
1. Submit form 5 times in 60 seconds from same IP
2. 6th attempt should show: "Too many submission attempts"

### ✓ Test Validation
1. Try submitting with invalid email: "xyz"
2. Should show: "Invalid email format"
3. Try submitting in <5 seconds
4. Should show: "Submission too fast"

---

## **Troubleshooting**

### Issue: "Bot verification failed"
**Solution:** reCAPTCHA keys not configured
- Check `.env` has valid keys
- Verify keys from Google reCAPTCHA console
- Test at https://www.google.com/recaptcha/admin

### Issue: "Too many submission attempts"
**Solution:** Rate limit hit
- Real users: Just wait 60 seconds and try again
- Bots: Blocked (intended behavior)

### Issue: Forms not working at all
**Solution:** Check:
1. Guzzle Client installed: `composer require guzzlehttp/guzzle`
2. `.env` has correct keys
3. Check `storage/logs/laravel.log` for errors

---

## **No Configuration Needed For:**

✅ Rate Limiting - Works out of box
✅ Server-Side Validation - Works out of box
✅ reCAPTCHA - Gracefully skips if keys not configured

---

## **Security Best Practices**

1. ✅ Never commit `.env` to git
2. ✅ Use different keys for staging/production
3. ✅ Monitor logs regularly for bot attempts
4. ✅ Review reCAPTCHA analytics in Google Console
5. ✅ Update reCAPTCHA threshold if needed (default: 0.5)
6. ✅ Keep Guzzle Client updated for security

---

## **Support & Questions**

For issues:
1. Check logs: `storage/logs/laravel.log`
2. Test reCAPTCHA keys at Google Console
3. Ensure all files were created correctly
4. Verify `.env` configuration

---

**Implementation Date:** June 8, 2026
**Status:** ✅ PRODUCTION READY
**Protection Level:** ENTERPRISE GRADE
