# `@joomshaper/helix-mcp-server`

Production-grade Model Context Protocol (MCP) server for the **Helix Ultimate** Joomla framework.

## Installation & Running

### Using NPX (Recommended)
```bash
npx -y @joomshaper/helix-mcp-server --path /path/to/helixultimatedev
```

### Local Development
```bash
cd packages/helix-mcp-server
npm install
npm run build
npm start -- --path ../..
```

## Available Resources
- `helix://system/info`: Workspace status, versions of Helix plugin & template.
- `helix://positions/registered`: Module positions defined in `templateDetails.xml`.
- `helix://standards/architecture`: Helix Ultimate coding rules and architectural constraints.
