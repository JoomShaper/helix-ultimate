# AI Client Setup Guide for Helix MCP Server

This guide explains how to connect `@joomshaper/helix-mcp-server` to popular AI clients and IDEs.

---

## 1. Claude Desktop

Add the following to your Claude Desktop configuration file:
- **macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
- **Windows**: `%APPDATA%\Claude\claude_desktop_config.json`

```json
{
  "mcpServers": {
    "helix-ultimate": {
      "command": "npx",
      "args": [
        "-y",
        "@joomshaper/helix-mcp-server",
        "--path",
        "/absolute/path/to/helixultimatedev"
      ]
    }
  }
}
```

---

## 2. Cursor IDE

In your project root, add or update `.cursor/mcp.json`:

```json
{
  "mcpServers": {
    "helix-ultimate": {
      "command": "node",
      "args": ["./packages/helix-mcp-server/dist/cli.js"]
    }
  }
}
```

---

## 3. Google Antigravity IDE

Add to your workspace or global `mcp_config.json`:

```json
{
  "mcpServers": {
    "helix-ultimate": {
      "command": "node",
      "args": [
        "/Users/siddiqur/Sites/helixultimatedev/packages/helix-mcp-server/dist/cli.js",
        "--path",
        "/Users/siddiqur/Sites/helixultimatedev"
      ]
    }
  }
}
```

---

## 4. Verification & Testing

To test the server independently before connecting to an AI client, use the official MCP Inspector:

```bash
npx @modelcontextprotocol/inspector node packages/helix-mcp-server/dist/cli.js --path .
```
