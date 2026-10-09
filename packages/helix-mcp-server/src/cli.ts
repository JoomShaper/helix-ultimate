import { Command } from 'commander';
import { HelixMcpServer } from './mcp/server.js';
import { Logger } from './core/logger.js';

const program = new Command();

program
  .name('helix-mcp-server')
  .description('Production Model Context Protocol (MCP) server for Helix Ultimate')
  .version('1.0.0')
  .option('-p, --path <workspacePath>', 'Path to Joomla/Helix workspace root', process.cwd())
  .option('-v, --verbose', 'Enable verbose debug logging (to stderr)', false)
  .action(async (options) => {
    try {
      const server = new HelixMcpServer({
        workspacePath: options.path,
        verbose: options.verbose
      });
      await server.start();
    } catch (error) {
      Logger.error(`Fatal error running Helix MCP Server: ${error}`);
      process.exit(1);
    }
  });

program.parse(process.argv);
