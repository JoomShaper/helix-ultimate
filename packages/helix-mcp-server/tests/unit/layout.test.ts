import { describe, it, expect } from 'vitest';
import { LayoutValidator } from '../../src/domain/layout/layout.validator.js';
import { HelixRow } from '../../src/domain/layout/layout.types.js';

describe('LayoutValidator', () => {
  it('passes on a valid 12-column row (e.g. 6 + 6)', () => {
    const validRow: HelixRow = {
      type: 'row',
      layout: '6+6',
      settings: { name: 'Header Row' },
      attr: [
        {
          type: 'sp_col',
          settings: { column_type: 0, name: 'logo', grid_size: 6 }
        },
        {
          type: 'sp_col',
          settings: { column_type: 0, name: 'menu', grid_size: 6 }
        }
      ]
    };

    const result = LayoutValidator.validateRow(validRow);
    expect(result.valid).toBe(true);
    expect(result.errors).toHaveLength(0);
  });

  it('fails when columns sum to less than 12 (e.g. 4 + 4)', () => {
    const invalidRow: HelixRow = {
      type: 'row',
      layout: '4+4',
      settings: { name: 'Broken Row' },
      attr: [
        {
          type: 'sp_col',
          settings: { column_type: 0, name: 'logo', grid_size: 4 }
        },
        {
          type: 'sp_col',
          settings: { column_type: 0, name: 'menu', grid_size: 4 }
        }
      ]
    };

    const result = LayoutValidator.validateRow(invalidRow);
    expect(result.valid).toBe(false);
    expect(result.errors[0]).toContain('Total grid size is 8, but Bootstrap 12-column grid requires exactly 12');
  });

  it('fails when an unregistered position is assigned and registered positions are provided', () => {
    const rowWithUnknownPosition: HelixRow = {
      type: 'row',
      layout: '12',
      settings: { name: 'Promo Row' },
      attr: [
        {
          type: 'sp_col',
          settings: { column_type: 0, name: 'non_existent_position', grid_size: 12 }
        }
      ]
    };

    const registeredPositions = ['top1', 'top2', 'logo', 'menu'];
    const result = LayoutValidator.validateRow(rowWithUnknownPosition, 0, registeredPositions);

    expect(result.valid).toBe(false);
    expect(result.errors[0]).toContain("unregistered module position 'non_existent_position'");
  });
});
