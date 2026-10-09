import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { Transport } from '@modelcontextprotocol/sdk/shared/transport.js';
import { JoomlaDetector } from '../core/joomla-detector.js';
import { Logger } from '../core/logger.js';
import { JoomlaDbClient } from '../core/db-client.js';
import { BackupService } from '../domain/backup/backup.service.js';
import { LayoutService } from '../domain/layout/layout.service.js';
import { ScaffoldService } from '../domain/scaffolding/scaffold.service.js';
import { ScssService } from '../domain/scss/scss.service.js';
import { AuditService } from '../domain/audit/audit.service.js';
import { registerResources } from './resources.js';
import { registerTools } from './tools.js';

export interface HelixMcpServerOptions {
  workspacePath?: string;
  verbose?: boolean;
}

export class HelixMcpServer {
  private server: Server;
  private detector: JoomlaDetector;
  private dbClient: JoomlaDbClient;
  private backupService: BackupService;
  private layoutService: LayoutService;
  private scaffoldService: ScaffoldService;
  private scssService: ScssService;
  private auditService: AuditService;

  constructor(options: HelixMcpServerOptions = {}) {
    if (options.verbose) {
      Logger.setVerbose(true);
    }

    this.detector = new JoomlaDetector(options.workspacePath || process.cwd());
    const workspaceRoot = this.detector.getRoot();
    const info = this.detector.detect();

    this.dbClient = new JoomlaDbClient(workspaceRoot);
    this.backupService = new BackupService(workspaceRoot);
    this.layoutService = new LayoutService(workspaceRoot, this.backupService, info.registeredPositions, this.dbClient);
    this.scaffoldService = new ScaffoldService(workspaceRoot);
    this.scssService = new ScssService(workspaceRoot);
    this.auditService = new AuditService(workspaceRoot, this.layoutService, info.registeredPositions);

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
      this.backupService,
      this.scssService,
      this.auditService
    );
  }

  public async start(customTransport?: Transport): Promise<void> {
    const workspaceInfo = this.detector.detect();
    Logger.info(`Connected to Joomla workspace: ${workspaceInfo.joomlaRoot}`, {
      pluginVersion: workspaceInfo.pluginVersion,
      templateVersion: workspaceInfo.templateVersion,
      positionsCount: workspaceInfo.registeredPositions.length,
      dbConfigured: this.dbClient.isConfigured()
    });

    const transport = customTransport || new StdioServerTransport();
    await this.server.connect(transport);
    Logger.info(`Helix Ultimate MCP Server listening on ${customTransport ? 'custom transport' : 'STDIO'}`);
  }

  public getServerInstance(): Server {
    return this.server;
  }
}
