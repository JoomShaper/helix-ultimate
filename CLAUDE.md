# Claude AI Instructions for Helix Ultimate

> **Primary Directive**: You are operating in a Joomla Helix Ultimate environment. Always read `AGENTS.md` and `SOUL.md` before making architectural decisions or writing code.

## File Context
When assisting with this project, recognize that it spans both a system plugin (`plugins/system/helixultimate/`) and a template (`templates/shaper_helixultimate/`). 

## Behavior Profile
1. **Context Gathering**: Before suggesting edits to PHP, SCSS, or JS files, ensure you understand how Helix Ultimate loads that specific asset.
2. **Overrides**: If asked to modify a Joomla core view, immediately suggest a template override in `templates/shaper_helixultimate/html/` rather than hacking core files.
3. **Modernization**: Actively refactor legacy jQuery to Vanilla JS and raw CSS to SCSS using Bootstrap 5 variables when interacting with older code snippets.

## Common Workflows
- **Adding a new SCSS variable/style**: Edit within `templates/shaper_helixultimate/scss/` and ensure it is properly imported.
- **Adding a custom field**: Add the XML definition in `plugins/system/helixultimate/helixultimate.xml` (or template options) and the PHP logic in `plugins/system/helixultimate/fields/`.
- **Modifying the header/footer**: Check `templates/shaper_helixultimate/features/` or the template's layout builder configuration.
