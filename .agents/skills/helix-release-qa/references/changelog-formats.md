# Helix Ultimate Changelog Formats Reference

When preparing release documentation, Helix Ultimate uses three standardized changelog formats depending on the audience:

1. **Repository Changelog (`CHANGELOG.md`)**: Full markdown adhering to [Keep a Changelog](https://keepachangelog.com/).
2. **Plain Text Bulleted Changelog**: Concise single-line entries for release tags, GitHub Releases, and quick developer summaries.
3. **Plain Text Categorized Changelog**: Sectioned plain text for blog posts, email announcements, and marketing communication.

---

## 1. Markdown Changelog (`CHANGELOG.md`)

Location: `CHANGELOG.md` at repository root.

```markdown
# Changelog

All notable changes to the Helix Ultimate Framework (System Plugin & Starter Template) are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [<version>] - <YYYY-MM-DD>

### Added
* **<Feature Name>**:
  * Description of feature and architectural notes. (PR #<number>)

### Security
* **<Security Fix Name>**:
  * Description of vulnerability mitigated, sanitization methods, and parameters. (PR #<number>)

### Fixed
* **<Bug Fix Name>**:
  * Description of issue resolved, affected Joomla versions, and fix details. (PR #<number>)

### Changed
* **<Maintenance / Dependency Name>**:
  * Description of dependency bumps, PHP requirements, or internal refactors.
```

---

## 2. Plain Text Bulleted Changelog

Best for: Quick developer handoffs, Git tag annotations, commit release bodies, and quick copy-paste.

Prefix each line with:
* `- New:` for features
* `- Security:` for security fixes and sanitization
* `- Fix:` for bugs and compatibility fixes
* `- Update:` for chores, dependencies, and internal refactors

### Example:

```text
Helix Ultimate <version> Changelog:

- New: Added mobile sticky header logo option for mobile viewports (< 992px).
- Security: Enforced strict InputFilter whitelist flags for audio/video embed sanitization.
- Security: Added regex validation and safe fallback for error page presets to prevent path traversal.
- Security: Escaped media picker folder names, file paths, previews, and breadcrumbs against XSS.
- Security: Enforced article edit authorization and verified image reference existence before blog image deletion.
- Security: Added CSRF token validation and range verification for article ratings.
- Security: Sanitized Mega Menu badge text using Joomla InputFilter.
- Fix: Resolved Joomla 6 crash on article edit pages with RTL calendar asset.
- Fix: Modernized Joomla 5 and 6 version comparisons and MenutypesModel resolution.
- Fix: Scoped legacy helix_id parameter strictly to admin com_ajax to resolve strict routing conflicts.
- Fix: Fixed missing closing parenthesis in multilingual template style database query.
- Fix: Ensured custom.js and custom.css load after compressed and minified bundle assets.
- Fix: Sanitized file paths by stripping query strings during asset exclusion checks and compression.
- Fix: Removed "v=" prefix from custom CSS cache-busting query parameter.
- Update: Enforced PHP 8.1 platform compatibility and pinned symfony/filesystem to 6.4.x.
```

---

## 3. Plain Text Categorized Changelog

Best for: JoomShaper blog announcements, email newsletters, and QA release notes.

### Example:

```text
Helix Ultimate Version <version>

NEW:
- Mobile sticky header logo option for responsive layouts (< 992px)
- Language strings for mobile sticky header logo customization

SECURITY:
- Stricter InputFilter whitelist sanitization for embedded media
- Regex validation for error page presets preventing directory traversal
- Output escaping across media picker thumbnails, paths, and breadcrumbs
- Article-level edit authorization and attribute binding for blog image deletion
- CSRF session token verification and rating range validation for article ratings
- HTML and script tag stripping for Mega Menu badge values

FIXES & IMPROVEMENTS:
- Fixed Joomla 6 RTL calendar asset loading crash
- Standardized Joomla 4, 5, and 6 version checks via version_compare()
- Resolved menu model loading on Joomla 4+ (MenutypesModel)
- Scoped framework ID injection to avoid SEF and strict routing 404 conflicts
- Corrected SQL syntax for active style queries in multilingual setups
- Preserved proper execution order for custom.js and custom.css after minified bundles
- Stripped URL query strings during asset compression and exclusion checks
- Cleaned up custom CSS timestamp query parameter format

CHORES & DEPENDENCIES:
- Enforced PHP 8.1 compatibility and pinned symfony/filesystem to 6.4.x
```
