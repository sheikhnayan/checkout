#!/usr/bin/env python3
"""
Bot Testing Script - Tests if reCAPTCHA blocking is working
Run: python test_recaptcha_bot.py
"""

import requests
import time
import random
import string
from datetime import datetime

# Configuration
BASE_URL = "http://localhost"  # Change this to your domain
AFFILIATE_URL = f"{BASE_URL}/affiliate/apply"
ENTERTAINER_URL = f"{BASE_URL}/entertainer/apply"
STAFF_URL = f"{BASE_URL}/staff/apply"

# Test bot profiles
BOT_PROFILES = [
    {
        "name": f"Bot User {i}",
        "email": f"bot{i}@spambot.test",
        "phone": "555-0100",
        "password": "BotPass123!@#",
        "experience": "I am a bot testing your system"
    }
    for i in range(1, 6)  # 5 bot test submissions
]

def generate_random_email():
    """Generate random email for each submission"""
    random_str = ''.join(random.choices(string.ascii_lowercase + string.digits, k=8))
    return f"testbot_{random_str}@spam.test"

def submit_form(form_url, bot_data, form_type):
    """Submit registration form without reCAPTCHA token (simulating bot)"""

    session = requests.Session()
    session.headers.update({
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    })

    # Prepare form data
    form_data = {
        'name': bot_data['name'],
        'email': generate_random_email(),
        'phone': bot_data['phone'],
        'password': bot_data['password'],
        'password_confirmation': bot_data['password'],
        'recaptcha_token': '',  # NO TOKEN - simulating bot that can't get reCAPTCHA token
        'form_load_time': str(int(time.time())),
    }

    if 'experience' in bot_data:
        form_data['experience'] = bot_data['experience']

    print(f"\n{'='*70}")
    print(f"🤖 BOT TEST #{len(form_data)} - {form_type}")
    print(f"{'='*70}")
    print(f"URL: {form_url}")
    print(f"Email: {form_data['email']}")
    print(f"reCAPTCHA Token: {form_data['recaptcha_token'] or 'NONE (bot cannot get it)'}")
    print(f"Timestamp: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")

    try:
        response = session.post(form_url, data=form_data, allow_redirects=False, timeout=10)

        print(f"\nResponse Status: {response.status_code}")

        # Check if blocked
        if response.status_code == 302:  # Redirect (blocked)
            print("✅ BOT BLOCKED! (Redirect detected)")
            if 'Bot verification failed' in response.text or 'error' in response.headers.get('Location', ''):
                print("🛡️ Reason: reCAPTCHA verification failed")
            return True

        elif response.status_code == 200:
            if 'error' in response.text.lower():
                print("✅ BOT BLOCKED! (Error message in response)")
                # Check for specific errors
                if 'bot' in response.text.lower():
                    print("🛡️ Reason: Bot detection triggered")
                if 'recaptcha' in response.text.lower():
                    print("🛡️ Reason: reCAPTCHA failed")
                return True
            else:
                print("❌ BOT ALLOWED! (Registration might have gone through)")
                return False

        else:
            print(f"⚠️ Unexpected response: {response.status_code}")
            print(response.text[:500])
            return None

    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return None

def test_all_forms():
    """Test all three registration forms"""

    print("\n" + "="*70)
    print("🧪 reCAPTCHA BOT DETECTION TEST SUITE")
    print("="*70)
    print(f"Testing at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print(f"Total bots to test: {len(BOT_PROFILES)}")

    results = {
        'affiliate': {'blocked': 0, 'allowed': 0, 'errors': 0},
        'entertainer': {'blocked': 0, 'allowed': 0, 'errors': 0},
        'staff': {'blocked': 0, 'allowed': 0, 'errors': 0},
    }

    # Test Affiliate Registration
    print("\n\n" + "█"*70)
    print("TEST 1: AFFILIATE REGISTRATION FORMS")
    print("█"*70)
    for i, bot in enumerate(BOT_PROFILES, 1):
        result = submit_form(AFFILIATE_URL, bot, f"Affiliate #{i}")
        if result is True:
            results['affiliate']['blocked'] += 1
        elif result is False:
            results['affiliate']['allowed'] += 1
        else:
            results['affiliate']['errors'] += 1
        time.sleep(1)  # Rate limit

    # Test Entertainer Registration
    print("\n\n" + "█"*70)
    print("TEST 2: ENTERTAINER REGISTRATION FORMS")
    print("█"*70)
    for i, bot in enumerate(BOT_PROFILES, 1):
        result = submit_form(ENTERTAINER_URL, bot, f"Entertainer #{i}")
        if result is True:
            results['entertainer']['blocked'] += 1
        elif result is False:
            results['entertainer']['allowed'] += 1
        else:
            results['entertainer']['errors'] += 1
        time.sleep(1)

    # Test Staff Registration
    print("\n\n" + "█"*70)
    print("TEST 3: STAFF REGISTRATION FORMS")
    print("█"*70)
    for i, bot in enumerate(BOT_PROFILES, 1):
        result = submit_form(STAFF_URL, bot, f"Staff #{i}")
        if result is True:
            results['staff']['blocked'] += 1
        elif result is False:
            results['staff']['allowed'] += 1
        else:
            results['staff']['errors'] += 1
        time.sleep(1)

    # Print Summary
    print("\n\n" + "="*70)
    print("📊 TEST RESULTS SUMMARY")
    print("="*70)

    for form_type, counts in results.items():
        total = counts['blocked'] + counts['allowed'] + counts['errors']
        blocked_pct = (counts['blocked'] / total * 100) if total > 0 else 0

        status = "✅ PASS" if counts['blocked'] == total else "❌ FAIL"

        print(f"\n{form_type.upper()} Registration:")
        print(f"  {status}")
        print(f"  Blocked:  {counts['blocked']}/{total} ({blocked_pct:.0f}%)")
        print(f"  Allowed:  {counts['allowed']}/{total}")
        print(f"  Errors:   {counts['errors']}/{total}")

    print("\n" + "="*70)
    print("📝 NEXT STEPS:")
    print("="*70)
    print("1. Check your logs file: resources/logs/laravel.log")
    print("2. Look for entries like: 'reCAPTCHA verification' and 'blocked by reCAPTCHA'")
    print("3. Verify bot submissions are showing 'WARNING' level logs")
    print("4. Make sure no registrations appear in your admin panel")
    print("="*70 + "\n")

if __name__ == "__main__":
    try:
        test_all_forms()
    except KeyboardInterrupt:
        print("\n\n❌ Test interrupted by user")
    except Exception as e:
        print(f"\n\n❌ Fatal error: {str(e)}")
