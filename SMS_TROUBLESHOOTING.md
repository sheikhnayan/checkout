# SMS Integration Troubleshooting Guide

## Error: "Could not resolve host: api.aloware.io"

This error means your server **cannot reach the Aloware API** on the internet.

---

## Quick Diagnostics

### Test 1: Can Server Reach Google?
```bash
curl -I https://google.com
```

**Expected:** 200 OK response  
**If fails:** Server has no internet access

---

### Test 2: Can Server Resolve Domains?
```bash
nslookup api.aloware.io
# or
ping api.aloware.io
```

**Expected:** IP address returned  
**If fails:** DNS resolution problem

---

### Test 3: Can Server Reach Aloware Directly?
```bash
curl -v https://api.aloware.io/v1/send-message
```

**Expected:** Connection response  
**If fails:** Firewall blocking or domain unreachable

---

## Common Causes & Solutions

### Cause 1: Server Behind Firewall

**Symptoms:**
- ✗ curl to external URLs fails
- ✗ DNS resolves but can't connect
- ✗ Only internal sites work

**Solutions:**
1. **Contact Hosting Provider** (most common fix)
   - Ask them to whitelist: `api.aloware.io`
   - Ask them to open port 443 (HTTPS)
   - Ask them to check firewall rules

2. **Check Server Configuration**
   ```bash
   # Check if outbound HTTPS is blocked
   sudo iptables -L -n | grep 443
   
   # Check firewall status
   sudo ufw status
   ```

3. **Ask for Proxy Settings**
   - If behind corporate proxy, you need proxy configuration
   - May need to use proxy in Guzzle client

---

### Cause 2: DNS Resolution Failure

**Symptoms:**
- ✗ `Could not resolve host` error
- ✓ Can ping internal servers
- ✗ Cannot resolve external domains

**Solutions:**
1. **Check DNS Configuration**
   ```bash
   # On Linux, check:
   cat /etc/resolv.conf
   
   # Look for nameserver entries like:
   # nameserver 8.8.8.8
   # nameserver 1.1.1.1
   ```

2. **Try Different DNS**
   ```bash
   # Temporarily test with Google DNS
   echo "nameserver 8.8.8.8" | sudo tee /etc/resolv.conf
   nslookup api.aloware.io
   ```

3. **Contact Hosting Provider**
   - Ask them to check DNS configuration
   - Ask them to use public DNS (8.8.8.8, 1.1.1.1)

---

### Cause 3: No Internet Connectivity

**Symptoms:**
- ✗ Cannot reach ANY external site
- ✗ All curl requests timeout or fail
- ✗ ping fails to external IPs

**Solutions:**
1. **Check Network Interface**
   ```bash
   ifconfig  # or ip addr on newer systems
   ```

2. **Test Gateway**
   ```bash
   ping 8.8.8.8  # Google DNS
   traceroute google.com
   ```

3. **Contact Hosting Provider**
   - Tell them: "Server has no outbound internet access"
   - Ask them to enable internet connectivity
   - Ask them to check network configuration

---

## For Your Specific Case

Based on your error: `cURL error 6: Could not resolve host: api.aloware.io`

### Most Likely Issues (in order):

1. **Server is in restricted network** (70% likely)
   - Behind corporate firewall
   - Hosting provider blocks outbound connections
   - **Fix:** Contact hosting provider, ask to whitelist `api.aloware.io`

2. **DNS resolver not working** (20% likely)
   - nameserver configuration missing
   - DNS server unreachable
   - **Fix:** Check `/etc/resolv.conf`, try public DNS like `8.8.8.8`

3. **No internet at all** (10% likely)
   - Network interface down
   - Gateway not configured
   - **Fix:** Run `ifconfig` and `ping 8.8.8.8`

---

## Testing Script

Save this as `test_sms.php` and run: `php test_sms.php`

```php
<?php
echo "=== CartVIP SMS Connectivity Test ===\n\n";

// Test 1: DNS Resolution
echo "1. Testing DNS Resolution...\n";
$alowareIp = gethostbyname('api.aloware.io');
if ($alowareIp !== 'api.aloware.io') {
    echo "   ✓ DNS OK: api.aloware.io = $alowareIp\n";
} else {
    echo "   ✗ DNS FAILED: Cannot resolve api.aloware.io\n";
}

// Test 2: Can reach Google (general internet)
echo "\n2. Testing Internet Connectivity (Google)...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.google.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ✗ FAILED: $error\n";
} else {
    echo "   ✓ OK: Can reach google.com\n";
}

// Test 3: Can reach Aloware
echo "\n3. Testing Aloware API Connectivity...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.aloware.io/v1/send-message');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ✗ FAILED: $error\n";
} else {
    echo "   ✓ OK: Aloware is reachable (HTTP $httpCode)\n";
}

echo "\n=== Summary ===\n";
echo "If all tests pass: SMS should work!\n";
echo "If tests fail: Contact your hosting provider with the errors above.\n";
?>
```

---

## What to Tell Your Hosting Provider

```
Subject: Need to enable outbound HTTPS for SMS service

Body:
We need our server to reach an external API:
- Domain: api.aloware.io
- Protocol: HTTPS
- Port: 443

Current error:
"cURL error 6: Could not resolve host: api.aloware.io"

Can you please:
1. Whitelist api.aloware.io
2. Ensure port 443 (HTTPS) is open for outbound connections
3. Verify DNS is working properly
4. Check firewall rules

Thank you!
```

---

## Temporary Workaround

While you fix the network issue, SMS will:
- ✅ Not break checkout (transactions still work)
- ✅ Log the error for debugging
- ✅ Silently fail (user won't see errors)
- ❌ Not send SMS messages

Once network is fixed, just restart and SMS will work!

---

## Prevention for Future

- ✅ Always test external API connectivity before going live
- ✅ Monitor logs for SMS errors
- ✅ Have fallback (email works as backup)
- ✅ Keep network configuration documented

---

## Still Not Working?

1. Run the test script above
2. Share the output with hosting provider
3. Ask them specifically which URLs are blocked
4. Ask them to test: `curl https://api.aloware.io/v1/send-message`
