# Helix Ultimate Security Audit & Verification Guide

This reference details the core security validation patterns enforced in Helix Ultimate. Use these guidelines when auditing recent pull requests or verifying bug fixes for a release.

---

## 1. InputFilter & Embed Sanitization Pattern

When user input contains HTML that should allow safe rich-media tags (like `iframe`, `audio`, `video`, `source`):

* **Whitelist Enforcement:** Always use `InputFilter::getInstance($tags, $attrs, 0, 0, 0)`.
  * `tagsMethod = 0`: `ONLY_ALLOW_DEFINED_TAGS`
  * `attrMethod = 0`: `ONLY_ALLOW_DEFINED_ATTRIBUTES`
  * `xssAuto = 0`: Only auto clean essentials
* **Never use blacklist mode (`1, 1`)** for user-supplied HTML embeds, as blacklist flags leave unspecified tags vulnerable.
* **Testing:** Attempt embedding `<script>`, `<object>`, `<embed>`, and event attributes like `onload`, `onerror`. Ensure they are stripped while legitimate `iframe` and `video` attributes remain.

---

## 2. Path Traversal & Preset Loading

When loading file assets based on user or template parameters:

* **Strict Character Whitelisting:**
  ```php
  if (!is_string($preset) || !preg_match('/^[A-Za-z0-9_-]+$/', $preset)) {
      $preset = 'default';
  }
  ```
* **Confinement:** Never interpolate raw `$params->get(...)` into file paths without canonicalizing and checking boundaries.
* **Testing:** Pass `../../../../` or null-byte characters (`%00`) into the preset parameter and verify that the application falls back safely to `default`.

---

## 3. Article Attribute & Media Scoping

When modifying or deleting files or media attributes linked to content:

* **Authorization Check:** Confirm user edit access (`Helper::canEditArticle($articleId)`).
* **Positive ID Validation:** Ensure `$articleId > 0`.
* **Explicit Reference Verification:** Before unlinking or clearing media, verify that the target `$src` exists in the article's decoded `attribs` (`helix_ultimate_image` or `helix_ultimate_gallery`).
* **Testing:** Tamper requests by setting `src` to an arbitrary existing image (`/images/joomla_logo.png`). Verify that unreferenced paths return an error (`HELIX_ULTIMATE_DELETE_FAILED`).

---

## 4. Cross-Site Scripting (XSS) in Admin Pickers & Modals

For dynamic DOM construction in media pickers, layout builders, and mega menus:

* **Server-side Escaping:** Escape all folder names, file paths, preview URLs, titles, and breadcrumbs with `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`.
* **Client-side Escaping:** When using JavaScript template literals, encode attributes using `encodeURIComponent` or safe DOM text content setters (`textContent` instead of `innerHTML`).
* **Testing:** Create items with payload: `"><script>alert(1)</script>` or `' onerror='alert(1)`. Verify that quotes and angle brackets are entity-encoded.

---

## 5. CSRF Protection for AJAX Actions

For all state-changing AJAX endpoints (ratings, layout mutations, style saves):

* **Client Token Inclusion:**
  ```javascript
  var csrfToken = window.Joomla && typeof Joomla.getOptions === 'function'
      ? Joomla.getOptions('csrf.token', '')
      : '';
  if (csrfToken) {
      request[csrfToken] = 1;
  }
  ```
* **Server Verification:** Validate via `Session::checkToken() or die('Invalid Token')`.
* **Testing:** Re-send the request with the token stripped; verify the server rejects the request.

---

## 6. Database Parameter Binding

Avoid raw string concatenation in SQL queries:

```php
$query->select($db->quoteName('id'))
    ->from($db->quoteName('#__content'))
    ->where($db->quoteName('id') . ' = :articleId')
    ->where($db->quoteName('state') . ' = 1')
    ->bind(':articleId', $id, ParameterType::INTEGER);
```

* **Testing:** Verify with single quotes or SQL injection payloads in IDs; confirm that queries remain parameterized.
