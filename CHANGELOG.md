# Changelog

All notable changes to the Helix Ultimate Framework (System Plugin & Starter Template) are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [2.2.11] - Upcoming Release

### Added
* **Mobile Sticky Header Logo**:
  * Added dedicated `mobile_sticky_logo` option to configure a custom logo for sticky headers on mobile viewports (< 992px).
  * Implemented responsive logo visibility and class switching (`.logo-mobile-default`, `.logo-sticky-mobile`) in `HelixUltimateFeatureLogo`.
  * Updated `theme.scss` with desktop and mobile media queries to ensure smooth sticky transitions without layout shift.
  * Added translation strings `HELIX_ULTIMATE_MOBILE_STICKY_LOGO` and `HELIX_ULTIMATE_MOBILE_STICKY_LOGO_DESC` across plugin and template language files. (PR #576)

### Security
* **InputFilter Whitelist Embed Sanitization**:
  * Switched `InputFilter::getInstance()` flags in `Helper::sanitizeMediaCode()` from blacklist to strict tag and attribute whitelist (`0, 0, 0`), restricting embed elements strictly to authorized media tags (`iframe`, `audio`, `video`, `source`, `a`, `img`). (PR #580)
* **Error Page Preset Validation**:
  * Enforced strict regex validation (`/^[A-Za-z0-9_-]+$/`) and fallback mechanisms for preset stylesheets on error pages to prevent path traversal and arbitrary CSS inclusion. (PR #578)
* **Media Picker XSS Prevention**:
  * Escaped folder paths, folder names, file paths, preview URLs, titles, and breadcrumb markup in `Platform\Media` using `htmlspecialchars` with `ENT_QUOTES`. (PR #574)
* **Blog Image Deletion Authorization & Attribute Binding**:
  * Enforced article ID validation (`id > 0`) and verified that targeted images are explicitly referenced in the article's `helix_ultimate_image` or `helix_ultimate_gallery` attributes before executing deletion.
  * Added localized `HELIX_ULTIMATE_DELETE_FAILED` error response. (PR #566)
* **Article Rating CSRF Protection & Input Validation**:
  * Added Joomla CSRF session token validation to AJAX rating submissions in `main.js`.
  * Enforced integer bounds (1 to 5) and verified that the target article is published (`state = 1`) via parameterized SQL query bindings (`:articleId`).
  * Added `HELIX_INVALID_RATING` and `HELIX_ARTICLE_NOT_FOUND` language strings. (PR #570)
* **Mega Menu Badge Sanitization**:
  * Upgraded `Helper::sanitizeMegaMenuBadge()` to sanitize badge text via `InputFilter::getInstance()->clean($value, 'STRING')` followed by `strip_tags` and `trim`. (PR #571)

### Fixed
* **Joomla 5 & 6 Compatibility & Version Comparisons**:
  * Replaced loose integer comparison of `JVERSION` with `version_compare()` in `HTMLOverride.php` for Joomla 4, 5, and 6 compatibility.
  * Updated `menutype.php` layout field to instantiate `MenutypesModel` on Joomla 4+ instead of legacy `MenusModelMenutypes`. (PR #579)
* **Joomla 6 RTL Calendar Asset Crash**:
  * Fixed fatal asset loading error on article edit pages in RTL mode under Joomla 6 by dynamically requesting unified `field.calendar` asset when `JVERSION >= 6.0.0`. (PR #573)
* **Router & Strict Routing Conflicts**:
  * Scoped `helix_id = 9` input injection strictly to administrator `com_ajax` requests in `helixultimate.php`, preventing routing interference and false 404s when SEF or Strict Routing is enabled. (PR #572)
* **Multilingual Template Style Query Syntax**:
  * Fixed missing closing parenthesis in the SQL query condition for multilingual home styles in `Helper::getActiveTemplateStyle()`. (PR #571)
* **Custom JS and CSS Execution Order**:
  * Reordered script and stylesheet document buffers to ensure `custom.js` and `custom.css` consistently load after compressed and minified assets. (Commit `4b9125c3`)
* **Asset Compression Path Sanitization**:
  * Stripped URL query parameters during asset file path resolution and exclusion checks to prevent cache-busting strings from causing file-not-found false negatives. (Commit `dd192b6b`)
* **Custom CSS Cache Busting Format**:
  * Removed `v=` prefix from custom CSS query string to align with standard Joomla asset cache-busting convention (`?timestamp`). (PR #569)

### Changed
* **PHP 8.1 & Symfony Filesystem Pinning**:
  * Enforced PHP 8.1 platform compatibility in plugin `composer.json` and pinned `symfony/filesystem` to `~6.4` to prevent PHP 8.2+ requirement incompatibilities. (Commit `d2e5e56a`)
* **Security Test Suite Harness**:
  * Aligned `JoomlaInputFilterStub::getInstance()` signature in security test bootstrap with optional parameters to match Joomla CMS core.

---

## [2.2.10] - 2026-08-27

### Security
* Hardened template style updates in installer with ID scoping and obsolete vendor directory cleanup.
* Sanitized embedded media URLs and applied stricter input validation for video IDs.
* Sanitized social share URLs using `http_build_query`.
* Escaped intro and full image source URLs to prevent XSS.
* Added heading tag allowlist and escaped background URLs in Page Title feature.
* Sanitized Mega Menu settings JSON output in layout container.
* Added item-level and menu-level authorization checks to `saveMegaMenuSettings`.
* Enforced article-level edit permissions during content saves (`canEditArticle`).
* Hardened media path resolution with null-byte detection and canonical `realpath` confinement (`Helper::resolveMediaPath`).
* Implemented image content validation (`Helper::isValidImageContent`) to prevent upload bypasses.
* Enforced granular ACL permissions for media management actions.
* Restricted draft mode and coming soon preview access to authorized users.

### Fixed
* Fixed custom CSS loading logic with versioning and PSR-12 styling.
* Resolved lazy load image display issues in content layouts.
* Resolved audio and video embed sanitization bypasses.
* Resolved Mega Menu configuration sanitization issues.
* Restricted automatic plugin activation to fresh installs and previously enabled states.

### Changed
* Upgraded `symfony/filesystem` to 7.4.x and updated obsolete `scssphp` directory cleanup list.
* Bumped framework version to 2.2.10.

---

## [2.2.9] - 2026-07-15

### Added
* Added new developer guidelines and rules for Helix Ultimate framework components.

### Fixed
* Resolved offcanvas menu arrow position styling.
* Fixed copyright year update and added fields to bypass input filter.
* Standardized item ID handling across menu components.
* Inlined query parameter binding in Blog platform methods.
* Updated navigation label translations for enhanced clarity.
