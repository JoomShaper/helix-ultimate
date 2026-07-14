# Helix Ultimate J3 Security Backport Audit

Baseline: **Helix 2.0.18** (`v2.0.18`) on **Joomla 3.10.x**

## Source security commits (dev branch)

| Commit | Phase | Files |
|--------|-------|-------|
| `e91206c2` | Open redirect | `helixultimate.php`, `Helper.php` |
| `6035666d` | CSRF + ACL | `helixultimate.php`, `Helper.php`, `Platform.php`, `Request.php` |
| `4fc9fb06` | Path traversal | `Helper.php`, `Request.php`, `Media.php`, `Blog.php` |
| `7293319f` | Upload hardening | `Media.php`, `Blog.php`, `Request.php` |
| `783b1e3b` | XSS / embeds | `Helper.php`, blog layouts, `Response.php`, `Request.php` |
| `5cfe9694` | Attribs / export / POST | `helixultimate.php`, `Helper.php`, `Request.php` |
| `77cb957c` | Mega menu XSS | `Helper.php`, `Response.php`, `HelixultimateMenu.php`, `mod_menu/default.php`, `helixultimate.php` |
| `63ae8852` | Module chrome escape | `overrides/layouts/chromes/html5.php`, pagination link |

## Patched file list (this release)

### Plugin (`plugins/system/helixultimate/`)

- `helixultimate.php`
- `helixultimate.xml`
- `src/Platform/Helper.php`
- `src/Platform/Platform.php`
- `src/Platform/Request.php`
- `src/Platform/Media.php`
- `src/Platform/Blog.php`
- `src/HttpResponse/Response.php`
- `src/Core/Classes/HelixultimateMenu.php`
- `overrides/layouts/joomla/content/blog/audio.php`
- `overrides/layouts/joomla/content/blog/gallery.php`
- `overrides/layouts/joomla/content/blog/video.php`
- `overrides/mod_menu/default.php`
- `overrides/layouts/chromes/html5.php`
- `tests/security/*` (QA only, excluded from patch zip)

### Template (`templates/shaper_helixultimate/`)

- `templateDetails.xml`
- `html/layouts/joomla/content/blog/audio.php`
- `html/layouts/joomla/content/blog/gallery.php`
- `html/layouts/joomla/content/blog/video.php`
- `html/mod_menu/default.php`
- `html/layouts/chromes/sp_xhtml.php`

## J3 compatibility notes

- Uses `Factory::getUser()` instead of `getIdentity()` (J3-safe)
- Uses `Factory::getDbo()` instead of DI container
- Uses `Joomla\CMS\Filesystem\Path` (J3 namespace)
- `onContentBeforeSave` accepts J3 context values (`com_content.article`, `com_content.form`)
- PHP 7.2+ compatible (no `str_contains` in runtime code)

## Supported install matrix

| Joomla | Helix plugin | Template | PHP |
|--------|--------------|----------|-----|
| 3.10.x | 2.0.x – 2.0.18 | shaper_helixultimate 2.0.x | 7.2.5 – 8.0 |
