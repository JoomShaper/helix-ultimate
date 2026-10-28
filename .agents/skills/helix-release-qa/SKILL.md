---
name: helix-release-qa
description: >-
  Audits changes, runs automated smoke tests, and generates comprehensive QA test plans, Markdown changelogs, and plain text release notes for Helix Ultimate (Joomla 4, 5, and 6). Use this skill when asked to run a QA audit, prepare QA release documentation, generate pre-release test matrices, create plain text changelogs, or verify bug fixes and security patches before packaging.
---

# Helix Ultimate Release QA, Audit & Changelog Skill

This skill guides the agent through auditing new merges, running pre-release verification scripts, and generating release-ready QA documentation, Markdown changelogs, and plain text release notes for the Helix Ultimate framework across Joomla 4, 5, and 6.

---

## When to Use This Skill

Activate this skill when:
- The user requests a QA test plan, QA checklist, or QA verification doc for an upcoming release.
- The user asks for a **changelog** (Markdown `CHANGELOG.md` or plain text format for release announcements).
- A new pull request or series of merges needs pre-release verification and smoke testing.
- Security fixes, sanitization patches, or framework refactors require validation against regression test suites.
- Validating template and system plugin compatibility across Joomla 4.4, Joomla 5, and Joomla 6.

---

## Standard Release QA & Documentation Workflow

Follow this 5-step workflow to audit code and produce release deliverables:

### Step 1: Discover Recent Merges & Code Diff

1. Identify the baseline release tag and commits since the last release:
   ```bash
   git describe --tags --abbrev=0
   git log $(git describe --tags --abbrev=0)..HEAD --merges --oneline
   ```
2. Inspect the list of modified files:
   ```bash
   git diff --name-only $(git describe --tags --abbrev=0)..HEAD
   ```
3. Group changes by category:
   - **Features (New)**: New template options, responsive behavior, layout capabilities.
   - **Security**: Sanitization, input filtering, authorization checks, CSRF, escaping.
   - **Bug Fixes (Fix)**: Compatibility regressions, routing errors, SQL queries, asset loading order.
   - **Chores / Dependencies (Update)**: Composer dependencies, build tooling, manifests.

### Step 2: Run Automated Smoke Tests

Execute the bundled smoke test script to verify PHP syntax, security suites, and XML/JSON manifests:

```bash
./.agents/skills/helix-release-qa/scripts/run-smoke-tests.sh
```

Or run individual checks:
* **Security Suites (8 phases):**
  ```bash
  php plugins/system/helixultimate/tests/security/run.php
  ```
* **PHP Linting on Modified Files:**
  ```bash
  for f in $(git diff --name-only $(git describe --tags --abbrev=0)..HEAD | grep '\.php$'); do php -l "$f"; done
  ```
* **Framework Compatibility Tool:**
  Use the `helix_audit_compatibility` MCP tool to scan for legacy jQuery/Joomla calls.

### Step 3: Analyze Security & Compatibility Patterns

Review changes against the security audit standards in [security-audit.md](./references/security-audit.md):
- **Embed Filtering:** Ensure `InputFilter` uses whitelist flags (`0, 0, 0`).
- **Path Traversal:** Ensure presets and file parameters validate with strict regex (`/^[A-Za-z0-9_-]+$/`).
- **Authorization & Scoping:** Ensure content-level edit checks (`Helper::canEditArticle`) and explicit attribute binding exist before file modifications.
- **XSS Escaping:** Ensure all dynamic output in modals, breadcrumbs, and labels is escaped with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
- **CSRF Tokens:** Ensure AJAX requests supply `csrf.token` and server enforces `Session::checkToken()`.
- **Joomla Versions:** Ensure version checks use `version_compare(JVERSION, 'X.Y.Z', '<')` rather than loose string comparison.

### Step 4: Generate the QA Test Plan (`QA.md`)

Use the standard structure in [qa-template.md](./references/qa-template.md) to generate or update `QA.md` at the project root:

1. **Test Matrix:** List required Joomla versions (4.4, 5.x, 6.0), PHP versions (8.1–8.3), and configurations (Strict Routing ON/OFF, Compression ON/OFF, LTR/RTL).
2. **Automated CLI Smoke Test:** Document exact command and expected pass output.
3. **Feature & Fix Test Cases (TC-01, TC-02, ...):** For each PR, provide:
   - Setting path / UI location
   - Clear, step-by-step test instructions
   - Positive test case (expected behavior)
   - Negative / tamper test case (attack payload, invalid ID, missing token)
   - Pass / Fail status checkboxes
4. **General Regression Checklist:** Standard sanity checks (homepage layout, off-canvas toggler, preset switching, typography, contact override).
5. **Sign-Off Table:** Roles for QA Lead and Release Manager.

### Step 5: Generate Release Changelogs (Markdown & Plain Text)

Helix Ultimate requires both structured Markdown and Plain Text formats as detailed in [changelog-formats.md](./references/changelog-formats.md):

1. **`CHANGELOG.md` at Project Root:**
   - Update repository `CHANGELOG.md` using the Keep a Changelog standard (`### Added`, `### Security`, `### Fixed`, `### Changed`).
2. **Plain Text Bulleted Changelog:**
   - Provide clean single-line bulleted entries (`- New:`, `- Security:`, `- Fix:`, `- Update:`) ideal for Git release tags, GitHub release bodies, and release notes.
3. **Plain Text Categorized Changelog:**
   - Provide categorized plain text grouped under uppercase headers (`NEW:`, `SECURITY:`, `FIXES & IMPROVEMENTS:`, `CHORES & DEPENDENCIES:`) ready for JoomShaper blog announcements and newsletters.

---

## Helper References & Scripts

* [QA Checklist Template](./references/qa-template.md) — Reusable markdown template for release QA test plans.
* [Changelog Formats Reference](./references/changelog-formats.md) — Standardized templates for `CHANGELOG.md` and Plain Text release notes.
* [Security Audit Guide](./references/security-audit.md) — Reference for security patterns and validation rules.
* [Smoke Test Runner](./scripts/run-smoke-tests.sh) — Bash script for automated pre-flight testing.
