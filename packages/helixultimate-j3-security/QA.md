# QA Checklist — Helix J3 Security v1.0.0

Run on **Joomla 3.10.x** staging with **PHP 7.4** and **PHP 8.0**.

## Pre-install

- [ ] Helix plugin 2.0.x installed and enabled
- [ ] shaper_helixultimate template installed
- [ ] Full site backup taken

## Patch install

- [ ] Upload `helixultimate_j3_security_fixes_v1.0.0.zip` via Extension Manager
- [ ] Success message displayed with fix summary
- [ ] Package self-uninstalls (not listed under Extensions → Manage)
- [ ] `administrator/logs/helix_j3_security_applied.json` created with SHA1 checksums
- [ ] Plugin version shows **2.0.19-j3sec** in `#__extensions` manifest cache

## Security smoke tests

- [ ] AJAX request without CSRF token returns JSON error (admin layout save)
- [ ] `helixreturn` with external URL does not redirect
- [ ] Media path `../configuration.php` rejected
- [ ] Upload `.svg` or `.php` rejected in blog/media upload
- [ ] Mega menu badge with `<script>` stripped on save
- [ ] Template settings export blocked for non-`com_templates` editors

## Regression

- [ ] Frontend homepage renders
- [ ] Mega menu renders and opens
- [ ] Blog article with image/video/audio formats displays
- [ ] Admin Helix template options save correctly
- [ ] Contact form submits

## Compatibility

- [ ] Site with [TLWebdesign J3 core fixes](https://github.com/TLWebdesign/Joomla-3-EOL-Security-Fixes) already applied — no PHP fatal errors

## Automated (dev machine)

```bash
php plugins/system/helixultimate/tests/security/run.php
php packages/helixultimate-j3-security/build/build-j3-security.php
```

Expected: `All security tests passed (7 suites).`
