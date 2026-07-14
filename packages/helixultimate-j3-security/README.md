# Helix Ultimate Joomla 3 Security Fixes

Security hardening patch package for **Helix Ultimate 2.1.x** on **Joomla 3.10.x**, modeled after [TLWebdesign Joomla 3 EOL Security Fixes](https://github.com/TLWebdesign/Joomla-3-EOL-Security-Fixes).

This package overwrites Helix plugin and template core files with security backports from Helix 2.2.3–2.2.8, then **self-uninstalls** so no permanent extension remains.

## Version 1.0.0 fixes (Helix baseline 2.1.4-j3sec)

- **[HU-SEC-001]** Open redirect protection for `helixreturn` admin login flow
- **[HU-SEC-002]** Central CSRF + ACL enforcement for Helix AJAX actions
- **[HU-SEC-003]** Path traversal protection for layout builder and media paths
- **[HU-SEC-004]** Upload hardening (extension allowlist, safe filenames, path confinement)
- **[HU-SEC-005]** XSS sanitization for blog embeds and admin HTML output
- **[HU-SEC-006]** Frontend article attribs merge allowlist; export ACL; no raw `$_POST`
- **[HU-SEC-007]** Mega menu settings sanitization and escaped menu output

## Supported environment

| Component | Version |
|-----------|---------|
| Joomla | 3.10.x |
| Helix plugin | 2.1.0 – 2.1.3 (patched to 2.1.4-j3sec) |
| Template | shaper_helixultimate 2.1.x |
| PHP | 7.2.5 – 8.0 (recommended 7.4 or 8.0) |

Per [Joomla 3 technical requirements](https://manual.joomla.org/docs/4.4/get-started/technical-requirements/).

## Backup first

**Always test on staging first.**

- Custom mega menu HTML or badge text may be stripped by sanitizers
- Blog audio/video embeds are filtered to an allowlist of safe tags
- Template overrides in `templates/shaper_helixultimate/html/` are **not** modified

## Install

1. Backup site files and database
2. Joomla Administrator → System → Install → Extensions
3. Upload `helixultimate_j3_security_fixes_v1.0.0.zip`
4. Confirm success message; package removes itself automatically
5. Audit log written to `administrator/logs/helix_j3_security_applied.json`

Compatible alongside [Joomla 3 EOL core security fixes](https://github.com/TLWebdesign/Joomla-3-EOL-Security-Fixes).

## Build from source

```bash
# Run security regression tests
php plugins/system/helixultimate/tests/security/run.php

# Build patch zip
php packages/helixultimate-j3-security/build/build-j3-security.php
```

Output: `packages/helixultimate-j3-security/dist/helixultimate_j3_security_fixes_v1.0.0.zip`

## Releasing to GitHub

The package zips are compiled automatically via GitHub Actions.

### Option A: Release with Tag `j3-security-v1.0.0` (Recommended)

1. **Update `update.xml`**:
   In `packages/helixultimate-j3-security/update.xml`, update the download/info URLs to use `j3-security-v1.0.0` instead of `v1.0.0`.
2. **Commit, Push, and Tag on Branch**:
   ```bash
   git add packages/helixultimate-j3-security/update.xml
   git commit -m "Update download URLs for j3-security-v1.0.0"
   git tag j3-security-v1.0.0
   git push origin security/j3-eol-security-fixes --tags
   ```
3. **Download Artifacts**:
   Wait for the **Helix J3 Security Release** workflow to finish in GitHub Actions, then download the `helix-j3-security` artifact package.
4. **Publish Release**:
   Draft a GitHub Release for tag `j3-security-v1.0.0` and upload all zip files, `manifest.json`, and `SHA256SUMS` as assets.

### Option B: Release with Tag `v1.0.0`

1. **Trigger Build Manually**:
   Go to GitHub Actions, select the **Helix J3 Security Release** workflow, click **Run workflow**, and target the release branch.
2. **Download Artifacts**:
   Download the built `helix-j3-security` artifacts from the workflow run.
3. **Publish Release**:
   Draft a GitHub Release for tag `v1.0.0` and upload the built zip files, `manifest.json`, and `SHA256SUMS` as assets.

## Migration notice

Joomla 3 is end of life. This package is a **security stopgap** for existing sites. Plan upgrade to **Joomla 4+** and **Helix Ultimate 2.2.x**.

## License

GPL-2.0-or-later (same as Helix Ultimate)
