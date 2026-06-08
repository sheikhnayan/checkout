# Telnyx SMS Notifications Integration Guide

## Overview

Professional SMS notifications are now sent to customers after every reservation and package purchase using Telnyx API. SMS includes comprehensive transaction details with club branding and links.

This replaces the Aloware integration with Telnyx, which offers better reliability and more transparent pricing.

---

## Setup Instructions

### Step 1: Create Telnyx Account

1. **Visit:** https://telnyx.com/
2. **Sign up** for free account
3. **Complete phone verification**
4. **Create a messaging profile** (required for SMS)

---

### Step 2: Get Your Telnyx API Key

1. **Login to Telnyx Dashboard:** https://portal.telnyx.com/
2. **Go to:** Account → Authentication
3. **Copy your API Key** (Bearer token format)
4. **Keep it secure!**

**API Documentation:**
- https://developers.telnyx.com/development/api-fundamentals/authentication
- https://developers.telnyx.com/docs/api/messaging/overview

---

### Step 3: Get Your Telnyx Phone Number

1. **In Telnyx Dashboard:** Phone Numbers → Numbers
2. **Verify or purchase a phone number**
3. **Assign to messaging profile**
4. **Copy the phone number** in E.164 format (e.g., `+15551234567`)

**Format is Critical!**
- Must be: `+[country code][number]` with NO spaces, dashes, or parentheses
- Examples:
  - US: `+15551234567`
  - UK: `+442071838750`
  - France: `+33123456789`
  - Australia: `+61212345678`

---

### Step 4: Configure .env

Update your `.env` file with Telnyx credentials:

```env
# Telnyx SMS Configuration
TELNYX_ENABLED=true
TELNYX_API_KEY=your_api_key_here
TELNYX_FROM_NUMBER=+15551234567
TELNYX_DEFAULT_COUNTRY_CODE=1
```

**Fields Explained:**
- `TELNYX_ENABLED` - Set to `true` to activate SMS sending
- `TELNYX_API_KEY` - Your API key from Telnyx dashboard
- `TELNYX_FROM_NUMBER` - Your Telnyx phone number (E.164 format, no spaces)
- `TELNYX_DEFAULT_COUNTRY_CODE` - Default country code for phone numbers without prefix

**Country Code Examples:**
- `1` = USA/Canada
- `44` = UK
- `33` = France
- `52` = Mexico
- `61` = Australia
- `971` = UAE

---

### Step 5: Clear Config Cache

```bash
php artisan config:clear
```

---

### Step 6: Test SMS

```bash
php artisan tinker
>>> $sms = new \App\Services\TelnyxSmsService();
>>> $sms->sendTest('+15551234567');  # Your real phone number
```

You should receive a test SMS within seconds!

---

## How It Works

### When SMS is Sent

✅ **After successful RESERVATION** from all checkout pages
- Sent to: `package_phone` field from reservation form
- Content: Reservation details (date, guests, total, club link)
- Format: Professional SMS message, max 1,600 characters

✅ **After successful PACKAGE PURCHASE** from all checkout pages
- Sent to: `package_phone` field from package form
- Content: Package details (name, quantity, date, total, club link)
- Format: Professional SMS message, max 1,600 characters

### SMS Content

#### Reservation SMS Example

```
RESERVATION CONFIRMED

Club: Vegas Hustler Club
Confirmation: #TRX-20260608-12345
Date: June 15, 2026
Guests: 3 Men + 2 Women = 5 Total
Total: $450.00

Your reservation is confirmed!
View Details: https://app.cartvip.com/vegashustlerclub
Questions? Contact Vegas Hustler Club
```

#### Package SMS Example

```
BOOKING CONFIRMED

Club: Vegas Hustler Club
Confirmation: #TRX-20260608-67890
Package: VIP Table Service
Quantity: 1
Total: $500.00

Event Date: June 20, 2026

Your booking is confirmed!
View Details: https://app.cartvip.com/vegashustlerclub
Questions? Contact Vegas Hustler Club
```

### Data Included in SMS

✅ Club name (prominent)
✅ Confirmation/Transaction ID
✅ Complete transaction details
✅ Club website link
✅ Professional formatting
✅ No emojis (pure text for reliability)

---

## Phone Number Handling

### Supported Input Formats

Phone number formatter automatically handles any format:

✅ `(555) 123-4567` - Formatted US number
✅ `555-123-4567` - Dashed US number
✅ `5551234567` - Raw US number
✅ `+1 (555) 123-4567` - International with formatting
✅ `+15551234567` - International clean
✅ `+44 20 7183 8750` - UK number with spaces

### How Formatting Works

1. **Removes all non-numeric characters** (except leading +)
2. **Detects if country code is present** (starts with +)
3. **If no country code:** Adds default from `TELNYX_DEFAULT_COUNTRY_CODE`
4. **Validates E.164 format** before sending to Telnyx
5. **Sends in proper format:** `+[country][number]`

