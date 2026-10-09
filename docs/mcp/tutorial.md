# Supercharge Your Joomla Workflow with AI: The Official Helix Ultimate MCP Tutorial

Welcome to the future of Joomla template development! Today, we are thrilled to introduce official **Model Context Protocol (MCP)** support for the **Helix Ultimate** framework.

Helix Ultimate is the first Joomla framework in the world to feature native, production-grade MCP integration. With this release, AI assistants like **Cursor**, **Claude Desktop**, **Antigravity**, and **VS Code (Cline)** can directly understand, scaffold, and safely manipulate your Helix Ultimate templates in real time.

---

## What is Model Context Protocol (MCP)?

**Model Context Protocol (MCP)** is an open standard developed by Anthropic that allows AI applications (such as Claude and Cursor) to connect directly to external developer tools, frameworks, and databases.

Instead of copying and pasting code back and forth or manually clicking through the Joomla administrator UI, your AI assistant can now:
* **Read live layouts** and module positions directly from your Joomla database.
* **Validate 12-column grid rules** before applying layout changes.
* **Scaffold modern PHP 8 overrides and Helix features** adhering to Joomla 4, 5, and 6 standards.
* **Compile SCSS** and catch syntax errors before you even refresh your browser.
* **Audit your codebase** for legacy Joomla 3 APIs or deprecated jQuery calls.
* **Safely revert changes** with pre-mutation snapshot backups.

---

## 🚀 60-Second Quickstart Setup

You don't need to clone any repositories or install build tools. The server runs instantly via `npx`.

### Option A: Cursor IDE Setup (Recommended)

1. Open your Joomla / Helix Ultimate project in Cursor.
2. In the root directory of your project, create or open `.cursor/mcp.json`.
3. Paste the following configuration:

```json
{
  "mcpServers": {
    "helix-ultimate": {
      "command": "npx",
      "args": [
        "-y",
        "@joomshaper/helix-mcp-server"
      ]
    }
  }
}
```
4. Save the file. Cursor will automatically start the server and register all tools.

---

### Option B: Claude Desktop Setup

1. Open Claude Desktop Settings (`Settings` ➔ `Developer` ➔ `Edit Config`).
2. Add `helix-ultimate` to your `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "helix-ultimate": {
      "command": "npx",
      "args": [
        "-y",
        "@joomshaper/helix-mcp-server",
        "--path",
        "/absolute/path/to/your/joomla/site"
      ]
    }
  }
}
```
3. Restart Claude Desktop. You will see the hammer (tools) icon with 7 Helix Ultimate tools!

---

## 🛠️ Real-World Walkthrough: 5 Practical Examples

Once connected, you can talk to your AI assistant using everyday language. Here are real prompts you can use right now:

### 1. Inspect Your Active Template Layout
> **Prompt**: *"What module positions are currently assigned to my layout?"*

**What happens**: The AI calls `helix_get_layout`, queries your active template style from the MySQL database or options file, and lists all active rows and module positions (`top1`, `left`, `right`, `footer1`, etc.).

---

### 2. Add or Reorder Rows with 12-Column Grid Validation
> **Prompt**: *"Add a 3-column Top Bar row above Header with positions top1, top2, and top3."*

**What happens**: 
1. The AI calculates column widths: `4 + 4 + 4 = 12`.
2. The MCP validator verifies that the grid sums to 12.
3. The server takes an atomic backup snapshot in `.helix-mcp/snapshots/`.
4. The database (`#__template_styles.params`) and `options.json` are synchronized immediately.
5. Refresh your browser, and the row is live!

---

### 3. Scaffold a New Template Override
> **Prompt**: *"Create a template override for com_content article view."*

**What happens**: The AI executes `helix_scaffold_override`. In 1 second, it creates `templates/shaper_helixultimate/html/com_content/article/default.php` formatted with strict Joomla 4/5/6 conventions, Bootstrap 5 markup, and modern namespaced classes.

---

### 4. Scaffold a Reusable Helix Feature
> **Prompt**: *"Scaffold a new Helix feature called 'announcement_bar'."*

**What happens**: The AI invokes `helix_scaffold_feature`. It creates `templates/shaper_helixultimate/features/announcement_bar.php` with:
* Clean PHP 8 class structure (`HelixUltimateFeatureAnnouncementBar`).
* Standard `init()` and `render()` lifecycle methods.
* Automatic parameter binding (`announcement_bar_enable`).
* Bootstrap 5 flex utility styling.

---

### 5. Audit for Joomla 5 & 6 Compatibility
> **Prompt**: *"Audit my template for deprecated Joomla 3 methods or legacy jQuery."*

**What happens**: The AI runs `helix_audit_compatibility`. It scans your template PHP and JS files and identifies:
* Deprecated calls like `JFactory::getApplication()` or `JRequest::getVar()`.
* Legacy `$(document).ready()` or `$.ajax()` calls.
* Unregistered module positions that need to be added to `templateDetails.xml`.

---

## 🛡️ Zero-Risk Development: Instant Rollbacks

Made a mistake or don't like the new layout? You don't have to restore a database backup manually.

> **Prompt**: *"Rollback the last change."*

The AI invokes `helix_rollback`, restores the latest snapshot from `.helix-mcp/snapshots/`, and re-synchronizes the database automatically.

---

## 📋 Full Tool Reference Table

| Tool Name | Purpose |
| :--- | :--- |
| **`helix_get_layout`** | Retrieves active row and column structure. |
| **`helix_update_row`** | Updates/inserts rows with 12-column validation & DB sync. |
| **`helix_scaffold_override`** | Generates Joomla component/module overrides in `html/`. |
| **`helix_scaffold_feature`** | Scaffolds new feature classes in `features/`. |
| **`helix_compile_scss`** | Compiles `theme.scss` with embedded Dart Sass engine. |
| **`helix_audit_compatibility`** | Flags legacy Joomla 3 globals and jQuery usage. |
| **`helix_rollback`** | Restores the previous file state and database parameters. |

---

## Summary

With Helix Ultimate's official MCP integration, Joomla template development is now faster, safer, and powered by modern generative AI workflows.

* **NPM Package**: [`@joomshaper/helix-mcp-server`](https://www.npmjs.com/package/@joomshaper/helix-mcp-server)
* **Anthropic Registry**: Active on [`registry.modelcontextprotocol.io`](https://registry.modelcontextprotocol.io/)
* **Repository**: [GitHub - JoomShaper/helix-ultimate](https://github.com/JoomShaper/helix-ultimate)

Give it a try in your favorite AI IDE today!
