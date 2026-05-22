# Helix Ultimate Framework — AI Developer Architecture (AGENTS.md)

This document serves as the primary architecture and knowledge base for AI agents developing on the Helix Ultimate framework (Joomla 4, 5, and 6).

## Tech Stack
- **CMS**: Joomla 4, 5, and 6
- **Language**: PHP (following Joomla coding standards where applicable)
- **Styling**: SCSS prioritized over standard CSS, utilizing Bootstrap 5 conventions
- **Scripting**: Vanilla JS (ES6) prioritized over jQuery
- **Template Framework**: Helix Ultimate

## Directory Structure
The framework is divided into two main components:

1. **System Plugin (`plugins/system/helixultimate/`)**
   - Contains the core framework logic, layouts, custom fields, and parameters.
   - `core/`: Core classes and bootstrapping.
   - `src/`: Namespaced classes (`HelixUltimate\Framework\...`).
   - `fields/`: Custom Joomla form fields.
   - `layouts/`: Reusable HTML layouts.
   - `html/`: Layout overrides for standard Joomla components.

2. **Template (`templates/shaper_helixultimate/`)**
   - The frontend starter template utilizing the framework.
   - `index.php`: Main entry point for rendering.
   - `scss/`: All styling should be done here. Do NOT write standard CSS unless unavoidable.
   - `js/`: Custom scripts (prefer Vanilla JS).
   - `features/`: Template features (logo, social, title, etc.).
   - `html/`: Template-specific overrides.

## Critical Rules
1. **SCSS over CSS**: Always write styles in SCSS. Use the existing SCSS structure in the template (`scss/theme.scss`, `scss/custom.scss`). Utilize Bootstrap 5 mixins and variables.
2. **Vanilla JS**: Avoid jQuery. Use modern Vanilla JS (ES6+) for all new scripts.
3. **Template Overrides**: When overriding Joomla components or modules, place them in the `html/` directory of the template, following the standard Joomla override hierarchy.
4. **Framework API**: Utilize `HelixUltimate\Framework\Platform\Helper` and other built-in classes rather than reinventing the wheel.
5. **Joomla Core Compatibility**: Ensure code is compatible with Joomla 4+ (avoid deprecated Joomla 3 methods like `JFactory`, use `Joomla\CMS\Factory` and Namespaced classes instead).

## Styling Conventions
- Utilize Bootstrap 5 utility classes for layout (e.g., `d-flex`, `row`, `col`) instead of writing custom CSS grids/flexbox where Bootstrap already provides a solution.
- Scope custom styles properly to avoid global conflicts.

## JavaScript Conventions
- Use `document.addEventListener('DOMContentLoaded', ...)` for initialization.
- Use `fetch` API instead of `jQuery.ajax`.
- Utilize standard ES6 features (const/let, arrow functions, template literals).
