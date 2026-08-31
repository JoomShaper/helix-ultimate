# Changelog

## 1.0.3 — 2026-08-31

- Bound blog image removal authorization to the specific article's attributes (`helix_ultimate_image` / `helix_ultimate_gallery`) to prevent arbitrary file deletion under the media root
- Updated deletion failure responses to use localized `HELIX_ULTIMATE_DELETE_FAILED` language string

## 1.0.2 — 2026-08-27

- Backported security hardening from Helix Ultimate 2.2.10
- Added binary raster decoding and MIME validation (`Helper::isValidImageContent`) to block file upload bypasses
- Hardened media path resolution with null-byte detection and canonical `realpath` confinement
- Added item-level and menu-level authorization checks to `saveMegaMenuSettings`
- Sanitized and escaped Mega Menu JSON layout attributes against XSS
- Added heading tag allowlist and quoted CSS background URL escaping to Page Title feature
- Enforced object-level edit authorization (`canEditArticle`) during content saves
- Hardened template style installer updates with ID scoping and obsolete vendor directory cleanup
- Resolved Joomla update site mapping via verified installed extension IDs

## 1.0.1 — 2026-08-24

- Enhanced custom field layout support and parameter handling
- Improved MegaMenu field value sanitization and builder hardening

## 1.0.0 — 2026-07-14

- Initial J3 security patch release (Helix baseline 2.1.4-j3sec)
- Backport security phases 1–7 from Helix 2.2.3–2.2.8 onto Helix 2.1.3
- Self-uninstalling `type="file"` installer with audit log
- Supported: Joomla 3.10.x, PHP 7.2.5+
