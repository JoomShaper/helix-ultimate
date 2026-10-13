import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { MegaMenuService } from '../../src/domain/megamenu/megamenu.service.js';
import { MegaMenuValidator } from '../../src/domain/megamenu/megamenu.validator.js';
import { BackupService } from '../../src/domain/backup/backup.service.js';
import { MegaMenuItemSettings } from '../../src/domain/megamenu/megamenu.types.js';

describe('MegaMenu Domain', () => {
  let tempDir: string;
  let backupService: BackupService;
  let megaMenuService: MegaMenuService;

  beforeEach(() => {
    tempDir = fs.mkdtempSync(path.join(os.tmpdir(), 'helix-megamenu-test-'));
    const templateDir = path.join(tempDir, 'templates', 'shaper_helixultimate');
    fs.mkdirSync(templateDir, { recursive: true });

    // Seed options.json
    fs.writeFileSync(
      path.join(templateDir, 'options.json'),
      JSON.stringify({ helixultimatemenulayout: '{}' }, null, 2),
      'utf-8'
    );

    backupService = new BackupService(tempDir);
    megaMenuService = new MegaMenuService(tempDir, backupService);
  });

  afterEach(() => {
    fs.rmSync(tempDir, { recursive: true, force: true });
  });

  describe('MegaMenuValidator', () => {
    it('validates a valid 2-column (6+6) mega menu layout', () => {
      const settings: MegaMenuItemSettings = {
        megamenu: 1,
        width: '800px',
        menualign: 'center',
        layout: [
          {
            type: 'row',
            attr: [
              {
                type: 'column',
                colGrid: '6',
                items: [{ type: 'menu_item', item_id: '102' }]
              },
              {
                type: 'column',
                colGrid: '6',
                items: [{ type: 'module', moduleId: '14' }]
              }
            ]
          }
        ]
      };

      const result = MegaMenuValidator.validateSettings(settings);
      expect(result.valid).toBe(true);
      expect(result.errors).toHaveLength(0);
    });

    it('rejects row where column widths do not sum to 12', () => {
      const settings: MegaMenuItemSettings = {
        megamenu: 1,
        layout: [
          {
            type: 'row',
            attr: [
              { type: 'column', colGrid: '4' },
              { type: 'column', colGrid: '4' } // total 8 instead of 12
            ]
          }
        ]
      };

      const result = MegaMenuValidator.validateSettings(settings);
      expect(result.valid).toBe(false);
      expect(result.errors.some(e => e.includes('Must equal exactly 12'))).toBe(true);
    });

    it('rejects invalid alignment enum', () => {
      const settings: MegaMenuItemSettings = {
        megamenu: 1,
        menualign: 'diagonal' as any
      };

      const result = MegaMenuValidator.validateSettings(settings);
      expect(result.valid).toBe(false);
      expect(result.errors.some(e => e.includes('Invalid menualign'))).toBe(true);
    });
  });

  describe('MegaMenuService', () => {
    it('saves and retrieves mega menu settings with snapshots', async () => {
      const { settings, snapshotId } = await megaMenuService.updateMenuItem('105', {
        megamenu: 1,
        width: '900px',
        menualign: 'full',
        badge: 'Featured',
        badge_bg_color: '#ec430f',
        layout: [
          {
            type: 'row',
            attr: [
              { type: 'column', colGrid: '4' },
              { type: 'column', colGrid: '4' },
              { type: 'column', colGrid: '4' }
            ]
          }
        ]
      });

      expect(settings.megamenu).toBe(1);
      expect(settings.width).toBe('900px');
      expect(snapshotId).toBeDefined();

      const retrieved = await megaMenuService.getMenuItemSettings('105');
      expect(retrieved).not.toBeNull();
      expect(retrieved?.badge).toBe('Featured');
      expect(retrieved?.menualign).toBe('full');

      const allMaps = await megaMenuService.getMegaMenuMap();
      expect(allMaps['105']).toBeDefined();
    });
  });
});
