# Aloware SMS Notifications Integration Guide

## Overview

Professional SMS notifications are now sent to customers after every reservation and package purchase. SMS includes comprehensive transaction details with club branding and links.

---

## Setup Instructions

### Step 1: Get Aloware API Key

1. **Visit:** https://app.aloware.io/
2. **Create/Login to account**
3. **Navigate to:** Settings → API Keys (or Developer/API section)
4. **Create new API key** for SMS sending
5. **Copy the API key**

### Step 2: Add Configuration to .env

Update your `.env` file:

```env
# Aloware SMS Configuration
ALOWARE_ENABLED=true
ALOWARE_API_KEY=your_actual_api_key_here
ALOWARE_API_URL=https://api.aloware.io/v1
```

**Fields:**
- `ALOWARE_ENABLED` - Set to `true` to activate SMS sending
- `ALOWARE_API_KEY` - Your API key from Aloware
- `ALOWARE_API_URL` - Aloware API endpoint (usually fixed)

### Step 3: Verify Configuration

Test that the service is properly configured:

```bash
php artisan tinker
>>> $sms = new \App\Services\AlowareSmsService();
>>> $sms->sendTest('+12125551234', 'Test SMS from CartVIP');
```

You should receive a test SMS if everything is configured correctly.

---

## How It Works

### When SMS is Sent

✅ **After successful RESERVATION** from checkout pages
- When user completes reservation booking
- Phone number: From `package_phone` field
- Type: "Reservation" 

✅ **After successful PACKAGE PURCHASE** from checkout pages  
- When user completes package purchase
- Phone number: From `package_phone` field
- Type: "Package"

### SMS Content

#### Reservation SMS Format

```
🎉 *RESERVATION CONFIRMED* 🎉

📍 *[CLUB NAME]*
Confirmation: #[CONFIRMATION ID]

📅 Reservation Date: [DATE]
👥 Guests: X Men + Y Women = Z Total
💰 Total: $[AMOUNT]

✅ Your reservation is confirmed and ready!
🔗 View Details: [CLUB LINK]
📞 Questions? Contact [CLUB NAME]

Thank you for your business! 🙏
```

#### Package SMS Format

```
🎊 *BOOKING CONFIRMED* 🎊

📍 *[CLUB NAME]*
Confirmation: #[CONFIRMATION ID]

📦 Package: [PACKAGE NAME]
Qty: [QUANTITY]
💰 Total: $[AMOUNT]

📅 Event Date: [DATE]

✅ Your booking is confirmed!
🔗 View Details: [CLUB LINK]
📞 Questions? Contact [CLUB NAME]

Thank you for your business! 🙏
```

### Data Included in SMS

#### Reservation Details
- ✅ Club name (prominent)
- ✅ Confirmation/Transaction ID
- ✅ Reservation date
- ✅ Guest breakdown (Men + Women = Total)
- ✅ Total amount
- ✅ Club link
- ✅ Professional formatting with emojis

#### Package Details
- ✅ Club name (prominent)
- ✅ Confirmation/Transaction ID
- ✅ Package name
- ✅ Quantity
- ✅ Event/Package date
- ✅ Total amount
- ✅ Club link
- ✅ Professional formatting with emojis

---

## Phone Number Handling

### Supported Formats

The system automatically handles various phone number formats:

✅ `+1 (212) 555-1234` - International with formatting
✅ `+12125551234` - International without formatting
✅ `2125551234` - US number without country code (auto-adds +1)
✅ `12125551234` - US number with leading 1 (auto-adds +)

### Phone Validation

Phone numbers are:
- ✅ Cleaned (all non-numeric chars except + removed)
- ✅ Validated for minimum length (10+ digits)
- ✅ Formatted with country code (+1 for US if missing)

---

## Testing SMS Integration

### Method 1: Manual Test via Tinker

```bash
php artisan tinker
>>> $sms = new \App\Services\AlowareSmsService();
>>> $sms->sendTest('+12125551234');
```

### Method 2: Test During Checkout

1. Go to checkout page
2. Fill out form with test phone number
3. Complete purchase
4. Check that SMS is sent (check logs if not received)

### Method 3: Check Logs

SMS attempts are logged in `storage/logs/laravel.log`:

