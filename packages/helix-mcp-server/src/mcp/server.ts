import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { JoomlaDetector } from '../core/joomla-detector.js';
import { Logger } from '../core/logger.js';
import { BackupService } from '../domain/backup/backup.service.js';
import { LayoutService } from '../domain/layout/layout.service.js';
import { ScaffoldService } from '../domain/scaffolding/scaffold.service.js';
import { registerResources } from './resources.js';
import { registerTools } from './tools.js';

export interface HelixMcpServerOptions {
  workspacePath?: string;
  verbose?: boolean;
}

export class HelixMcpServer {
  private server: Server;
  private detector: JoomlaDetector;
  private backupService: BackupService;
  private layoutService: LayoutService;
  private scaffoldService: ScaffoldService;

  constructor(options: HelixMcpServerOptions = {}) {
    if (options.verbose) {
      Logger.setVerbose(true);
    }

    this.detector = new JoomlaDetector(options.workspacePath || process.cwd());
    const workspaceRoot = this.detector.getRoot();
    const info = this.detector.detect();

    this.backupService = new BackupService(workspaceRoot);
    this.layoutService = new LayoutService(workspaceRoot, this.backupService, info.registeredPositions);
    this.scaffoldService = new ScaffoldService(workspaceRoot);

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

    // Register MCP Tool Handlers
    registerTools(
      this.server,
      this.layoutService,
      this.scaffoldService,
      this.backupService
    );
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
