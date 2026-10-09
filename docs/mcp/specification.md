# Protocol Specification: Resources, Tools & Prompts

## 1. MCP Resources (Read-Only Context)

Resources provide the AI with structured background context on demand.

### `helix://system/info`
* **Description**: Returns environment health, Joomla version, Helix plugin version, and active template style ID.
* **MIME Type**: `application/json`
* **Example Output**:
  ```json
  {
    "joomlaVersion": "5.2.0",
    "helixPluginVersion": "2.2.0",
    "templateName": "shaper_helixultimate",
    "templateStyleId": 1,
    "phpVersion": "8.2.14"
  }
  ```

### `helix://layout/active`
* **Description**: Complete JSON structure of rows, columns, and assigned module positions in the active template style.
* **MIME Type**: `application/json`

### `helix://positions/registered`
* **Description**: Array of all module positions defined in `templates/shaper_helixultimate/templateDetails.xml`.
* **MIME Type**: `application/json`

### `helix://standards/architecture`
* **Description**: The architectural rules mandated by `AGENTS.md` and `SOUL.md` (e.g. Vanilla JS only, Bootstrap 5 utilities, SCSS over CSS, Joomla 4/5/6 namespaces).
* **MIME Type**: `text/markdown`

---

## 2. MCP Tools (Actions & Mutations)

### 1. `helix_get_layout`
* **Parameters**:
  * `styleId` *(optional, integer)*: Template style ID. Defaults to active default style.
* **Returns**: Parsed layout tree including rows, columns, and settings.

### 2. `helix_update_row`
* **Description**: Safely update or rearrange columns and module positions in a specified row.
* **Parameters**:
  * `rowId` *(string, required)*: The unique ID or name of the row (e.g., `"header"`, `"title"`, `"custom_banner"`).
  * `columns` *(array, required)*: Array of column definitions:
    * `width` *(integer, 1-12, required)*: Column width in 12-column grid.
    * `position` *(string, optional)*: Assigned Joomla module position name.
    * `customClass` *(string, optional)*: Additional CSS classes.
* **Validation**:
  * Total width of all columns in `columns` array must equal **12**.
  * Module position must be registered in `templateDetails.xml`.

### 3. `helix_scaffold_override`
* **Description**: Generates a standard Helix-compatible layout override in `templates/shaper_helixultimate/html/`.
* **Parameters**:
  * `type` *(enum: `"component"` | `"module"`, required)*: Target extension type.
  * `extension` *(string, required)*: Name of the extension (e.g., `"com_content"`, `"mod_menu"`).
  * `view` *(string, required)*: View or layout directory (e.g., `"article"`, `"category"`).
  * `layoutFile` *(string, optional, default: `"default.php"`)*: File name.

### 4. `helix_scaffold_feature`
* **Description**: Creates a new feature class in `templates/shaper_helixultimate/features/`.
* **Parameters**:
  * `featureName` *(string, required)*: Name of the feature (e.g., `"promo_banner"`).
  * `position` *(string, optional)*: Default position hook.

### 5. `helix_compile_scss`
* **Description**: Compiles `templates/shaper_helixultimate/scss/theme.scss` using embedded Sass engine and verifies compilation.
* **Parameters**:
  * `outputStyle` *(enum: `"expanded"` | `"compressed"`, optional, default: `"expanded"`)*.
* **Returns**: Compilation status, generated CSS byte size, or detailed line-number syntax errors.

### 6. `helix_audit_compatibility`
* **Description**: Audits the template codebase for deprecated jQuery patterns, legacy `JFactory` calls, unescaped output, and missing positions.
* **Parameters**: None.
* **Returns**: Array of detected violations with file paths and line numbers.

### 7. `helix_rollback`
* **Description**: Restores the last recorded snapshot before a mutation tool was executed.
* **Parameters**: None.
* **Returns**: Confirmation and timestamp of restored state.
