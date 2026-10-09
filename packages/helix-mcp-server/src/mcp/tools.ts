import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import {
  ListToolsRequestSchema,
  CallToolRequestSchema
} from '@modelcontextprotocol/sdk/types.js';
import { z } from 'zod';
import { LayoutService } from '../domain/layout/layout.service.js';
import { ScaffoldService } from '../domain/scaffolding/scaffold.service.js';
import { BackupService } from '../domain/backup/backup.service.js';
import { ScssService } from '../domain/scss/scss.service.js';
import { AuditService } from '../domain/audit/audit.service.js';
import { Logger } from '../core/logger.js';

export function registerTools(
  server: Server,
  layoutService: LayoutService,
  scaffoldService: ScaffoldService,
  backupService: BackupService,
  scssService: ScssService,
  auditService: AuditService
): void {
  // 1. Tool Listing
  server.setRequestHandler(ListToolsRequestSchema, async () => {
    Logger.debug('Listing MCP tools');
    return {
      tools: [
        {
          name: 'helix_get_layout',
          description: 'Retrieves the complete Helix Ultimate layout grid tree with all rows, columns, and assigned module positions from the active Joomla template style.',
          inputSchema: {
            type: 'object',
            properties: {
              styleId: {
                type: 'integer',
                description: 'Optional Joomla template style ID (defaults to active/default style)'
              }
            }
          }
        },
        {
          name: 'helix_update_row',
          description: 'Updates an existing row or inserts a new row in the Helix Ultimate layout builder (synced with Joomla database). Enforces Bootstrap 12-column grid rule.',
          inputSchema: {
            type: 'object',
            properties: {
              rowId: {
                type: 'string',
                description: 'Unique name or numeric index of the row to update (e.g. "Main Body", "Header", "0")'
              },
              columns: {
                type: 'array',
                description: 'Array of columns. Total sum of widths must equal exactly 12.',
                items: {
                  type: 'object',
                  properties: {
                    width: {
                      type: 'integer',
                      minimum: 1,
                      maximum: 12,
                      description: 'Column grid size (1-12)'
                    },
                    position: {
                      type: 'string',
                      description: 'Assigned Joomla module position name (e.g. "left", "right", "top1")'
                    },
                    customClass: {
                      type: 'string',
                      description: 'Optional CSS class for the column'
                    },
                    isComponent: {
                      type: 'boolean',
                      description: 'Set to true if this column renders the main Joomla component body'
                    }
                  },
                  required: ['width']
                }
              },
              rowSettings: {
                type: 'object',
                description: 'Optional row settings (fluidrow, custom_class, name, etc.)'
              },
              insertAbove: {
                type: 'string',
                description: 'Insert this row immediately above the named row (e.g. "Header")'
              },
              insertBelow: {
                type: 'string',
                description: 'Insert this row immediately below the named row'
              },
              styleId: {
                type: 'integer',
                description: 'Optional Joomla template style ID to modify'
              }
            },
            required: ['rowId', 'columns']
          }
        },
        {
          name: 'helix_scaffold_override',
          description: 'Generates a clean Joomla 4/5/6 template override in templates/shaper_helixultimate/html/ adhering to Helix Ultimate standards.',
          inputSchema: {
            type: 'object',
            properties: {
              type: {
                type: 'string',
                enum: ['component', 'module'],
                description: 'Target extension type'
              },
              extension: {
                type: 'string',
                description: 'Extension name (e.g., com_content, mod_menu)'
              },
              view: {
                type: 'string',
                description: 'View name (e.g., article, category)'
              },
              layoutFile: {
                type: 'string',
                description: 'Layout filename (defaults to default.php)'
              }
            },
            required: ['type', 'extension', 'view']
          }
        },
        {
          name: 'helix_scaffold_feature',
          description: 'Creates a new Helix Ultimate feature class in templates/shaper_helixultimate/features/.',
          inputSchema: {
            type: 'object',
            properties: {
              featureName: {
                type: 'string',
                description: 'Name of the feature (e.g. "promo_bar", "cookie_consent")'
              },
              position: {
                type: 'string',
                description: 'Default position hook'
              }
            },
            required: ['featureName']
          }
        },
        {
          name: 'helix_compile_scss',
          description: 'Compiles template SCSS (theme.scss) using embedded Sass engine and returns detailed syntax diagnostics or byte size.',
          inputSchema: {
            type: 'object',
            properties: {
              targetFile: {
                type: 'string',
                description: 'Optional relative path to target SCSS file (defaults to templates/shaper_helixultimate/scss/theme.scss)'
              },
              outputStyle: {
                type: 'string',
                enum: ['expanded', 'compressed'],
                description: 'CSS output style format'
              }
            }
          }
        },
        {
          name: 'helix_audit_compatibility',
          description: 'Audits template codebase for legacy jQuery calls, deprecated JFactory / JRequest methods, and unregistered layout positions.',
          inputSchema: {
            type: 'object',
            properties: {
              targetDir: {
                type: 'string',
                description: 'Optional relative directory to scan (defaults to templates/shaper_helixultimate)'
              }
            }
          }
        },
        {
          name: 'helix_rollback',
          description: 'Reverts the most recent file or layout mutation using an atomic snapshot backup.',
          inputSchema: {
            type: 'object',
            properties: {}
          }
        }
      ]
    };
  });

  // 2. Tool Execution
  server.setRequestHandler(CallToolRequestSchema, async (request) => {
    const { name, arguments: args } = request.params;
    Logger.info(`Tool invocation: ${name}`, { args });

    try {
      if (name === 'helix_get_layout') {
        const schema = z.object({
          styleId: z.number().int().optional()
        });
        const parsed = schema.parse(args || {});
        const layout = await layoutService.getLayout(parsed.styleId);
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify(layout, null, 2)
            }
          ]
        };
      }

      if (name === 'helix_update_row') {
        const schema = z.object({
          rowId: z.string(),
          columns: z.array(
            z.object({
              width: z.number().int().min(1).max(12),
              position: z.string().optional(),
              customClass: z.string().optional(),
              isComponent: z.boolean().optional()
            })
          ),
          rowSettings: z.record(z.any()).optional(),
          insertAbove: z.string().optional(),
          insertBelow: z.string().optional(),
          styleId: z.number().int().optional()
        });

        const parsed = schema.parse(args);
        const result = await layoutService.updateRow({
          rowId: parsed.rowId,
          columns: parsed.columns,
          rowSettings: parsed.rowSettings,
          insertAbove: parsed.insertAbove,
          insertBelow: parsed.insertBelow,
          styleId: parsed.styleId
        });

        return {
          content: [
            {
              type: 'text',
              text: `Successfully updated row '${parsed.rowId}'. Saved to database and options.json. Snapshot '${result.snapshotId}' created for rollback.`
            }
          ]
        };
      }

      if (name === 'helix_scaffold_override') {
        const schema = z.object({
          type: z.enum(['component', 'module']),
          extension: z.string(),
          view: z.string(),
          layoutFile: z.string().optional()
        });

        const parsed = schema.parse(args);
        const result = scaffoldService.scaffoldOverride(parsed);

        return {
          content: [
            {
              type: 'text',
              text: `Successfully created template override at: ${result.relativePath}`
            }
          ]
        };
      }

      if (name === 'helix_scaffold_feature') {
        const schema = z.object({
          featureName: z.string(),
          position: z.string().optional()
        });

        const parsed = schema.parse(args);
        const result = scaffoldService.scaffoldFeature(parsed);

        return {
          content: [
            {
              type: 'text',
              text: `Successfully created Helix feature at: ${result.relativePath}`
            }
          ]
        };
      }

      if (name === 'helix_compile_scss') {
        const schema = z.object({
          targetFile: z.string().optional(),
          outputStyle: z.enum(['expanded', 'compressed']).optional()
        });

        const parsed = schema.parse(args || {});
        const result = scssService.compile(parsed);

        if (result.success) {
          return {
            content: [
              {
                type: 'text',
                text: `SCSS compiled successfully in ${result.durationMs}ms (${result.cssSize} bytes).`
              }
            ]
          };
        } else {
          return {
            isError: true,
            content: [
              {
                type: 'text',
                text: `SCSS compilation failed:\n${result.error?.message}\nLine: ${result.error?.line}, Column: ${result.error?.column}`
              }
            ]
          };
        }
      }

      if (name === 'helix_audit_compatibility') {
        const schema = z.object({
          targetDir: z.string().optional()
        });

        const parsed = schema.parse(args || {});
        const report = await auditService.runAudit(parsed.targetDir);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify(report, null, 2)
            }
          ]
        };
      }

      if (name === 'helix_rollback') {
        const result = backupService.rollbackLatest();

        // If options.json was restored, also synchronize the database
        if (result.restoredFile.includes('options.json')) {
          try {
            const restoredLayout = await layoutService.getLayout();
            await layoutService.saveLayout(restoredLayout, 'sync_rollback');
          } catch (syncErr) {
            Logger.warn(`Could not sync restored layout to DB: ${syncErr}`);
          }
        }

        return {
          content: [
            {
              type: 'text',
              text: `Successfully restored file '${result.restoredFile}' from snapshot '${result.snapshotId}' and synchronized database.`
            }
          ]
        };
      }

      throw new Error(`Unknown tool: ${name}`);
    } catch (error: any) {
      Logger.error(`Error in tool '${name}': ${error?.message || error}`);
      return {
        isError: true,
        content: [
          {
            type: 'text',
            text: `Error: ${error?.message || String(error)}`
          }
        ]
      };
    }
  });
}
