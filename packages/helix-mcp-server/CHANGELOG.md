# Changelog

All notable changes to the Helix Ultimate MCP Server (`@joomshaper/helix-mcp-server`) will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.1.0] - 2026-10-13

### Added
* **Mega Menu Layout Tools**:
  * `helix_get_megamenu`: Inspects dropdown width, alignment, and column grid tree for any menu item.
  * `helix_update_megamenu`: Configures multi-column mega menu grids, assigns child menu links or Joomla modules, and validates the Bootstrap 12-column grid rule per row.
* **Universal Template Support**:
  * Dynamic detection for **all** Helix Ultimate plugin-based templates (starter template, official JoomShaper themes like *Giver, Unfold, Educon*, child templates, and custom agency themes).
  * Automatically resolves active frontend template via Joomla's `#__template_styles` table (`home = 1`).
  * Added optional `template` parameter across all 9 MCP tools (`helix_get_layout`, `helix_update_row`, `helix_get_megamenu`, `helix_update_megamenu`, `helix_scaffold_override`, `helix_scaffold_feature`, `helix_compile_scss`, `helix_audit_compatibility`).
* **Dictionary Normalization**:
  * Added `normalizeMap()` in `MegaMenuService` to prevent sparse array `null` holes when serializing menu item IDs.
* **Comprehensive Test Suite**:
  * Expanded to 25 unit and integration tests passing with 100% success rate.

---

## [1.0.1] - 2026-10-09

### Added
* Official Anthropic MCP Registry manifest (`server.json`).
* Smithery 1-click installer manifest (`smithery.yaml`).
* Package verification marker `mcpName` for official registry authentication.
* First official publication on npm registry and Anthropic's MCP Registry.

---

## [1.0.0] - 2026-10-09

### Added
* Initial production release of Helix Ultimate MCP Server.
* Core tools:
  * `helix_get_layout`: Inspects active template style layout.
  * `helix_update_row`: Modifies 12-column grid rows with live MySQL database sync.
  * `helix_scaffold_override`: Generates clean PHP 8 / Bootstrap 5 overrides in `html/`.
  * `helix_scaffold_feature`: Scaffolds Helix feature classes in `features/`.
  * `helix_compile_scss`: In-memory Dart Sass compiler with syntax diagnostics.
  * `helix_audit_compatibility`: Scans for legacy Joomla 3 globals and jQuery calls.
  * `helix_rollback`: Atomic snapshot rollbacks for zero-risk mutations.
* Core resources:
  * `helix://system/info`
  * `helix://layout/active`
  * `helix://positions/registered`
  * `helix://presets/all`
  * `helix://standards/architecture`
