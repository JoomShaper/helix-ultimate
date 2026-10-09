import { describe, it, expect, beforeAll, afterAll } from 'vitest';
import { Client } from '@modelcontextprotocol/sdk/client/index.js';
import { InMemoryTransport } from '@modelcontextprotocol/sdk/inMemory.js';
import { HelixMcpServer } from '../../src/mcp/server.js';

describe('HelixMcpServer Integration Test (Full Client-Server Roundtrip)', () => {
  let client: Client;
  let server: HelixMcpServer;

  beforeAll(async () => {
    // 1. Create linked in-memory transports
    const [clientTransport, serverTransport] = InMemoryTransport.createLinkedPair();

    // 2. Initialize Helix MCP Server with current workspace
    server = new HelixMcpServer({
      workspacePath: process.cwd()
    });
    await server.start(serverTransport);

    // 3. Initialize MCP Client
    client = new Client(
      {
        name: 'test-ai-client',
        version: '1.0.0'
      },
      {
        capabilities: {}
      }
    );
    await client.connect(clientTransport);
  });

  afterAll(async () => {
    await client.close();
  });

  it('lists all registered resources', async () => {
    const response = await client.listResources();
    expect(response.resources.length).toBeGreaterThanOrEqual(4);

    const uris = response.resources.map(r => r.uri);
    expect(uris).toContain('helix://system/info');
    expect(uris).toContain('helix://positions/registered');
    expect(uris).toContain('helix://presets/all');
    expect(uris).toContain('helix://standards/architecture');
  });

  it('reads helix://system/info resource', async () => {
    const response = await client.readResource({ uri: 'helix://system/info' });
    expect(response.contents).toHaveLength(1);

    const data = JSON.parse(response.contents[0].text as string);
    expect(data.joomlaRoot).toBeDefined();
    expect(data.template.path).toContain('templates/shaper_helixultimate');
    expect(data.plugin.path).toContain('plugins/system/helixultimate');
  });

  it('reads helix://positions/registered resource', async () => {
    const response = await client.readResource({ uri: 'helix://positions/registered' });
    expect(response.contents).toHaveLength(1);

    const data = JSON.parse(response.contents[0].text as string);
    expect(data.totalPositions).toBeGreaterThan(0);
    expect(Array.isArray(data.positions)).toBe(true);
  });

  it('lists all available MCP tools', async () => {
    const response = await client.listTools();
    const toolNames = response.tools.map(t => t.name);

    expect(toolNames).toContain('helix_get_layout');
    expect(toolNames).toContain('helix_update_row');
    expect(toolNames).toContain('helix_scaffold_override');
    expect(toolNames).toContain('helix_scaffold_feature');
    expect(toolNames).toContain('helix_compile_scss');
    expect(toolNames).toContain('helix_audit_compatibility');
    expect(toolNames).toContain('helix_rollback');
  });

  it('invokes helix_get_layout tool', async () => {
    const result = await client.callTool({
      name: 'helix_get_layout',
      arguments: {}
    });

    expect(result.isError).toBeFalsy();
    const content = result.content as Array<{ type: string; text: string }>;
    expect(content[0].text).toBeDefined();

    const layout = JSON.parse(content[0].text);
    expect(Array.isArray(layout)).toBe(true);
    expect(layout.length).toBeGreaterThan(0);
  });

  it('invokes helix_audit_compatibility tool', async () => {
    const result = await client.callTool({
      name: 'helix_audit_compatibility',
      arguments: {}
    });

    expect(result.isError).toBeFalsy();
    const content = result.content as Array<{ type: string; text: string }>;
    const report = JSON.parse(content[0].text);

    expect(report.timestamp).toBeDefined();
    expect(report.totalFilesScanned).toBeGreaterThan(0);
    expect(Array.isArray(report.violations)).toBe(true);
  });
});