**Example Processing:**
```
Input: (212) 555-1234
Strip: 2125551234
Check: No + found
Add: Default country code (1) → +12125551234
Send: ✅ +12125551234
```

---

## Testing SMS Integration

### Method 1: Manual Test via Tinker

```bash
php artisan tinker
>>> $sms = new \App\Services\TelnyxSmsService();
>>> $sms->sendTest('+15551234567');
```

Expected response: `Test message from CartVIP SMS Service. If you received this, SMS notifications are working!`

### Method 2: Test During Checkout

1. Go to any checkout page (general, entertainer, or affiliate)
2. Fill out form with test phone number
3. Complete reservation or package purchase
4. SMS should arrive within 1-3 seconds
5. Check `storage/logs/laravel.log` for details

### Method 3: Check Logs

SMS attempts are logged in `storage/logs/laravel.log`:

```bash
# See all SMS attempts
tail -f storage/logs/laravel.log | grep "SMS\|Telnyx"

# Count successful SMS sends
grep "SMS sent successfully" storage/logs/laravel.log | wc -l

# Count failed SMS sends
grep "SMS\|Telnyx.*error\|failed" storage/logs/laravel.log | wc -l
```

**Log Examples:**

```
[2026-06-08 14:32:10] local.INFO: SMS sent successfully via Telnyx 
  Phone: +15551234567
  From: +15551234567
  Message ID: 12345678-1234-1234-1234-123456789012
  Status: queued

[2026-06-08 14:32:15] local.ERROR: Telnyx API client error
  Phone: +15551234567
  Status: 401
  Error Code: 40100
  Error Detail: "Invalid API Key"
```

---

## Troubleshooting

### Issue: "Invalid API Key" (401 error)

**Cause:** API key is wrong or expired

**Solutions:**
1. Go to https://portal.telnyx.com/ → Account → Authentication
2. Copy the exact API key
3. Update `.env`: `TELNYX_API_KEY=your_key_here`
4. Clear config cache: `php artisan config:clear`
5. Test again

---

### Issue: "Number not assigned to messaging profile" (403 error)

**Cause:** Phone number not properly configured

**Solutions:**
1. Go to Telnyx Dashboard → Phone Numbers
2. Select your phone number
3. Under Messaging: Assign to a messaging profile
4. Make sure profile is **active**
5. Copy phone number in E.164 format: `+XXXXXXXXXXX`
6. Update `.env`: `TELNYX_FROM_NUMBER=+your_number`
7. Clear config cache: `php artisan config:clear`
8. Test again

---

### Issue: "Invalid phone number" (422 error)

**Cause:** Phone number format is wrong

**Solutions:**
- Recipient phone number must be valid and international
- Must be in E.164 format: `+[country code][number]`
- Examples:
  - ✅ `+15551234567` (US)
  - ✅ `+442071838750` (UK)
  - ❌ `5551234567` (missing country code)
  - ❌ `(555) 123-4567` (has special characters - should auto-format)

---

### Issue: SMS Not Arriving

**Check 1: Is SMS enabled?**
```env
TELNYX_ENABLED=true  # Must be true
```

**Check 2: Is API key valid?**
- Test with tinker: `$sms->sendTest('+15551234567')`
- Check logs for error messages

**Check 3: Is phone number valid?**
- Recipient number must be real, not test number
- Must have at least 7 digits after country code
- Must be in format: `+[country code][number]`

**Check 4: Does customer have phone number?**
- Phone field must be filled in checkout form
- Field name: `package_phone` or `reservation_phone`

**Check 5: Network Connectivity**
- Test: `curl https://api.telnyx.com/v2/messages`
- If fails: Contact hosting provider to whitelist `api.telnyx.com`

---

### Issue: "Network error: Cannot reach SMS service"

**Cause:** Server cannot reach Telnyx API

**Solutions:**
1. Test connectivity: `curl -I https://api.telnyx.com`
2. If it fails: Contact hosting provider
3. Ask them to:
   - Whitelist `api.telnyx.com`
   - Open port 443 (HTTPS) for outbound
   - Check firewall rules
   - Verify DNS resolution

---

### Issue: "Telnyx service temporarily unavailable" (5xx error)

**Cause:** Telnyx servers having issues (rare)

**Solution:**
- This is automatic and retryable
- SMS will be queued for retry
- Usually resolves within minutes
- Check Telnyx status: https://status.telnyx.com/

---

## Configuration Files

**Updated/Created files:**

1. **`app/Services/TelnyxSmsService.php`** - NEW
   - Telnyx API integration
   - Phone number formatting (E.164)
   - Message composition
   - Error handling with specific Telnyx codes
   - Test capability

2. **`config/services.php`** - UPDATED
   - Added Telnyx configuration section
   - API key, URL, phone number, country code

