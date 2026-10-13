import { MegaMenuItemSettings, MegaMenuRow } from './megamenu.types.js';

export interface MegaMenuValidationResult {
  valid: boolean;
  errors: string[];
}

export class MegaMenuValidator {
  public static validateSettings(settings: MegaMenuItemSettings): MegaMenuValidationResult {
    const errors: string[] = [];

    if (settings.menualign && !['left', 'right', 'center', 'full'].includes(settings.menualign)) {
      errors.push(`Invalid menualign '${settings.menualign}'. Must be 'left', 'right', 'center', or 'full'.`);
    }

    if (settings.dropdown && !['left', 'right'].includes(settings.dropdown)) {
      errors.push(`Invalid dropdown direction '${settings.dropdown}'. Must be 'left' or 'right'.`);
    }

    if (settings.badge_position && !['top', 'bottom'].includes(settings.badge_position)) {
      errors.push(`Invalid badge_position '${settings.badge_position}'. Must be 'top' or 'bottom'.`);
    }

    if (settings.layout && Array.isArray(settings.layout)) {
      settings.layout.forEach((row, rowIndex) => {
        const rowErrors = this.validateRow(row, rowIndex);
        errors.push(...rowErrors);
      });
    }

    return {
      valid: errors.length === 0,
      errors
    };
  }

  public static validateRow(row: MegaMenuRow, rowIndex: number): string[] {
    const errors: string[] = [];

    if (!row.attr || !Array.isArray(row.attr) || row.attr.length === 0) {
      errors.push(`Row #${rowIndex + 1} has no columns.`);
      return errors;
    }

    let totalGrid = 0;
    row.attr.forEach((col, colIndex) => {
      const gridSize = parseInt(col.colGrid, 10);
      if (isNaN(gridSize) || gridSize < 1 || gridSize > 12) {
        errors.push(`Row #${rowIndex + 1}, Column #${colIndex + 1} has invalid colGrid '${col.colGrid}'. Must be between 1 and 12.`);
      } else {
        totalGrid += gridSize;
      }

      if (col.items && Array.isArray(col.items)) {
        col.items.forEach((item, itemIndex) => {
          if (!['menu_item', 'module'].includes(item.type)) {
            errors.push(`Row #${rowIndex + 1}, Column #${colIndex + 1}, Item #${itemIndex + 1} has invalid type '${item.type}'. Must be 'menu_item' or 'module'.`);
          }
        });
      }
    });

    if (totalGrid !== 12) {
      errors.push(`Row #${rowIndex + 1} total grid width is ${totalGrid}. Must equal exactly 12 (Bootstrap grid constraint).`);
    }

    return errors;
  }
}
