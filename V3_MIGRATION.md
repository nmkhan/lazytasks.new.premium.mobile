# Lazytasks Premium — v1 → v3 API Migration

**Date:** 2026-03-28
**Version:** 1.0.40 → 1.0.41

---

## Why This Was Done

The main LazyTasks plugin is migrating all REST API endpoints from v1/v2 to v3, with v1/v2 deprecation planned within 2-4 weeks. The premium plugin had all 5 of its endpoints hardcoded to the `lazytasks/api/v1` namespace — meaning it would break completely once v1 is removed.

Additionally, two existing namespace references were already broken due to the main plugin's earlier reorganization of classes into versioned subdirectories (`v1/`, `v2/`, `v3/`).

---

## What Was Done

### 1. Created v3 Controller

**New file:** `src/Controller/v3/Lazytask_Default_Controller.php`

- Extends the existing v1 controller (`LazytasksPremium\Controller\Lazytask_Default_Controller`)
- Adds a **self-contained `validate_token()` method** that decodes JWT tokens using `Firebase\JWT\JWT` directly — no dependency on any main plugin controller class
- Overrides **only `getQRCode()`** to use `validate_token()` instead of the broken `Lazytask_UserController::decode()` import
- All other 4 methods (`validQrCodeScanCheck`, `lazytask_license_validation`, `lazytask_license_delete`, `getLicenseKey`) are inherited unchanged from v1

**Pattern follows:** Performance addon's `Lazytask_Performance_DefaultController` — same self-contained JWT validation approach.

### 2. Created v3 Route File

**New file:** `src/Routes/Lazytask_Premium_Api_V3.php`

Registers all 5 endpoints under the `lazytasks/api/v3` namespace:

| Endpoint | Method | Controller | Notes |
|----------|--------|------------|-------|
| `/premium/qr-code` | GET | **v3 controller** | Fixed JWT validation |
| `/premium/valid-qr-code-scan-check` | GET | v1 controller (facade) | Unchanged logic |
| `/premium/license-validation` | POST | v1 controller (facade) | Unchanged logic |
| `/premium/license-delete` | POST | v1 controller (facade) | Unchanged logic |
| `/premium/get-license-key` | GET | v1 controller (facade) | Unchanged logic |

The "facade pattern" means v3 routes point to the v1 controller for endpoints that don't need changes — same approach used throughout the main plugin.

### 3. Registered v3 Routes in Admin Class

**Modified:** `admin/class-lazytasks-premium-admin.php`

Added v3 route registration alongside v1 in `lazytask_premium_admin_routes()`:
```php
(new \LazytasksPremium\Routes\Lazytask_Premium_Api())->admin_routes();      // v1 — kept
(new \LazytasksPremium\Routes\Lazytask_Premium_Api_V3())->admin_routes();   // v3 — added
```

### 4. Fixed Broken QR Helper Namespace

**Modified:** `admin/class-lazytasks-premium-admin.php` line 175

The daily license check cron (`lazytask_premium_license_check`) was calling:
```php
\Lazytask\Helper\Lazytask_Helper_QR_Code::lazytask_preview_app_qrcode_generator();
```

This class path no longer exists in the main plugin (reorganized to `\Lazytask\Helper\v1\...`). Fixed to use the premium plugin's own QR helper:
```php
\LazytasksPremium\Helper\Lazytask_Helper_QR_Code::lazytask_preview_app_qrcode_generator();
```

### 5. Updated Frontend Base URL

**Modified:** `admin/frontend/src/configs/app.config.js`

```js
// Before:
liveApiUrl: `${appLocalizerPremium?.apiUrl}/lazytasks/api/v1`,

// After:
liveApiUrl: `${appLocalizerPremium?.apiUrl}/lazytasks/api/v3`,
```

All frontend service files (`LicenseService.js`, `QRCodeService.js`) use relative paths like `/premium/qr-code`, so no other frontend changes were needed. Frontend was rebuilt with `npm run build`.

