# Git Branching & Contribution Strategy for Helix MCP

## 1. Branch Hierarchy

To ensure production stability and zero regressions in the main template and system plugin code, development follows a structured branching model:

```
dev (Base development branch)
 │
 └── feature/helix-mcp-server (Epic branch)
      │
      ├── feat/mcp-core-foundation    (Milestone 1: Server runtime, logger, detector, read resources)
      ├── feat/mcp-layout-scaffold     (Milestone 2: Grid validation, updater, code scaffolders)
      ├── feat/mcp-scss-diagnostics    (Milestone 3: Embedded Sass compiler, compatibility auditor)
      └── feat/mcp-backup-hardening    (Milestone 4: Snapshot rollbacks, test suites, CLI packaging)
```

---

## 2. Branch Roles & Merge Criteria

### Base Branch: `dev`
- The shared target branch for upcoming releases of Helix Ultimate.
- Only receives code from `feature/helix-mcp-server` after complete milestone validation and testing.

### Epic Branch: `feature/helix-mcp-server`
- Root branch for all MCP-related work.
- Contains all documentation in `docs/mcp/` and package source in `packages/helix-mcp-server/`.

### Milestone Sub-branches
All work is executed on granular sub-branches merged via Pull Requests into `feature/helix-mcp-server`:

| Branch Name | Scope | Definition of Done |
| :--- | :--- | :--- |
| `feat/mcp-core-foundation` | Node/TS setup, STDIO transport, stderr logger, Joomla root detector, basic resources (`helix://system/info`, `helix://layout/active`). | Successfully connects via MCP Inspector / STDIO client; returns valid JSON. |
| `feat/mcp-layout-scaffold` | Layout validator (sum == 12 columns), `helix_update_row`, `helix_scaffold_override`, `helix_scaffold_feature`. | Unit tests pass for column validation and code generation. |
| `feat/mcp-scss-diagnostics` | Embedded Sass compiler, AST variable extractor, jQuery/JFactory compatibility checker. | Can compile `theme.scss` and catch SCSS syntax errors. |
| `feat/mcp-backup-hardening` | Snapshot manager, `helix_rollback` tool, Vitest integration test suite, CLI binary packaging (`tsup`). | 100% test pass rate, CLI runs via `npx` without crashing. |

---

## 3. Commit Message Convention

Follow standard Conventional Commits:

- `feat(mcp): add layout grid validator with 12-column enforcement`
- `fix(mcp): prevent stdout pollution in stdio transport logger`
- `docs(mcp): add client setup instructions for Claude Desktop`
- `test(mcp): add integration test for template override scaffolding`
