# Content Security Policy (CSP) Issues

## Overview
The application is experiencing multiple **Content Security Policy (CSP)** violations that are blocking inline styles, scripts, and external resources. The CSP header is defined in `.htaccess` file to improve security, but the current implementation has violations that need to be fixed.

**CSP Header Location:** [.htaccess](.htaccess)

---

## Current CSP Policy

```
Content-Security-Policy: Applying inline style violates the following Content Security Policy directive 'style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com'. Either the 'unsafe-inline' keyword, a hash ('sha256-xDGFBb780W7LmpHqRQPnfksTiNmcOzvbhNmKqGFik0w='), or a nonce ('nonce-...') is required to enable inline execution. Note that hashes do not apply to event handlers, style attributes and javascript: navigations unless the 'unsafe-hashes' keyword is present. The action has been blocked.
2:40 Applying inline style violates the following Content Security Policy directive 'style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com'. Either the 'unsafe-inline' keyword, a hash ('sha256-Oa+9u6taoh1mtaDV6MtfAFAYfiJszwlJNdsiRcgAC8o='), or a nonce ('nonce-...') is required to enable inline execution. Note that hashes do not apply to event handlers, style attributes and javascript: navigations unless the 'unsafe-hashes' keyword is present. The action has been blocked.
2:49 Applying inline style violates the following Content Security Policy directive 'style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com'. Either the 'unsafe-inline' keyword, a hash ('sha256-SwtdYwKqKN6rAwyawXpYJ5WNcxKgonOOvVS3xHbXLQ8='), or a nonce ('nonce-...') is required to enable inline execution. Note that hashes do not apply to event handlers, style attributes and javascript: navigations unless the 'unsafe-hashes' keyword is present. The action has been blocked.
2:57 Applying inline style violates the following Content Security Policy directive 'style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com'. Either the 'unsafe-inline' keyword, a hash ('sha256-6LdgKyrvSU70xj2ETGcezvzhl7OQrHsYFBSHwUzWKkA='), or a nonce ('nonce-...') is required to enable inline execution. The action has been blocked.
2:101 Applying inline style violates the following Content Security Policy directive 'style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com'. Either the 'unsafe-inline' keyword, a hash ('sha256-jHpRmKa1qTRY7sLJhZIzCkjc+6YXVKT0RTscQXoZJ2w='), or a nonce ('nonce-...') is required to enable inline execution. Note that hashes do not apply to event handlers, style attributes and javascript: navigations unless the 'unsafe-hashes' keyword is present. The action has been blocked.
2:116 Executing inline script violates the following Content Security Policy directive 'script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com https://code.iconify.design https://unpkg.com'. Either the 'unsafe-inline' keyword, a hash ('sha256-H/Yp2AAN2nE5LcJh5aS5NRt2DPD7KSu/0zKdZMVM+Co='), or a nonce ('nonce-...') is required to enable inline execution. The action has been blocked.
2:1 Failed to find a valid digest in the 'integrity' attribute for resource 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js' with computed SHA-384 integrity 'geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz'. The resource has been blocked.
Connecting to 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css.map' violates the following Content Security Policy directive: "connect-src 'self'". The request has been blocked.

iconify.min.js:12 Applying inline style violates the following Content Security Policy directive 'style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com'. Either the 'unsafe-inline' keyword, a hash ('sha256-Oa+9u6taoh1mtaDV6MtfAFAYfiJszwlJNdsiRcgAC8o='), or a nonce ('nonce-...') is required to enable inline execution. Note that hashes do not apply to event handlers, style attributes and javascript: navigations unless the 'unsafe-hashes' keyword is present. The action has been blocked.
Ze @ iconify.min.js:12
c @ iconify.min.js:12
(anonymous) @ iconify.min.js:12
(anonymous) @ iconify.min.js:12
ln @ iconify.min.js:12
(anonymous) @ iconify.min.js:12
setTimeout
(anonymous) @ iconify.min.js:12
(anonymous) @ iconify.min.js:12
2:1 Failed to find a valid digest in the 'integrity' attribute for resource 'https://code.iconify.design/3/3.1.0/iconify.min.js' with computed SHA-384 integrity 'GYcZF/Xz4/6ZHVch5eVcYcyWmSCvO3+ffsxF+B9hfRyc3XCkSws7SO5ZSGqHlUNH'. The resource has been blocked.
2:1 Failed to find a valid digest in the 'integrity' attribute for resource 'https://cdn.jsdelivr.net/npm/chart.js' with computed SHA-384 integrity 'jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ'. The resource has been blocked.
2:1 Failed to find a valid digest in the 'integrity' attribute for resource 'https://unpkg.com/swup@4' with computed SHA-384 integrity 'yzkU2LzN4yZh/Abp/DRUsN1AanVM6aQ8FPHmTpWgu6mzZiXf7L7I3jAWZ00h7fys'. The resource has been blocked.
2:289 Executing inline script violates the following Content Security Policy directive 'script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com https://code.iconify.design https://unpkg.com'. Either the 'unsafe-inline' keyword, a hash ('sha256-f1uL5NPOH3u+6ZiM7DuKIKTTvKxWhoqjEKin7f3vvbc='), or a nonce ('nonce-...') is required to enable inline execution. The action has been blocked.
Connecting to 'https://cdn.jsdelivr.net/npm/chart.umd.min.js.map' violates the following Content Security Policy directive: "connect-src 'self'". The request has been blocked.

Connecting to 'https://unpkg.com/Swup.umd.js.map' violates the following Content Security Policy directive: "connect-src 'self'". The request has been blocked.

  default-src 'self'; 
  script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com https://code.iconify.design https://unpkg.com; 
  style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com; 
  font-src 'self' https://fonts.gstatic.com; 
  img-src 'self' data:; 
  connect-src 'self'; 
  frame-src 'none'; 
  base-uri 'self'; 
  form-action 'self'
```

