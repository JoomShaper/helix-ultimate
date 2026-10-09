import fs from 'node:fs';
import path from 'node:path';
import { HelixLayout, HelixRow, HelixColumn } from './layout.types.js';
import { LayoutValidator } from './layout.validator.js';
import { BackupService } from '../backup/backup.service.js';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';

export class LayoutService {
  private optionsPath: string;

  constructor(
    private workspaceRoot: string,
    private backupService: BackupService,
    private registeredPositions: string[] = []
  ) {
    this.optionsPath = path.join('templates', 'shaper_helixultimate', 'options.json');
  }

  /**
   * Reads and parses the active Helix Ultimate layout tree.
   */
  public getLayout(): HelixLayout {
    const fullPath = PathGuard.assertWithin(this.optionsPath, this.workspaceRoot);
    if (!fs.existsSync(fullPath)) {
      throw new Error(`Options file not found: ${this.optionsPath}`);
    }

    const raw = fs.readFileSync(fullPath, 'utf-8');
    const options = JSON.parse(raw);

    if (!options.layout) {
      throw new Error('No layout found in options.json');
    }

    // In options.json, layout is stored as a JSON string
    return typeof options.layout === 'string'
      ? JSON.parse(options.layout)
      : options.layout;
  }

  /**
   * Updates an existing row or inserts a new row in the layout.
   */
  public updateRow(
    rowIdentifier: string | number,
    columns: Array<{ width: number; position?: string; customClass?: string; isComponent?: boolean }>,
    rowSettings?: Partial<HelixRow['settings']>
  ): { layout: HelixLayout; snapshotId: string } {
    const layout = this.getLayout();

    // Find row by index or name
    let rowIndex = -1;
    if (typeof rowIdentifier === 'number') {
      rowIndex = rowIdentifier;
    } else {
      rowIndex = layout.findIndex(
        r => r.settings?.name?.toLowerCase() === rowIdentifier.toLowerCase()
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
        name: rowSettings?.name || (rowIndex >= 0 ? layout[rowIndex].settings.name : String(rowIdentifier)),
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
    } else {
      layout.push(targetRow);
      Logger.info(`Appended new row (${targetRow.settings.name})`);
    }

    const snapshotId = this.saveLayout(layout, `update_row_${rowIdentifier}`);
    return { layout, snapshotId };
  }

  /**
   * Validates and saves layout changes to options.json with a pre-mutation backup snapshot.
   */
  public saveLayout(layout: HelixLayout, action: string): string {
    const fullPath = PathGuard.assertWithin(this.optionsPath, this.workspaceRoot);

    // 1. Validate full layout
    const validation = LayoutValidator.validateLayout(layout, this.registeredPositions);
    if (!validation.valid) {
      throw new Error(`Invalid layout: ${validation.errors.join('; ')}`);
    }

    // 2. Snapshot
    const snapshotId = this.backupService.captureSnapshot(this.optionsPath, action);

    // 3. Read options, update layout, write back
    const raw = fs.readFileSync(fullPath, 'utf-8');
    const options = JSON.parse(raw);
    options.layout = JSON.stringify(layout);

    fs.writeFileSync(fullPath, JSON.stringify(options, null, '\t'), 'utf-8');
    Logger.info(`Layout saved successfully to ${this.optionsPath}`);

    return snapshotId;
  }
}
