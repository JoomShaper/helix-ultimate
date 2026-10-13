import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import {
  ListToolsRequestSchema,
  CallToolRequestSchema
} from '@modelcontextprotocol/sdk/types.js';
import { z } from 'zod';
import { LayoutService } from '../domain/layout/layout.service.js';
import { MegaMenuService } from '../domain/megamenu/megamenu.service.js';
import { ScaffoldService } from '../domain/scaffolding/scaffold.service.js';
import { BackupService } from '../domain/backup/backup.service.js';
import { ScssService } from '../domain/scss/scss.service.js';
import { AuditService } from '../domain/audit/audit.service.js';
import { Logger } from '../core/logger.js';
import { MegaMenuRow } from '../domain/megamenu/megamenu.types.js';

export function registerTools(
  server: Server,
  layoutService: LayoutService,
  megaMenuService: MegaMenuService,
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
              },
              template: {
                type: 'string',
                description: 'Optional template folder name (e.g. "shaper_helixultimate", "shaper_educon"). Defaults to active Helix template.'
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
              },
              template: {
                type: 'string',
                description: 'Optional template folder name (e.g. "shaper_helixultimate", "shaper_educon"). Defaults to active Helix template.'
              }
            },
            required: ['rowId', 'columns']
          }
        },
        {
          name: 'helix_get_megamenu',
          description: 'Retrieves mega menu configuration, dropdown width, and row/column grid layouts for a specific menu item ID, or all configured mega menus.',
          inputSchema: {
            type: 'object',
            properties: {
              menuItemId: {
                type: 'string',
                description: 'Optional Joomla menu item ID (e.g. "101"). If omitted, returns all mega menu configurations.'
              },
              styleId: {
                type: 'integer',
                description: 'Optional Joomla template style ID'
              },
              template: {
                type: 'string',
                description: 'Optional template folder name. Defaults to active Helix template.'
              }
            }
          }
        },
        {
          name: 'helix_update_megamenu',
          description: 'Configures or updates a Helix Ultimate Mega Menu layout for a specific menu item ID. Enforces Bootstrap 12-column grid rule per row and syncs with MySQL.',
          inputSchema: {
            type: 'object',
            properties: {
              menuItemId: {
                type: 'string',
                description: 'Joomla menu item ID to configure (e.g. "101")'
              },
              enabled: {
                type: 'boolean',
                description: 'Whether mega menu dropdown is enabled (true/false)'
              },
              width: {
                type: 'string',
                description: 'Dropdown width (e.g. "600px", "800px", "100%")'
              },
              align: {
                type: 'string',
                enum: ['left', 'right', 'center', 'full'],
                description: 'Mega menu alignment'
              },
              dropdown: {
                type: 'string',
                enum: ['left', 'right'],
                description: 'Dropdown direction'
              },
              badge: {
                type: 'string',
                description: 'Optional badge text (e.g. "Hot", "New")'
              },
              badgePosition: {
                type: 'string',
                enum: ['top', 'bottom'],
                description: 'Badge position'
              },
              badgeBgColor: {
                type: 'string',
                description: 'Badge background color hex (e.g. "#ec430f")'
              },
              badgeTextColor: {
                type: 'string',
                description: 'Badge text color hex (e.g. "#ffffff")'
              },
              icon: {
                type: 'string',
                description: 'FontAwesome icon class (e.g. "fa fa-star")'
              },
              customClass: {
                type: 'string',
                description: 'Custom CSS class'
              },
              rows: {
                type: 'array',
                description: 'Array of rows for the mega menu grid layout. Each row contains columns whose colGrid values must sum to 12.',
                items: {
                  type: 'object',
                  properties: {
                    columns: {
                      type: 'array',
                      items: {
                        type: 'object',
                        properties: {
                          colGrid: {
                            type: 'string',
                            description: 'Column grid width ("1" to "12")'
                          },
                          moduleId: {
                            type: 'string',
                            description: 'Optional Joomla module ID rendered in this column'
                          },
                          items: {
                            type: 'array',
                            description: 'Array of child menu items or modules in this column',
                            items: {
                              type: 'object',
                              properties: {
                                type: {
                                  type: 'string',
                                  enum: ['menu_item', 'module']
                                },
                                id: {
                                  type: 'string',
                                  description: 'Item ID or Module ID'
                                }
                              },
                              required: ['type', 'id']
                            }
                          }
                        },
                        required: ['colGrid']
                      }
                    }
                  },
                  required: ['columns']
                }
              },
              styleId: {
                type: 'integer',
                description: 'Optional Joomla template style ID'
              },
              template: {
                type: 'string',
                description: 'Optional template folder name. Defaults to active Helix template.'
              }
            },
            required: ['menuItemId']
          }
        },
        {
          name: 'helix_scaffold_override',
          description: 'Generates a clean Joomla 4/5/6 template override inside the active or specified Helix template HTML directory.',
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
              },
              template: {
                type: 'string',
                description: 'Optional target template folder name (e.g. "shaper_helixultimate", "shaper_raw"). Defaults to active Helix template.'
              }
            },
            required: ['type', 'extension', 'view']
          }
        },
        {
          name: 'helix_scaffold_feature',
          description: 'Creates a new Helix Ultimate feature class inside the active or specified Helix template features directory.',
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
              },
              template: {
                type: 'string',
                description: 'Optional target template folder name. Defaults to active Helix template.'
              }
            },
            required: ['featureName']
          }
        },
        {
          name: 'helix_compile_scss',
          description: 'Compiles template SCSS using embedded Sass engine and returns detailed syntax diagnostics or byte size.',
          inputSchema: {
            type: 'object',
            properties: {
              targetFile: {
                type: 'string',
                description: 'Optional relative path to target SCSS file'
              },
              template: {
                type: 'string',
                description: 'Optional target template folder name (e.g. "shaper_helixultimate", "shaper_educon"). Defaults to active Helix template.'
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
                description: 'Optional relative directory to scan (defaults to active Helix template directory)'
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
          styleId: z.number().int().optional(),
          template: z.string().optional()
        });
        const parsed = schema.parse(args || {});
        const layout = await layoutService.getLayout(parsed.styleId, parsed.template);
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
          styleId: z.number().int().optional(),
          template: z.string().optional()
        });

        const parsed = schema.parse(args);
        const result = await layoutService.updateRow({
          rowId: parsed.rowId,
          columns: parsed.columns,
          rowSettings: parsed.rowSettings,
          insertAbove: parsed.insertAbove,
          insertBelow: parsed.insertBelow,
          styleId: parsed.styleId,
          templateName: parsed.template
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

      if (name === 'helix_get_megamenu') {
        const schema = z.object({
          menuItemId: z.string().optional(),
          styleId: z.number().int().optional(),
          template: z.string().optional()
        });

        const parsed = schema.parse(args || {});
        if (parsed.menuItemId) {
          const item = await megaMenuService.getMenuItemSettings(parsed.menuItemId, parsed.styleId, parsed.template);
          return {
            content: [
              {
                type: 'text',
                text: JSON.stringify(item || { megamenu: 0, message: `No mega menu configured for item #${parsed.menuItemId}` }, null, 2)
              }
            ]
          };
        } else {
          const map = await megaMenuService.getMegaMenuMap(parsed.styleId, parsed.template);
          return {
            content: [
              {
                type: 'text',
                text: JSON.stringify(map, null, 2)
              }
            ]
          };
        }
      }

      if (name === 'helix_update_megamenu') {
        const schema = z.object({
          menuItemId: z.string(),
          enabled: z.boolean().optional(),
          width: z.string().optional(),
          align: z.enum(['left', 'right', 'center', 'full']).optional(),
          dropdown: z.enum(['left', 'right']).optional(),
          badge: z.string().optional(),
          badgePosition: z.enum(['top', 'bottom']).optional(),
          badgeBgColor: z.string().optional(),
          badgeTextColor: z.string().optional(),
          icon: z.string().optional(),
          customClass: z.string().optional(),
          rows: z.array(
            z.object({
              columns: z.array(
                z.object({
                  colGrid: z.string(),
                  moduleId: z.string().optional(),
                  items: z.array(
                    z.object({
                      type: z.enum(['menu_item', 'module']),
                      id: z.string()
                    })
                  ).optional()
                })
              )
            })
          ).optional(),
          styleId: z.number().int().optional(),
          template: z.string().optional()
        });

        const parsed = schema.parse(args);

        let convertedLayout: MegaMenuRow[] | undefined = undefined;
        if (parsed.rows) {
          convertedLayout = parsed.rows.map(r => ({
            type: 'row',
            attr: r.columns.map(c => ({
              type: 'column',
              colGrid: c.colGrid,
              moduleId: c.moduleId,
              items: c.items ? c.items.map(it => ({
                type: it.type,
                item_id: it.type === 'menu_item' ? it.id : undefined,
                moduleId: it.type === 'module' ? it.id : undefined
              })) : []
            }))
          }));
        }

        const settingsUpdate: any = {};
        if (parsed.enabled !== undefined) settingsUpdate.megamenu = parsed.enabled ? 1 : 0;
        if (parsed.width) settingsUpdate.width = parsed.width;
        if (parsed.align) settingsUpdate.menualign = parsed.align;
        if (parsed.dropdown) settingsUpdate.dropdown = parsed.dropdown;
        if (parsed.badge !== undefined) settingsUpdate.badge = parsed.badge;
        if (parsed.badgePosition) settingsUpdate.badge_position = parsed.badgePosition;
        if (parsed.badgeBgColor) settingsUpdate.badge_bg_color = parsed.badgeBgColor;
        if (parsed.badgeTextColor) settingsUpdate.badge_text_color = parsed.badgeTextColor;
        if (parsed.icon !== undefined) settingsUpdate.faicon = parsed.icon;
        if (parsed.customClass !== undefined) settingsUpdate.customclass = parsed.customClass;
        if (convertedLayout) settingsUpdate.layout = convertedLayout;

        const result = await megaMenuService.updateMenuItem(
          parsed.menuItemId,
          settingsUpdate,
          parsed.styleId,
          parsed.template
        );

        return {
          content: [
            {
              type: 'text',
              text: `Successfully updated Mega Menu for menu item #${parsed.menuItemId}. Snapshot '${result.snapshotId}' created for rollback.`
            }
          ]
        };
      }

      if (name === 'helix_scaffold_override') {
        const schema = z.object({
          type: z.enum(['component', 'module']),
          extension: z.string(),
          view: z.string(),
          layoutFile: z.string().optional(),
          template: z.string().optional()
        });

        const parsed = schema.parse(args);
        const result = scaffoldService.scaffoldOverride({
          ...parsed,
          templateName: parsed.template
        });

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
          position: z.string().optional(),
          template: z.string().optional()
        });

        const parsed = schema.parse(args);
        const result = scaffoldService.scaffoldFeature({
          ...parsed,
          templateName: parsed.template
        });

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
          template: z.string().optional(),
          outputStyle: z.enum(['expanded', 'compressed']).optional()
        });

        const parsed = schema.parse(args || {});
        const result = scssService.compile({
          targetFile: parsed.targetFile,
          templateName: parsed.template,
          outputStyle: parsed.outputStyle
        });

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
