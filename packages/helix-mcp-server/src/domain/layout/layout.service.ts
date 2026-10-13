import fs from 'node:fs';
import path from 'node:path';
import { HelixLayout, HelixRow, HelixColumn } from './layout.types.js';
import { LayoutValidator } from './layout.validator.js';
import { BackupService } from '../backup/backup.service.js';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';
import { JoomlaDbClient } from '../../core/db-client.js';

export interface UpdateRowOptions {
  rowId: string | number;
  columns: Array<{ width: number; position?: string; customClass?: string; isComponent?: boolean }>;
  rowSettings?: Partial<HelixRow['settings']>;
  insertAbove?: string;
  insertBelow?: string;
  styleId?: number;
  templateName?: string;
}

export class LayoutService {
  constructor(
    private workspaceRoot: string,
    private backupService: BackupService,
    private registeredPositions: string[] = [],
    private dbClient?: JoomlaDbClient,
    private defaultTemplate: string = 'shaper_helixultimate'
  ) {}

  public getOptionsPath(templateName?: string): string {
    return path.join('templates', templateName || this.defaultTemplate, 'options.json');
  }

  /**
   * Reads and parses the active Helix Ultimate layout tree.
   * Prioritizes live database if connected, else falls back to options.json.
   */
  public async getLayout(styleId?: number, templateName?: string): Promise<HelixLayout> {
    const targetTemplate = templateName || this.defaultTemplate;

    // 1. Try from live database
    if (this.dbClient && this.dbClient.isConfigured()) {
      try {
        const activeStyleId = styleId || await this.dbClient.getActiveHelixStyleId(targetTemplate);
        if (activeStyleId) {
          const params = await this.dbClient.getStyleParams(activeStyleId);
          if (params?.layout) {
            const layout = typeof params.layout === 'string' ? JSON.parse(params.layout) : params.layout;
            if (Array.isArray(layout) && layout.length > 0) {
              Logger.debug(`Loaded layout for style #${activeStyleId} (${targetTemplate}) directly from database`);
              return layout;
            }
          }
        }
      } catch (err) {
        Logger.warn(`Failed reading layout from DB, falling back to options.json: ${err}`);
      }
    }

    // 2. Fallback to options.json
    const optionsPath = this.getOptionsPath(targetTemplate);
    const fullPath = PathGuard.assertWithin(optionsPath, this.workspaceRoot);
    if (!fs.existsSync(fullPath)) {
      throw new Error(`Options file not found for template '${targetTemplate}': ${optionsPath}`);
    }

    const raw = fs.readFileSync(fullPath, 'utf-8');
    const options = JSON.parse(raw);

    if (!options.layout) {
      throw new Error(`No layout found in options.json for template '${targetTemplate}'`);
    }

    return typeof options.layout === 'string'
      ? JSON.parse(options.layout)
      : options.layout;
  }

