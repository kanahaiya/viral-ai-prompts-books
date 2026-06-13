# Enable cURL on Hostinger Shared Hosting

The payment gateways (Razorpay, Cashfree, PayPal) require the PHP cURL extension to be enabled on the server. If cURL is disabled, you'll see the error:

> **"Server payment module is unavailable (cURL disabled). Enable cURL in hosting PHP settings."**

## Steps to Enable cURL on Hostinger

### 1) Log in to Hostinger hPanel

1. Go to [hPanel.hostinger.com](https://hpanel.hostinger.com)
2. Log in with your Hostinger account credentials
3. Navigate to **Websites** → click on your domain (`aipromptbooks.in`)

### 2) Access PHP Configuration

1. In the left sidebar, click **Settings** (or similar option depending on current UI)
2. Look for **PHP Settings**, **PHP Configuration**, or **Advanced Settings**
3. Find the list of enabled/disabled PHP extensions

### 3) Enable cURL

1. Look for `curl` in the extension list
2. If it's listed as **disabled**, click to enable it
3. Save/Apply changes (usually an "Apply" or "Save" button)
4. The change may take a few seconds to 1 minute to propagate

### 4) Verify cURL is Enabled

After enabling, verify by creating a temporary test file:

Create a file named `test-curl.php` in your web root:

```php
<?php
if (function_exists('curl_init')) {
    echo "✓ cURL is ENABLED";
} else {
    echo "✗ cURL is DISABLED";
}
?>
```

Then visit: `https://www.aipromptbooks.in/test-curl.php`

If you see "✓ cURL is ENABLED", cURL is working. **Delete this test file after confirming.**

### 5) Retry Payment Processing

Once cURL is enabled:

1. Try creating an order via the checkout flow
2. The payment gateway (Razorpay) should now initialize successfully
3. Complete a test payment to verify everything is working

---

## If You Can't Find the Setting

If Hostinger's UI doesn't show an obvious cURL toggle:

1. **Contact Hostinger Support**:
   - Email: support@hostinger.com
   - Chat: Available in hPanel
   - Request: "Please enable the PHP cURL extension for my hosting account"

2. **Reference your domain**: `aipromptbooks.in`

Hostinger typically responds within hours and will enable cURL for you.

---

## Why cURL is Required

- **Razorpay**: Uses cURL to securely communicate with Razorpay's API to create orders and verify payments
- **Cashfree**: Uses cURL to create payment sessions
- **PayPal**: Uses cURL for payment capture and verification

Without cURL, the server cannot make HTTPS API calls to these payment gateways.
