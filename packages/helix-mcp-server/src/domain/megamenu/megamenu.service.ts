import fs from 'node:fs';
import path from 'node:path';
import { HelixMegaMenuMap, MegaMenuItemSettings } from './megamenu.types.js';
import { MegaMenuValidator } from './megamenu.validator.js';
import { BackupService } from '../backup/backup.service.js';
import { JoomlaDbClient } from '../../core/db-client.js';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';

export class MegaMenuService {
  constructor(
    private workspaceRoot: string,
    private backupService: BackupService,
    private dbClient?: JoomlaDbClient,
    private defaultTemplate: string = 'shaper_helixultimate'
  ) {}

  public getOptionsPath(templateName?: string): string {
    return path.join('templates', templateName || this.defaultTemplate, 'options.json');
  }

  /**
   * Retrieves all mega menu configurations across all menu items.
   */
  public async getMegaMenuMap(styleId?: number, templateName?: string): Promise<HelixMegaMenuMap> {
    const targetTemplate = templateName || this.defaultTemplate;

    // 1. Try from live database
    if (this.dbClient && this.dbClient.isConfigured()) {
      try {
        const activeStyleId = styleId || await this.dbClient.getActiveHelixStyleId(targetTemplate);
        if (activeStyleId) {
          const params = await this.dbClient.getStyleParams(activeStyleId);
          if (params?.helixultimatemenulayout) {
            const raw = params.helixultimatemenulayout;
            return this.normalizeMap(raw);
          }
        }
      } catch (err) {
        Logger.warn(`Failed reading mega menu from DB, falling back to options.json: ${err}`);
      }
    }

    // 2. Fallback to options.json
    const optionsPath = this.getOptionsPath(targetTemplate);
    const fullPath = PathGuard.assertWithin(optionsPath, this.workspaceRoot);
    if (!fs.existsSync(fullPath)) {
      return {};
    }

    try {
      const raw = fs.readFileSync(fullPath, 'utf-8');
      const options = JSON.parse(raw);
      if (options.helixultimatemenulayout) {
        return this.normalizeMap(options.helixultimatemenulayout);
      }
    } catch (err) {
      Logger.warn(`Could not read mega menu from options.json for ${targetTemplate}: ${err}`);
    }

    return {};
  }

  /**
   * Ensures the mega menu map is always a clean object dictionary, never a sparse array of nulls.
   */
  public normalizeMap(raw: unknown): HelixMegaMenuMap {
    if (!raw) return {};
    let parsed: any = raw;
    if (typeof raw === 'string') {
      try {
        parsed = JSON.parse(raw);
      } catch {
        return {};
      }
    }

    if (Array.isArray(parsed)) {
      const obj: HelixMegaMenuMap = {};
      parsed.forEach((val, idx) => {
        if (val !== null && val !== undefined && typeof val === 'object') {
          obj[String(idx)] = val;
        }
      });
      return obj;
    }

    if (typeof parsed === 'object') {
      const cleanObj: HelixMegaMenuMap = {};
      for (const [key, val] of Object.entries(parsed)) {
        if (val !== null && val !== undefined) {
          cleanObj[key] = val as any;
        }
      }
      return cleanObj;
    }

    return {};
  }

  /**
   * Retrieves mega menu settings for a specific menu item ID.
   */
  public async getMenuItemSettings(menuItemId: string, styleId?: number, templateName?: string): Promise<MegaMenuItemSettings | null> {
    const map = await this.getMegaMenuMap(styleId, templateName);
    return map[String(menuItemId)] || null;
  }

  /**
   * Updates or creates mega menu configuration for a specific menu item.
   */
  public async updateMenuItem(
    menuItemId: string,
    settings: Partial<MegaMenuItemSettings>,
    styleId?: number,
    templateName?: string
  ): Promise<{ settings: MegaMenuItemSettings; snapshotId: string }> {
    const targetTemplate = templateName || this.defaultTemplate;
    const optionsPath = this.getOptionsPath(targetTemplate);
    const fullPath = PathGuard.assertWithin(optionsPath, this.workspaceRoot);
    const map = this.normalizeMap(await this.getMegaMenuMap(styleId, targetTemplate));

    const existing: MegaMenuItemSettings = map[String(menuItemId)] || {
      megamenu: 1,
      menualign: 'left',
      width: '600px'
    };

    const merged: MegaMenuItemSettings = {
      ...existing,
      ...settings,
      megamenu: settings.megamenu !== undefined ? settings.megamenu : (existing.megamenu ?? 1)
    };

    // Validate
    const validation = MegaMenuValidator.validateSettings(merged);
    if (!validation.valid) {
      throw new Error(`Mega menu validation failed: ${validation.errors.join('; ')}`);
    }

    // Capture backup snapshot before saving
    const snapshotId = this.backupService.captureSnapshot(optionsPath, `update_megamenu_${menuItemId}`);

    // Update map
    map[String(menuItemId)] = merged;

    // 1. Update live MySQL database if client is configured
    if (this.dbClient && this.dbClient.isConfigured()) {
      try {
        const activeStyleId = styleId || await this.dbClient.getActiveHelixStyleId(targetTemplate);
        if (activeStyleId) {
          const params = await this.dbClient.getStyleParams(activeStyleId) || {};
          params.helixultimatemenulayout = JSON.stringify(map);
          await this.dbClient.updateStyleParams(activeStyleId, params);
          Logger.info(`Synchronized mega menu layout to MySQL database for style #${activeStyleId} (${targetTemplate})`);
        }
      } catch (err) {
        Logger.warn(`Database sync for mega menu failed: ${err}`);
      }
    }

    // 2. Update options.json
    try {
      if (fs.existsSync(fullPath)) {
        const raw = fs.readFileSync(fullPath, 'utf-8');
        const options = JSON.parse(raw);
        options.helixultimatemenulayout = JSON.stringify(map);
        fs.writeFileSync(fullPath, JSON.stringify(options, null, '\t'), 'utf-8');
        Logger.info(`Updated helixultimatemenulayout in ${optionsPath}`);
      }
    } catch (err) {
      Logger.warn(`Could not update options.json for mega menu: ${err}`);
    }

    return { settings: merged, snapshotId };
  }
}