  /**
   * Updates an existing row or inserts a new row in the layout.
   */
  public async updateRow(options: UpdateRowOptions): Promise<{ layout: HelixLayout; snapshotId: string }> {
    const layout = await this.getLayout(options.styleId, options.templateName);
    const { rowId, columns, rowSettings, insertAbove, insertBelow, templateName } = options;

    // Find row by index or name
    let rowIndex = -1;
    if (typeof rowId === 'number') {
      rowIndex = rowId;
    } else {
      rowIndex = layout.findIndex(
        r => r.settings?.name?.toLowerCase() === rowId.toLowerCase()
      );
    }

    // Convert columns to HelixColumn format
    const helixCols: HelixColumn[] = columns.map(col => ({
      type: 'sp_col',
      settings: {
        column_type: col.isComponent ? 1 : 0,
        name: col.position || '',
        grid_size: col.width,
        custom_class: col.customClass || '',
        sm_col: '',
        xs_col: '',
        hidden_xs: 0,
        hidden_sm: 0,
        hidden_md: 0
      }
    }));

    const layoutString = columns.map(c => c.width).join('+');

    const targetRow: HelixRow = {
      type: 'row',
      layout: layoutString,
      settings: {
        name: rowSettings?.name || (rowIndex >= 0 ? layout[rowIndex].settings.name : String(rowId)),
        fluidrow: rowSettings?.fluidrow ?? 0,
        custom_class: rowSettings?.custom_class || '',
        padding: rowSettings?.padding || '',
        margin: rowSettings?.margin || '',
        ...rowSettings
      },
      attr: helixCols
    };

    // Validate the proposed row before modifying anything
    const validation = LayoutValidator.validateRow(targetRow, rowIndex >= 0 ? rowIndex : layout.length, this.registeredPositions);
    if (!validation.valid) {
      throw new Error(`Layout validation failed: ${validation.errors.join('; ')}`);
    }

    if (rowIndex >= 0 && rowIndex < layout.length) {
      layout[rowIndex] = targetRow;
      Logger.info(`Updated row #${rowIndex + 1} (${targetRow.settings.name})`);
    } else if (insertAbove) {
      const targetIndex = layout.findIndex(r => r.settings?.name?.toLowerCase() === insertAbove.toLowerCase());
      if (targetIndex >= 0) {
        layout.splice(targetIndex, 0, targetRow);
        Logger.info(`Inserted row '${targetRow.settings.name}' above '${insertAbove}' at index ${targetIndex}`);
      } else {
        layout.unshift(targetRow);
        Logger.info(`Target '${insertAbove}' not found, prepended row to start`);
      }
    } else if (insertBelow) {
      const targetIndex = layout.findIndex(r => r.settings?.name?.toLowerCase() === insertBelow.toLowerCase());
      if (targetIndex >= 0) {
        layout.splice(targetIndex + 1, 0, targetRow);
        Logger.info(`Inserted row '${targetRow.settings.name}' below '${insertBelow}' at index ${targetIndex + 1}`);
      } else {
        layout.push(targetRow);
        Logger.info(`Target '${insertBelow}' not found, appended row to end`);
      }
    } else {
      layout.push(targetRow);
      Logger.info(`Appended new row (${targetRow.settings.name})`);
    }

    const snapshotId = await this.saveLayout(layout, `update_row_${rowId}`, options.styleId, templateName);
    return { layout, snapshotId };
  }

  /**
   * Validates and saves layout changes to both database (if configured) and options.json.
   */
  public async saveLayout(layout: HelixLayout, action: string, styleId?: number, templateName?: string): Promise<string> {
    const targetTemplate = templateName || this.defaultTemplate;
    const optionsPath = this.getOptionsPath(targetTemplate);
    const fullPath = PathGuard.assertWithin(optionsPath, this.workspaceRoot);

    // 1. Validate full layout
    const validation = LayoutValidator.validateLayout(layout, this.registeredPositions);
    if (!validation.valid) {
      throw new Error(`Invalid layout: ${validation.errors.join('; ')}`);
    }

    // 2. Snapshot
    const snapshotId = this.backupService.captureSnapshot(optionsPath, action);

    // 3. Update database if client is configured
    if (this.dbClient && this.dbClient.isConfigured()) {
      try {
        const activeStyleId = styleId || await this.dbClient.getActiveHelixStyleId(targetTemplate);
        if (activeStyleId) {
          const params = await this.dbClient.getStyleParams(activeStyleId) || {};
          params.layout = JSON.stringify(layout);
          await this.dbClient.updateStyleParams(activeStyleId, params);
          Logger.info(`Synchronized layout to MySQL database for style #${activeStyleId} (${targetTemplate})`);
        }
      } catch (err) {
        Logger.warn(`Database sync failed (persisting to options.json): ${err}`);
      }
    }

    // 4. Update options.json file
    if (fs.existsSync(fullPath)) {
      const raw = fs.readFileSync(fullPath, 'utf-8');
      const options = JSON.parse(raw);
      options.layout = JSON.stringify(layout);

      fs.writeFileSync(fullPath, JSON.stringify(options, null, '\t'), 'utf-8');
      Logger.info(`Layout saved successfully to ${optionsPath}`);
    }

    return snapshotId;
  }
}
