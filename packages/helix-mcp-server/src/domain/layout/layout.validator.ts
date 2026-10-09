import { HelixRow, HelixColumn } from './layout.types.js';

export interface LayoutValidationResult {
  valid: boolean;
  errors: string[];
}

export class LayoutValidator {
  /**
   * Validates an entire Helix layout tree.
   */
  public static validateLayout(rows: HelixRow[], registeredPositions?: string[]): LayoutValidationResult {
    const errors: string[] = [];

    if (!Array.isArray(rows) || rows.length === 0) {
      return { valid: false, errors: ['Layout must be a non-empty array of rows.'] };
    }

    rows.forEach((row, index) => {
      const rowResult = this.validateRow(row, index, registeredPositions);
      if (!rowResult.valid) {
        errors.push(...rowResult.errors);
      }
    });

    return {
      valid: errors.length === 0,
      errors
    };
  }

  /**
   * Validates a single Helix row and its columns.
   */
  public static validateRow(row: HelixRow, rowIndex: number = 0, registeredPositions?: string[]): LayoutValidationResult {
    const errors: string[] = [];
    const rowName = row.settings?.name || `Row #${rowIndex + 1}`;

    if (row.type !== 'row') {
      errors.push(`Row #${rowIndex + 1} has invalid type '${row.type}'. Expected 'row'.`);
    }

    if (!Array.isArray(row.attr) || row.attr.length === 0) {
      errors.push(`${rowName} must have at least one column.`);
      return { valid: false, errors };
    }

    let totalGridSize = 0;

    row.attr.forEach((col: HelixColumn, colIndex: number) => {
      const size = Number(col.settings?.grid_size);
      if (isNaN(size) || size < 1 || size > 12) {
        errors.push(`${rowName}, Column #${colIndex + 1} has invalid grid_size '${col.settings?.grid_size}'. Must be between 1 and 12.`);
      } else {
        totalGridSize += size;
      }

      // Check position if module position (type 0)
      if (registeredPositions && col.settings?.column_type === 0 && col.settings?.name) {
        const pos = col.settings.name;
        if (!registeredPositions.includes(pos)) {
          errors.push(`${rowName}, Column #${colIndex + 1} references unregistered module position '${pos}'.`);
        }
      }
    });

    if (totalGridSize !== 12) {
      errors.push(
        `Column grid sum mismatch in ${rowName}: Total grid size is ${totalGridSize}, but Bootstrap 12-column grid requires exactly 12.`
      );
    }

    return {
      valid: errors.length === 0,
      errors
    };
  }
}