3. **`.env`** - UPDATED
   - Added TELNYX_* variables
   - Kept ALOWARE_* for backward compatibility (disabled)

4. **`app/Http/Controllers/TransactionController.php`** - UPDATED
   - Changed AlowareSmsService to TelnyxSmsService
   - SMS sends after successful reservation
   - SMS sends after successful package purchase

---

## Monitoring & Analytics

### View SMS Logs

```bash
# Real-time SMS monitoring
tail -f storage/logs/laravel.log | grep -E "SMS|Telnyx"

# Count by status
grep "SMS sent successfully" storage/logs/laravel.log | wc -l    # Sent
grep "SMS.*error\|failed" storage/logs/laravel.log | wc -l       # Failed
grep "Network error" storage/logs/laravel.log | wc -l            # Network issues
```

### Check Message Status

Telnyx starts messages in `queued` status, then updates to:
- `sent` - SMS queued with carrier
- `delivered` - Delivered to phone
- `delivery_failed` - Bounced/failed
- `undelivered` - No delivery confirmation

Status updates are logged when received from Telnyx.

---

## Best Practices

✅ **Always include phone number in checkout form**
- Field name: `package_phone`
- Field name: `reservation_phone`
- Both are auto-formatted before sending

✅ **Use E.164 format for your Telnyx phone number**
- Format: `+[country code][number]`
- No spaces, dashes, or parentheses
- Example: `+15551234567`

✅ **Monitor SMS logs regularly**
- Check `storage/logs/laravel.log`
- Watch for API errors (401, 403, 422)
- Verify phone number format issues
- Track sending patterns

✅ **Test before going live**
- Send test SMS with `$sms->sendTest()`
- Test with various phone number formats
- Test on real checkout pages
- Verify logs show successful sends

✅ **Keep API key secure**
- Never commit to git
- Only store in `.env`
- Rotate key periodically (monthly recommended)
- Use different keys for dev/prod

✅ **Set appropriate country code**
- Match your primary market
- Default is US (1)
- Change if mostly international users
- Users can still enter any country code

---

## API Reference

### Telnyx Messaging API

**Endpoint:** `POST https://api.telnyx.com/v2/messages`

**Authentication:** Bearer token in `Authorization` header

**Required Fields:**
- `from` - Sender phone in E.164 format
- `to` - Recipient phone in E.164 format
- `text` - Message body (max 1,600 characters)

**Response (201 Created):**
```json
{
  "data": {
    "id": "message_id",
    "status": "queued",
    "type": "sms",
    "cost": "0.0075",
    "parts": 1
  }
}
```

**Error Response (4xx/5xx):**
```json
{
  "errors": [
    {
      "code": 40300,
      "title": "Unprocessable Entity",
      "detail": "Number not assigned to messaging profile"
    }
  ]
}
```

**Common Error Codes:**
- `401` - Invalid API key
- `403` - Number not assigned to messaging profile
- `422` - Invalid phone number format or too long message
- `429` - Rate limited (retry after specified time)
- `500-503` - Telnyx server error (retry recommended)

**Full Documentation:**
https://developers.telnyx.com/docs/api/messaging/overview

---

## Comparison: Telnyx vs Aloware

| Feature | Telnyx | Aloware |
|---------|--------|---------|
| **API Type** | REST API | REST API |
| **Authentication** | Bearer Token | API Token in body |
| **Endpoint** | `api.telnyx.com` | `app.aloware.io` |
| **Phone Format** | E.164 required | Flexible |
| **Cost** | Transparent per-SMS | Variable |
| **Setup** | Simple | Moderate |
| **Documentation** | Excellent | Good |
| **Status** | ✅ Active | ⚠️ Deprecated |

---

## Support

**Telnyx Support:**
- https://developers.telnyx.com/support
- API Docs: https://developers.telnyx.com/docs/api
- Status: https://status.telnyx.com/

**SMS Integration Issues:**
- Check `storage/logs/laravel.log`
- Verify `.env` configuration
- Test via `php artisan tinker`
- Check Telnyx dashboard for phone number status

---

## Migration from Aloware

The old Aloware configuration is kept in `.env` but disabled (`ALOWARE_ENABLED=false`). 

**To revert to Aloware:**
1. Set `ALOWARE_ENABLED=true` in `.env`
2. Update `TransactionController.php` to use `AlowareSmsService`
3. Clear config cache: `php artisan config:clear`

**To fully remove Aloware:**
1. Delete `app/Services/AlowareSmsService.php`
2. Remove Aloware config from `config/services.php`
3. Remove Aloware variables from `.env`

---

**Implementation Date:** June 8, 2026  
**Status:** ✅ ACTIVE  
**Test Coverage:** Reservation + Package transactions (all checkout pages)  
**Backup Plan:** Can revert to Aloware by changing service class

