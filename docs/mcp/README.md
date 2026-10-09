# Helix Ultimate MCP Architecture & Integration

## 1. Overview
The Model Context Protocol (MCP) server for Helix Ultimate (`@joomshaper/helix-mcp-server`) provides a standardized interface allowing AI assistants (Claude, Cursor, Antigravity, Cline) to safely inspect, scaffold, compile, and manipulate Joomla templates and system plugins powered by Helix Ultimate.

---

## 2. Documentation Directory

- **[Branching Strategy](branching-strategy.md)**: Git branching workflow, naming conventions, and PR milestones.
- **[Architecture Specification](architecture.md)**: System design, process lifecycle, runtime guardrails, and data flow.
- **[Protocol Specification](specification.md)**: Schemas and contracts for all MCP Resources, Tools, and Prompts.
- **[Client Setup Guide](client-setup.md)**: Configuration instructions for Claude Desktop, Cursor, and Antigravity.

---

## 3. Key Capabilities

```mermaid
graph LR
    subgraph AI Assistants
        Claude[Claude Desktop]
        Cursor[Cursor IDE]
        Antigravity[Antigravity IDE]
    end

    subgraph MCP Server ["@joomshaper/helix-mcp-server"]
        Resources["Resources<br/>(Layout, Presets, Info)"]
        Tools["Tools<br/>(Grid Editor, Scaffolder, SCSS)"]
        Safety["Guardrails<br/>(Backups, Grid Check, Stderr Log)"]
    end

    subgraph Helix Codebase
        Plugin["plg_system_helixultimate"]
        Template["shaper_helixultimate"]
        JoomlaDB[("Joomla DB / #__template_styles")]
    end

    Claude <--> MCP Server
    Cursor <--> MCP Server
    Antigravity <--> MCP Server

    MCP Server <--> Plugin
    MCP Server <--> Template
    MCP Server <--> JoomlaDB
```

1. **Intelligent Grid Management**: Read and update Helix 12-column layouts without manual JSON editing.
2. **Standardized Code Scaffolding**: Generate PHP 8 / Bootstrap 5 overrides, custom Helix features, and form fields adhering strictly to Joomla 4/5/6 standards.
3. **Embedded SCSS Compilation**: Compile `theme.scss` directly and receive line-numbered error reports within the chat.
4. **Safety & Rollbacks**: Automatic JSON/file snapshots before mutations, with instant one-step rollback.
