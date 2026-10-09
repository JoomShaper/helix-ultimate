import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import {
  ListResourcesRequestSchema,
  ReadResourceRequestSchema
} from '@modelcontextprotocol/sdk/types.js';
import { JoomlaDetector } from '../core/joomla-detector.js';
import { Logger } from '../core/logger.js';

export function registerResources(server: Server, detector: JoomlaDetector): void {
  // 1. List available resources
  server.setRequestHandler(ListResourcesRequestSchema, async () => {
    Logger.debug('Listing MCP resources');
    return {
      resources: [
        {
          uri: 'helix://system/info',
          name: 'Helix System Information',
          mimeType: 'application/json',
          description: 'Workspace root, Helix plugin version, template version, and configuration state'
        },
        {
          uri: 'helix://positions/registered',
          name: 'Helix Registered Module Positions',
          mimeType: 'application/json',
          description: 'All module positions officially declared in templateDetails.xml'
        },
        {
          uri: 'helix://presets/all',
          name: 'Helix Style Presets',
          mimeType: 'application/json',
          description: 'Color schemes and style presets configured in Helix Ultimate template'
        },
        {
          uri: 'helix://standards/architecture',
          name: 'Helix Architecture & Coding Rules',
          mimeType: 'text/markdown',
          description: 'Core architectural guidelines for Helix Ultimate: Vanilla JS, SCSS, Bootstrap 5, Joomla namespaces'
        }
      ]
    };
  });

  // 2. Read resource handler
  server.setRequestHandler(ReadResourceRequestSchema, async (request) => {
    const { uri } = request.params;
    Logger.debug(`Reading MCP resource: ${uri}`);

    const info = detector.detect();

    if (uri === 'helix://system/info') {
      return {
        contents: [
          {
            uri,
            mimeType: 'application/json',
            text: JSON.stringify(
              {
                joomlaRoot: info.joomlaRoot,
                hasConfiguration: info.hasConfiguration,
                plugin: {
                  path: info.pluginPath,
                  version: info.pluginVersion
                },
                template: {
                  path: info.templatePath,
                  version: info.templateVersion
                },
                nodeVersion: process.version,
                platform: process.platform
              },
              null,
              2
            )
          }
        ]
      };
    }

    if (uri === 'helix://positions/registered') {
      return {
        contents: [
          {
            uri,
            mimeType: 'application/json',
            text: JSON.stringify(
              {
                totalPositions: info.registeredPositions.length,
                positions: info.registeredPositions
              },
              null,
              2
            )
          }
        ]
      };
    }

    if (uri === 'helix://presets/all') {
      let presetsData: Record<string, unknown> = {};
      const optionsRel = 'templates/shaper_helixultimate/options.json';
      if (detector.fileExists(optionsRel)) {
        try {
          const raw = detector.readFile(optionsRel);
          const parsed = JSON.parse(raw);
          if (parsed['presets-data']) {
            presetsData = typeof parsed['presets-data'] === 'string'
              ? JSON.parse(parsed['presets-data'])
              : parsed['presets-data'];
          }
        } catch (err) {
          Logger.warn(`Failed to parse presets from ${optionsRel}: ${err}`);
        }
      }

      return {
        contents: [
          {
            uri,
            mimeType: 'application/json',
            text: JSON.stringify(presetsData, null, 2)
          }
        ]
      };
    }

    if (uri === 'helix://standards/architecture') {
      let standardsDoc = '';
      if (detector.fileExists('AGENTS.md')) {
        standardsDoc = detector.readFile('AGENTS.md');
      } else {
        standardsDoc = `# Helix Ultimate Coding Standards\n- SCSS over CSS\n- Vanilla JS (ES6+) over jQuery\n- Modern Joomla 4/5/6 namespaces\n- Bootstrap 5 utility classes`;
      }

      return {
        contents: [
          {
            uri,
            mimeType: 'text/markdown',
            text: standardsDoc
          }
        ]
      };
    }

    throw new Error(`Resource not found: ${uri}`);
  });
}
