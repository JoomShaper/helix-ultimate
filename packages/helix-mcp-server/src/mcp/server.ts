import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { JoomlaDetector } from '../core/joomla-detector.js';
import { Logger } from '../core/logger.js';
import { registerResources } from './resources.js';

export interface HelixMcpServerOptions {
  workspacePath?: string;
  verbose?: boolean;
}

export class HelixMcpServer {
  private server: Server;
  private detector: JoomlaDetector;

  constructor(options: HelixMcpServerOptions = {}) {
    if (options.verbose) {
      Logger.setVerbose(true);
    }

    this.detector = new JoomlaDetector(options.workspacePath || process.cwd());

    this.server = new Server(
      {
        name: 'helix-ultimate-mcp-server',
        version: '1.0.0'
      },
      {
        capabilities: {
          resources: {},
          tools: {},
          prompts: {}
        }
      }
    );

    // Register MCP Resource Handlers
    registerResources(this.server, this.detector);
  }

  public async start(): Promise<void> {
    Logger.info('Initializing Helix Ultimate MCP Server over STDIO transport');
    const workspaceInfo = this.detector.detect();
    Logger.info(`Connected to Joomla workspace: ${workspaceInfo.joomlaRoot}`, {
      pluginVersion: workspaceInfo.pluginVersion,
      templateVersion: workspaceInfo.templateVersion,
      positionsCount: workspaceInfo.registeredPositions.length
    });

    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    Logger.info('Helix Ultimate MCP Server listening on STDIO');
  }
}
