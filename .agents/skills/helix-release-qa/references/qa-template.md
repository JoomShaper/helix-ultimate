# QA Test Plan & Release Verification Template

**Release:** `v<X.Y.Z>`  
**Base Version:** `v<X.Y.(Z-1)>`  
**Target Environments:** Joomla 4.4.x, Joomla 5.x, Joomla 6.x  
**PHP Versions:** PHP 8.1, PHP 8.2, PHP 8.3  

---

## 1. Pre-Requisites & Test Matrix

- [ ] Clean install of Joomla 4.4, Joomla 5.x, and Joomla 6.0 staging.
- [ ] Helix Ultimate template (`shaper_helixultimate`) and plugin (`plg_system_helixultimate`) installed.
- [ ] Multilingual setup configured (at least 2 languages: 1 LTR e.g. English, 1 RTL e.g. Arabic).
- [ ] Test with both **Strict Routing ON** and **Strict Routing OFF** in Joomla Global Configuration.
- [ ] Test with CSS/JS Compression **Enabled** and **Disabled** in Helix Options.

---

## 2. Automated Smoke Tests (CLI)

Run the automated smoke test script:

```bash
./.agents/skills/helix-release-qa/scripts/run-smoke-tests.sh
```

- [ ] **Expected Output:**
  - Modified PHP files pass syntax check (`php -l`).
  - All 8 security test suites pass (`run.php`).
  - `options.json`, `options.xml`, `composer.json`, and `helixultimate.xml` validate cleanly.

---

## 3. Core Feature & Fix Verification Matrix

### TC-01: Header & Logo Variations
* **Path:** Template Options → Basic → Header
* **Checks:**
  - Desktop sticky header displays sticky logo on scroll.
  - Mobile viewport (< 992px) displays mobile sticky logo on scroll.
  - Layout does not jump or flash broken image tags when scrolling.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-02: Blog Options & Media Embeds
* **Path:** Content → Articles → Edit Article → Helix Blog Options
* **Checks:**
  - Audio and Video embed code renders standard HTML5 / iframe embeds.
  - Unsafe tags (`<script>`, `<object>`, `<embed>`) and inline event handlers (`onload`, `onerror`) are stripped.
  - Image removal authorization: only referenced article images can be deleted; unreferenced paths or negative IDs are rejected.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-03: Presets & Error Pages
* **Path:** Frontend 404 / Error view (`/index.php?option=com_content&view=article&id=999999`)
* **Checks:**
  - 404 error page loads preset styling without CSS 404.
  - Preset name parameter tampering (`../../`) safely falls back to `default.css`.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-04: Menu Types & Joomla Model Compatibility
* **Path:** Template Options → Navigation → Select Menu dropdown
* **Checks:**
  - Dropdown lists all Joomla menu types across Joomla 4, 5, and 6 without class loading errors.
  - Template overrides in `html/` load smoothly without fatal errors on Joomla 5 & 6.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-05: Media Picker Security & Navigation
* **Path:** Template Options / Article Edit → Media Picker
* **Checks:**
  - Folder names, breadcrumbs, and image titles with special characters (`<`, `>`, `"`, `'`) are HTML-escaped.
  - Directory navigation, breadcrumb navigation, and file selection return valid paths.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-06: Multilingual & RTL Behavior
* **Path:** Frontend language switcher & RTL administrator
* **Checks:**
  - RTL calendar asset loads on Joomla 6 without missing asset crashes.
  - Active template style query resolves correctly across all installed languages.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-07: Routing & URL Rewriting
* **Path:** Global Configuration → SEO Settings (Strict Routing enabled)
* **Checks:**
  - Frontend articles, categories, and tags load without router 404 errors.
  - `helix_id` parameter is isolated strictly to admin `com_ajax` calls.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-08: Mega Menu Builder
* **Path:** Template Options → Navigation → Mega Menu
* **Checks:**
  - Menu item badges sanitize HTML and scripts.
  - Grid row column counts adhere to 12-column rules.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-09: Article Ratings
* **Path:** Frontend article view with rating enabled
* **Checks:**
  - Star ratings submit with valid Joomla CSRF session token.
  - Submissions missing CSRF token or out of range (not 1–5) are rejected.
- [ ] **Status:** [ ] Pass [ ] Fail

### TC-10: Custom Assets & Compression
* **Path:** Template Options → Advanced → Compress CSS & JS
* **Checks:**
  - `custom.css` and `custom.js` load **after** compressed bundles in `<head>`.
  - Timestamp query parameters format as `?{timestamp}`.
  - Asset exclusions match successfully even when URLs have query strings.
- [ ] **Status:** [ ] Pass [ ] Fail

---

## 4. General Regression Checklist

- [ ] Homepage renders complete layout grid without PHP notices.
- [ ] Off-canvas menu drawer opens, animates, and closes smoothly.
- [ ] Preset switching updates colors immediately.
- [ ] Typography and Google Fonts download and apply styles.
- [ ] Contact view override displays form and contact details.
- [ ] Keyboard navigation (Tab/Enter/Escape) operates menus and modals.

---

## 5. Sign-Off

| Role | Name | Status | Date |
| :--- | :--- | :--- | :--- |
| **QA Lead** | | [ ] Approved [ ] Rejected | |
| **Release Manager** | | [ ] Ready for Packaging | |
