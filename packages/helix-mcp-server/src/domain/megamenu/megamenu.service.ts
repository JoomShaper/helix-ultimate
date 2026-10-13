import fs from 'node:fs';
import path from 'node:path';
import { HelixMegaMenuMap, MegaMenuItemSettings } from './megamenu.types.js';
import { MegaMenuValidator } from './megamenu.validator.js';
import { BackupService } from '../backup/backup.service.js';
import { JoomlaDbClient } from '../../core/db-client.js';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';

export class MegaMenuService {
  private optionsPath: string;

  constructor(
    private workspaceRoot: string,
    private backupService: BackupService,
    private dbClient?: JoomlaDbClient
  ) {
    this.optionsPath = path.join('templates', 'shaper_helixultimate', 'options.json');
  }

  /**
   * Retrieves all mega menu configurations across all menu items.
   */
  public async getMegaMenuMap(styleId?: number): Promise<HelixMegaMenuMap> {
    // 1. Try from live database
    if (this.dbClient && this.dbClient.isConfigured()) {
      try {
        const activeStyleId = styleId || await this.dbClient.getActiveHelixStyleId();
        if (activeStyleId) {
          const params = await this.dbClient.getStyleParams(activeStyleId);
          if (params?.helixultimatemenulayout) {
            const raw = params.helixultimatemenulayout;
            const parsed = typeof raw === 'string' ? JSON.parse(raw) : raw;
            Logger.debug(`Loaded mega menu map from database for style #${activeStyleId}`);
            return parsed || {};
          }
        }
      } catch (err) {
        Logger.warn(`Failed reading mega menu from DB, falling back to options.json: ${err}`);
      }
    }

    // 2. Fallback to options.json
    const fullPath = PathGuard.assertWithin(this.optionsPath, this.workspaceRoot);
    if (!fs.existsSync(fullPath)) {
      return {};
    }

    try {
      const raw = fs.readFileSync(fullPath, 'utf-8');
      const options = JSON.parse(raw);
      if (options.helixultimatemenulayout) {
        const rawLayout = options.helixultimatemenulayout;
        return typeof rawLayout === 'string' ? JSON.parse(rawLayout) : rawLayout;
      }
    } catch (err) {
      Logger.warn(`Could not read mega menu from options.json: ${err}`);
    }

    return {};
  }

  /**
   * Retrieves mega menu settings for a specific menu item ID.
   */
  public async getMenuItemSettings(menuItemId: string, styleId?: number): Promise<MegaMenuItemSettings | null> {
    const map = await this.getMegaMenuMap(styleId);
    return map[String(menuItemId)] || null;
  }

  /**
   * Updates or creates mega menu configuration for a specific menu item.
   */
  public async updateMenuItem(
    menuItemId: string,
    settings: Partial<MegaMenuItemSettings>,
    styleId?: number
  ): Promise<{ settings: MegaMenuItemSettings; snapshotId: string }> {
    const fullPath = PathGuard.assertWithin(this.optionsPath, this.workspaceRoot);
    const map = await this.getMegaMenuMap(styleId);

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
    const snapshotId = this.backupService.captureSnapshot(this.optionsPath, `update_megamenu_${menuItemId}`);

    // Update map
    map[String(menuItemId)] = merged;

    // 1. Update live MySQL database if client is configured
    if (this.dbClient && this.dbClient.isConfigured()) {
      try {
        const activeStyleId = styleId || await this.dbClient.getActiveHelixStyleId();
        if (activeStyleId) {
          const params = await this.dbClient.getStyleParams(activeStyleId) || {};
          params.helixultimatemenulayout = JSON.stringify(map);
          await this.dbClient.updateStyleParams(activeStyleId, params);
          Logger.info(`Synchronized mega menu layout to MySQL database for style #${activeStyleId}`);
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
        Logger.info(`Updated helixultimatemenulayout in ${this.optionsPath}`);
      }
    } catch (err) {
      Logger.warn(`Could not update options.json for mega menu: ${err}`);
    }

    return { settings: merged, snapshotId };
  }
}