---

## Issues to Fix

### Issue 1: Inline Styles Violations
**Severity:** HIGH  
**Files Affected:**
- [application/views/templates/header.php](application/views/templates/header.php#L29)
- [application/views/templates/header.php](application/views/templates/header.php#L43)
- [application/views/templates/header.php](application/views/templates/header.php#L52)
- [application/views/templates/admin_header.php](application/views/templates/admin_header.php#L215)
- [application/views/templates/admin_header.php](application/views/templates/admin_header.php#L217)

**Error:**
```
Applying inline style violates the following Content Security Policy directive 'style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com'
```

**Root Cause:** Inline `style=""` attributes are blocked by CSP. Only external CSS files are allowed.

**Solution (Choose one):**
1. **Recommended:** Move inline styles to `assets/css/style.css`
2. Alternative: Add `'unsafe-inline'` to `style-src` (NOT recommended for production)

**Tasks:**
- [ ] **Task 1.1** - Identify all inline styles in header.php (lines 29, 43, 52)
- [ ] **Task 1.2** - Create CSS classes in `assets/css/style.css` for:
  - `.navbar-sticky` for `z-index: 1020;`
  - `.icon-logout` for `width: 20px; height: 20px;`
  - `.loader-overlay` for the big inline style object
- [ ] **Task 1.3** - Replace inline styles in header.php with class names
- [ ] **Task 1.4** - Repeat for admin_header.php (lines 215-217)
- [ ] **Task 1.5** - Test in browser and verify no console errors

**Example Fix:**
```php
// BEFORE (header.php line 29)
<nav class="navbar navbar-light bg-white border-bottom border-black border-2 px-3 sticky-top"
    style="z-index: 1020;">

// AFTER
<nav class="navbar navbar-light bg-white border-bottom border-black border-2 px-3 sticky-top navbar-sticky">

// In assets/css/style.css
.navbar-sticky {
    z-index: 1020;
}
```

---

### Issue 2: Inline Scripts Violations
**Severity:** HIGH  
**Files Affected:**
- [application/views/templates/main_footer.php](application/views/templates/main_footer.php#L69)
- [application/views/templates/admin_footer.php](application/views/templates/admin_footer.php#L25)

**Error:**
```
Executing inline script violates the following Content Security Policy directive 'script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com https://code.iconify.design https://unpkg.com'
```

**Root Cause:** Inline `<script>` tags are blocked by CSP. Only external script files are allowed.

**Solution:** Move inline scripts to external JavaScript files

**Tasks:**
- [ ] **Task 2.1** - Extract inline scripts from main_footer.php (starting at line 69)
- [ ] **Task 2.2** - Create `assets/js/page-init.js` with Swup initialization code
- [ ] **Task 2.3** - Extract inline scripts from admin_footer.php (starting at line 25)
- [ ] **Task 2.4** - Create `assets/js/admin-init.js` with admin-specific initialization
- [ ] **Task 2.5** - Reference these scripts in footer files instead of inline code
- [ ] **Task 2.6** - Test page transitions and ensure Swup works correctly

**Example Fix:**
```php
// BEFORE (main_footer.php)
<script>
    const swup = new Swup();
    // ... more code ...
</script>

// AFTER (main_footer.php)
<script src="<?= base_url('assets/js/page-init.js'); ?>"></script>

// NEW FILE: assets/js/page-init.js
const swup = new Swup();
// ... rest of code ...
```

---

### Issue 3: Integrity Attribute Mismatches
**Severity:** HIGH  
**Files Affected:**
- [application/views/templates/header.php](application/views/templates/header.php)
- [application/views/templates/main_footer.php](application/views/templates/main_footer.php)
- [application/views/templates/admin_header.php](application/views/templates/admin_header.php)
- [application/views/templates/admin_footer.php](application/views/templates/admin_footer.php)

**Errors:**
```
Failed to find a valid digest in the 'integrity' attribute for resource 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'
Failed to find a valid digest in the 'integrity' attribute for resource 'https://code.iconify.design/3/3.1.0/iconify.min.js'
Failed to find a valid digest in the 'integrity' attribute for resource 'https://cdn.jsdelivr.net/npm/chart.js'
Failed to find a valid digest in the 'integrity' attribute for resource 'https://unpkg.com/swup@4'
```

**Root Cause:** The SHA-384 integrity hashes in the HTML don't match the actual resource hashes. Resources are being blocked.

**Solution:** Update integrity hashes to match the actual CDN resources

**Tasks:**
- [ ] **Task 3.1** - For each external resource, calculate the correct SHA-384 hash
- [ ] **Task 3.2** - Update integrity attribute in header.php
  - Bootstrap CSS (line ~12)
- [ ] **Task 3.3** - Update integrity attributes in main_footer.php
  - Bootstrap JS
  - Iconify JS
  - Chart.js
  - Swup
- [ ] **Task 3.4** - Update integrity attributes in admin_footer.php (same resources)
- [ ] **Task 3.5** - Test that resources load correctly

**How to Generate Correct Hashes:**
Use this bash command:
```bash
# For a file URL
curl -s "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" | openssl dgst -sha384 -binary | openssl enc -base64 -A

# Output will be: sha384-[BASE64_HASH]
```

**Example Fix:**
```html
<!-- BEFORE -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/73Y1"
        crossorigin="anonymous"></script>

<!-- AFTER (with correct hash) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-[CORRECT_HASH_HERE]"
        crossorigin="anonymous"></script>
```

---

### Issue 4: Source Maps Not Allowed
**Severity:** MEDIUM  
**Errors:**
```
Connecting to 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css.map' violates the following Content Security Policy directive: "connect-src 'self'"
Connecting to 'https://cdn.jsdelivr.net/npm/chart.umd.min.js.map'
Connecting to 'https://unpkg.com/Swup.umd.js.map'
```

**Root Cause:** Source map files (.map) from CDNs are blocked by `connect-src 'self'` policy

**Solution Options:**
1. **For Production:** Keep current policy (source maps shouldn't be loaded in production builds)
2. **For Development:** Update CSP to allow source maps during development

**Tasks:**
- [ ] **Task 4.1** - Verify this is a development-only issue
- [ ] **Task 4.2** - Check if source maps are disabled in production builds
- [ ] **Task 4.3** - If needed for development, add CDN domains to `connect-src` in `.htaccess`

**Optional Fix (Development Only):**
```apache
# In .htaccess - Only if needed during development
Header set Content-Security-Policy "... connect-src 'self' https://cdn.jsdelivr.net https://unpkg.com; ..."
```

---

### Issue 5: Duplicate Script Tags
**Severity:** LOW  
**Files Affected:**
- [application/views/templates/main_footer.php](application/views/templates/main_footer.php)
- [application/views/templates/admin_footer.php](application/views/templates/admin_footer.php)

**Issue:** Some scripts are referenced multiple times (Iconify, Swup, Chart.js)

**Tasks:**
- [ ] **Task 5.1** - Remove duplicate script tags from main_footer.php
- [ ] **Task 5.2** - Remove duplicate script tags from admin_footer.php
- [ ] **Task 5.3** - Verify functionality still works

---

## Priority Summary

| Priority | Issue | Status |
|----------|-------|--------|
| 🔴 HIGH | Inline Styles | Not Started |
| 🔴 HIGH | Inline Scripts | Not Started |
| 🔴 HIGH | Integrity Hash Mismatches | Not Started |
| 🟡 MEDIUM | Source Maps | Not Started |
| 🟢 LOW | Duplicate Scripts | Not Started |

---

## Testing Checklist

After fixes are implemented, verify:

- [ ] No console errors related to CSP violations
- [ ] All external resources load successfully
- [ ] Inline styles are applied correctly (check UI rendering)
- [ ] Page transitions with Swup work smoothly
- [ ] Admin panel loads and functions correctly
- [ ] Icons (Iconify) display properly
- [ ] Charts display correctly
- [ ] Responsive design works on mobile
- [ ] All links and navigation work

**Test in Browser Console:**
```javascript
// Should be empty (no CSP violations)
console.clear();
// Reload page and watch console for errors
```

---

## Helpful Resources

- [MDN: Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
- [CSP Reference](https://content-security-policy.com/)
- [Integrity Attribute Guide](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity)
- [Bootstrap CDN Documentation](https://getbootstrap.com/docs/5.3/getting-started/download/)

---

## Notes

- All inline styles should be moved to `assets/css/style.css`
- All inline scripts should be moved to external files in `assets/js/`
- The CSP header in `.htaccess` should NOT be modified to use `'unsafe-inline'` in production
- Test thoroughly after each change to ensure functionality isn't broken