```
[2024-06-08 14:32:10] SMS sent successfully
  Phone: +12125551234
  Type: reservation
  Status: sent

[2024-06-08 14:32:15] SMS notification failed
  Phone: +12125551234
  Error: Invalid phone format
```

---

## Troubleshooting

### Issue: SMS Not Sending

**Check 1: Is SMS enabled?**
```env
ALOWARE_ENABLED=true  # Must be true
```

**Check 2: Is API key valid?**
- Verify API key in `.env` matches Aloware dashboard
- Check key hasn't been revoked/regenerated

**Check 3: Is phone number valid?**
- Must be international format or US number
- Check `storage/logs/laravel.log` for format errors

**Check 4: Does customer have phone number?**
- Phone field must be filled in checkout form
- Field name: `package_phone`

---

### Issue: "Invalid API Key"

**Solution:**
1. Go to https://app.aloware.io/
2. Check API key in settings
3. Copy exact key to `.env`
4. Clear config cache: `php artisan config:clear`
5. Test again

---

### Issue: "Phone Number Invalid"

**Check:**
- Phone number has at least 10 digits
- Phone number is real (not test number like 555-1234)
- If international, includes country code: +[CC]XXXXXXXXXX

**Examples that work:**
- +12125551234 (US)
- +442071838750 (UK)
- +33123456789 (France)

**Examples that DON'T work:**
- 2125551234 (missing +1)
- +1 555-1234 (too short)
- 555-1234 (too short)

---

### Issue: "API Request Failed"

**Possible causes:**
1. Network connectivity issue
2. Aloware service temporarily down
3. Invalid API endpoint in `.env`

**Solution:**
- Check `ALOWARE_API_URL` in `.env`
- Should be: `https://api.aloware.io/v1`
- Test API connectivity separately

---

## SMS Features

### ✅ Supported

- Text messages with emoji and formatting
- International phone numbers
- Automatic phone number formatting
- Club name and link in SMS
- Transaction ID and confirmation number
- Error logging and monitoring
- Graceful failure (SMS fail doesn't block transaction)

### ⏳ Not Yet Implemented (Future)

- WhatsApp messages (if Aloware supports)
- SMS delivery receipts
- SMS read/click tracking
- Batch SMS sending
- SMS templates management

---

## Configuration Files

**Updated/Created files:**

1. **`app/Services/AlowareSmsService.php`** - SMS service
   - Handles API communication with Aloware
   - Formats SMS messages
   - Validates phone numbers
   - Logs all SMS attempts

2. **`config/services.php`** - Service configuration
   - Added Aloware config section

3. **`.env`** - Environment variables
   - Added ALOWARE_ENABLED
   - Added ALOWARE_API_KEY
   - Added ALOWARE_API_URL

4. **`app/Http/Controllers/TransactionController.php`** - Transaction handling
   - Added SMS sending after successful reservation
   - Added SMS sending after successful package purchase

---

## Monitoring

### View SMS Logs

```bash
# See all SMS attempts
tail -f storage/logs/laravel.log | grep "SMS\|Aloware"

# Count successful SMS sends
grep "SMS sent successfully" storage/logs/laravel.log | wc -l

# Count failed SMS sends
grep "SMS.*failed\|SMS.*error" storage/logs/laravel.log | wc -l
```

### Log Format

```
[timestamp] SMS sent successfully
  Phone: +12125551234
  Response: {...json response from Aloware...}

[timestamp] SMS notification failed
  Phone: +12125551234
  Error: [Error message]
```

---

## Best Practices

✅ **Always include phone number in checkout form**
- Use field name: `package_phone`
- Make it required field
- Add format hint: "(e.g., +1 (212) 555-1234)"

✅ **Monitor SMS logs regularly**
- Check for delivery failures
- Identify phone number format issues
- Track SMS sending patterns

✅ **Test with various phone formats**
- US numbers
- International numbers
- Different formatting styles

✅ **Keep API key secure**
- Never commit API key to git
- Only store in `.env` file
- Rotate key periodically

---

## Support

For issues with:

**SMS Service:**
- Check `storage/logs/laravel.log`
- Verify `.env` configuration
- Test via `php artisan tinker`

**Aloware API:**
- Visit https://app.aloware.io/
- Check API documentation
- Contact Aloware support

---

**Implementation Date:** June 8, 2026  
**Status:** ✅ ACTIVE  
**Test Coverage:** Reservation + Package transactions