### 6. Version Bump

**Modified:** `lazytasks-premium.php`

- Plugin header `Version:` 1.0.40 → 1.0.41
- `LAZYTASKS_PREMIUM_VERSION` constant 1.0.40 → 1.0.41

---

## Files Changed

| Action | File |
|--------|------|
| CREATED | `src/Controller/v3/Lazytask_Default_Controller.php` |
| CREATED | `src/Routes/Lazytask_Premium_Api_V3.php` |
| MODIFIED | `admin/class-lazytasks-premium-admin.php` |
| MODIFIED | `admin/frontend/src/configs/app.config.js` |
| MODIFIED | `admin/frontend/build/index.js` (rebuilt) |
| MODIFIED | `admin/frontend/build/index.css` (rebuilt) |
| MODIFIED | `lazytasks-premium.php` |
| UNCHANGED | `src/Controller/Lazytask_Default_Controller.php` (v1) |
| UNCHANGED | `src/Routes/Lazytask_Premium_Api.php` (v1) |

---

## Pre-Existing Bugs Fixed

### Bug 1: Broken JWT Controller Import (v1 controller, line 5)
```php
use Lazytask\Controller\Lazytask_UserController;
```
This class no longer exists at that namespace path (moved to `Lazytask\Controller\v1\Lazytask_UserController`). The v1 controller file was **not modified** per versioning rules — instead, the v3 controller replaces this dependency entirely with self-contained JWT validation.

### Bug 2: Broken QR Helper Call (admin class, line 175)
```php
\Lazytask\Helper\Lazytask_Helper_QR_Code::lazytask_preview_app_qrcode_generator();
```
Same issue — class moved to `v1` subdirectory. Fixed by pointing to the premium plugin's own helper class instead.

---

## Backward Compatibility

- **v1 routes remain active** — both v1 and v3 endpoints are registered simultaneously
- **Mobile app** continues to work on v1 for `valid-qr-code-scan-check` until the mobile app is migrated to v3
- **WordPress admin frontend** now uses v3 exclusively (built into compiled JS)
- When v1 is eventually deprecated in the main plugin, the premium v1 routes will still function independently (they're registered by this plugin, not the main plugin)

---

## External Licensing Server — NOT Affected

The premium plugin makes outbound HTTP calls to the **external licensing server** at `https://live.appza.net`. These calls are **completely separate** from the v1 → v3 WordPress REST API migration and were **not changed**:

| Call | URL | Used By |
|------|-----|---------|
| License check | `https://live.appza.net/api/appza/v1/license/check` | Daily cron (`lazytask_premium_license_check`) + manual verify |
| License activate | `https://live.appza.net/api/appza/v1/license/activate` | First-time license activation |
| License deactivate | `https://live.appza.net/api/appza/v1/license/deactivate` | License delete from Settings |
| Lead store | `https://live.appza.net/api/appza/v1/lead/store/lazy_task` | Plugin activation (first install) |
| Firebase credentials | `https://live.appza.net/api/appza/v1/firebase/credential/lazy_task` | Plugin activation (first install) |
| Plugin update check | `https://live.appza.net/api/appza/v1/plugin/version-check` | WP plugin update checker |

The `/api/appza/v1/` in these URLs is the **licensing server's own API version** — it has nothing to do with the LazyTasks WordPress plugin's REST API versioning (`lazytasks/api/v1` vs `v3`). These external calls remain unchanged and will continue to work exactly as before.

---

## Verification Checklist

- [ ] Activate premium plugin — no PHP fatal errors
- [ ] Settings → License tab — verify/delete license works
- [ ] "Mobile App" button — QR code popover loads correctly
- [ ] Browser network tab — API calls go to `/lazytasks/api/v3/premium/*`
- [ ] Mobile app QR scan — `valid-qr-code-scan-check` still responds on v1
- [ ] Daily cron — QR code regeneration doesn't fatal (fixed namespace)
