# Helix Ultimate QA Test Plan & Release Changelog (v2.2.11)

**Release:** `v2.2.11`  
**Base Version:** `v2.2.10`  
**Target Environments:** Joomla 4.4.x, Joomla 5.x, Joomla 6.x  
**PHP Versions:** PHP 8.1, PHP 8.2, PHP 8.3  

---

## Pre-Requisites & Test Matrix

- [ ] Clean install of Joomla 4.4, Joomla 5.x, and Joomla 6.0 staging.
- [ ] Helix Ultimate template (`shaper_helixultimate`) and plugin (`plg_system_helixultimate`) installed.
- [ ] Multilingual setup configured (at least 2 languages: 1 LTR e.g. English, 1 RTL e.g. Arabic).
- [ ] Test with both **Strict Routing ON** and **Strict Routing OFF** in Joomla Global Configuration.
- [ ] Test with CSS/JS Compression **Enabled** and **Disabled** in Helix Options.

---

## 1. Automated Security Smoke Tests (CLI)

Run on development/staging terminal:

```bash
php plugins/system/helixultimate/tests/security/run.php
```

- [ ] **Expected Output:** `All security tests passed (8 suites).`

---

## 2. Feature & Fix QA Test Cases

### TC-01: Mobile Sticky Header Logo (PR #576)
* **Configuration:** Template Options → Basic → Header → Sticky Header: **Enabled**
* **Inputs:**
  * Main Logo: Set desktop image
  * Sticky Logo: Set sticky desktop image
  * Mobile Logo: Set mobile image
  * Mobile Sticky Logo: Set mobile sticky image
* **Test Steps:**
  1. Open frontend on desktop (> 992px width). Scroll down: Main logo hides, **Sticky Logo** appears.
  2. Resize browser or open mobile view (< 992px width). Initial state: **Mobile Logo** appears.
  3. Scroll down in mobile view: Mobile logo hides, **Mobile Sticky Logo** appears.
  4. Test fallback: Clear Mobile Sticky Logo, leave Desktop Sticky Logo. Verify mobile scrolling does not break layout or show broken image tags.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-02: Video/Audio Embed Sanitization & Whitelist (PR #580)
* **Configuration:** Content → Articles → Edit Article → Helix Blog Options → Audio/Video Embed.
* **Test Steps:**
  1. Paste standard YouTube embed:
     ```html
     <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" width="560" height="315" frameborder="0" allowfullscreen></iframe>
     ```
     Save article. View frontend. **Expected:** Video renders properly.
  2. Paste malicious script and unauthorized tags:
     ```html
     <script>alert('XSS')</script><iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" onload="alert('XSS')"></iframe><object data="test.swf"></object>
     ```
     Save article. Inspect source in database/frontend.
  3. **Expected:** `<script>`, `<object>`, and `onload="..."` attributes are completely stripped. Only clean iframe remains.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-03: Error Page Preset Fallback & Path Traversal (PR #578)
* **Configuration:** Trigger 404 error on frontend (e.g. `/index.php?option=com_content&view=article&id=9999999`).
* **Test Steps:**
  1. Load 404 page normally. Verify styling loads from `css/presets/{preset}.css` without 404 on CSS.
  2. Test path traversal injection: Manually request error page or test preset tamper:
     ```
     preset=../../../../etc/passwd
     ```
  3. **Expected:** Fallback defaults to `default`. Filename strictly matches `^[A-Za-z0-9_-]+$`. No traversal occurs.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-04: Menu Type Model & Joomla 5/6 Version Comparison (PR #579)
* **Configuration:** Template Options → Navigation → Menu Type dropdown.
* **Test Steps:**
  1. Open Template Options on Joomla 4.4, Joomla 5.x, and Joomla 6.x.
  2. Navigate to **Navigation** settings. Inspect the **Select Menu** dropdown.
  3. **Expected:** All menus (Main Menu, User Menu, etc.) are listed properly without PHP `Class not found` fatal errors.
  4. Test component overrides on Joomla 5 & 6 (e.g. article view, category blog). Overrides load from the correct legacy/modern path structure without breaking.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-05: Media Picker XSS Prevention (PR #574)
* **Configuration:** Joomla Media Manager & Helix Media Picker.
* **Test Steps:**
  1. In Joomla Media, create a test folder named `test<img src=x onerror=alert(1)>` or upload an image `photo"onload="alert(1).jpg`.
  2. In Template Options (e.g. Logo image picker), click **Select**.
  3. Navigate folders and click breadcrumbs.
  4. **Expected:** Folder names, breadcrumbs, thumbnail titles, and data attributes are HTML-escaped. No alert boxes or JavaScript execution triggers. Selection functions normally.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-06: Blog Image Deletion Authorization & Scoping (PR #566)
* **Configuration:** Article Edit → Helix Blog Options (Frontend & Backend).
* **Test Steps:**
  1. Upload a blog image to an article and save. Click the **Remove Image** (trash icon) button.
     * **Expected:** Image is removed from article attributes successfully.
  2. Tamper the AJAX removal request (using browser DevTools Network tab or Postman):
     * Set `id=0` or `id=-1` (invalid ID).
     * **Expected:** Response `JERROR_ALERTNOAUTHOR`.
     * Set `id={valid_id}` but change `src` to an image that does NOT belong to this article (e.g. `/images/joomla_logo.png`).
     * **Expected:** Response `HELIX_ULTIMATE_DELETE_FAILED`. Arbitrary images cannot be unlinked or deleted.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-07: Joomla 6 RTL Calendar Asset Fix (PR #573)
* **Configuration:** Joomla 6 site.
* **Test Steps:**
  1. Install an RTL language (e.g. Arabic) and set as Default Administrator language.
  2. Navigate to Content → Articles → Edit an article (which renders date/calendar fields).
  3. **Expected:** Page loads smoothly. No fatal WebAsset error for missing `field.calendar-rtl` asset (Joomla 6 consolidated this into `field.calendar`).
  4. Repeat on Joomla 4 & 5 with RTL admin: Verify calendar displays properly.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-08: Strict Routing & SEF URL Isolation (PR #572)
* **Configuration:** Global Configuration → SEO:
  * Search Engine Friendly URLs: **Yes**
  * Use URL Rewriting: **Yes**
  * Strict Routing: **Yes**
* **Test Steps:**
  1. Browse frontend pages, categories, and tags.
  2. **Expected:** All pages load without 404 or router redirect loops. `helix_id` is no longer injected into frontend request parameters.
  3. Open Template Options in admin: Verify Helix administrative AJAX requests (`com_ajax`) continue functioning normally.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-09: Mega Menu Badge Sanitization & Multilingual Query (PR #571)
* **Configuration:** Template Options → Navigation → Mega Menu.
* **Test Steps:**
  1. Select a menu item in Mega Menu Builder. Set **Badge Text** to `<script>alert(1)</script>NEW`.
  2. Save settings. View frontend navigation.
  3. **Expected:** Badge renders as `NEW`. Script tags are stripped.
  4. Test on a multilingual site with multiple languages assigned to different template styles:
     * **Expected:** Frontend language switcher routes to corresponding template styles without SQL syntax errors.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-10: Article Rating CSRF Protection & Bound Query (PR #570)
* **Configuration:** Enable Article Ratings in Template Options / Article Options.
* **Test Steps:**
  1. Open an article on frontend. Click star 4.
     * **Expected:** Rating submits successfully and displays "Thanks for your rating."
  2. Open DevTools Network tab, copy rating request as cURL. Remove CSRF token parameter. Re-send request.
     * **Expected:** Request rejected.
  3. Send rating with invalid value (`rating=10` or `rating=-1`):
     * **Expected:** Error response `HELIX_INVALID_RATING`.
  4. Send rating for non-existent or unpublished article (`article_id=999999`):
     * **Expected:** Error response `HELIX_ARTICLE_NOT_FOUND`.
- [ ] **Status:** [ ] Pass [ ] Fail

---

### TC-11: Custom Assets Order & Cache-Buster Sanitization (Commits `4b9125c3`, `dd192b6b`, `fd534029`)
* **Configuration:** Template Options → Advanced → Compile CSS to SCSS / Compress CSS & JS: **Enabled**.
* **Test Steps:**
  1. Add custom CSS rule in `templates/shaper_helixultimate/css/custom.css` (e.g. `body { outline: 1px solid red !important; }`).
  2. Add custom script in `templates/shaper_helixultimate/js/custom.js` (e.g. `console.log("custom.js executed");`).
  3. Load frontend. Inspect `<head>` in Page Source:
     * **Expected:** `custom.css` `<link>` appears **after** compressed CSS bundle.
     * **Expected:** `custom.js` `<script>` appears **after** compressed JS bundle.
  4. Verify custom CSS stylesheet link format: uses `custom.css?{timestamp}` (without `v=`).
  5. Add an excluded CSS file with query parameters in Template Options. Verify exclusion still matches and is not ignored due to query strings.
- [ ] **Status:** [ ] Pass [ ] Fail

---

## 3. General Regression Checklist

- [ ] **Homepage & Layout Builder:** Grid rows, fluid rows, column ordering render as expected.
- [ ] **Off-canvas Menu:** Toggler opens off-canvas drawer smoothly on mobile and desktop; close button functions.
- [ ] **Preset Switching:** Switching presets in Template Options updates colors immediately.
- [ ] **Typography & Google Fonts:** Selected Google webfonts download and display properly.
- [ ] **Blog Formats:** Standard, Gallery, Audio, Video, and Quote post formats render correctly.
- [ ] **Contact Page Override:** Contact view layout renders address and form fields properly.
- [ ] **Accessibility:** Screen readers and keyboard navigation (Tab/Enter) navigate dropdown menus and off-canvas correctly.

---

## 4. Sign-Off

| Role | Name | Signature / Status | Date |
| :--- | :--- | :--- | :--- |
| **QA Lead** | | [ ] Approved [ ] Rejected | |
| **Release Manager** | | [ ] Ready for Packaging | |
