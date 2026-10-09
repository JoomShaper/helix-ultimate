import fs from 'node:fs';
import path from 'node:path';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';

export interface ScaffoldOverrideOptions {
  type: 'component' | 'module';
  extension: string; // e.g., 'com_content', 'mod_menu'
  view: string;      // e.g., 'article', 'default'
  layoutFile?: string; // e.g., 'default.php'
}

export interface ScaffoldFeatureOptions {
  featureName: string; // e.g., 'promo_bar'
  position?: string;   // e.g., 'top1'
}

export class ScaffoldService {
  constructor(private workspaceRoot: string) {}

  /**
   * Scaffolds a clean Joomla 4/5/6 template override inside templates/shaper_helixultimate/html/
   */
  public scaffoldOverride(options: ScaffoldOverrideOptions): { relativePath: string; fullPath: string } {
    const layoutFileName = options.layoutFile || 'default.php';
    const relativeDir = path.join(
      'templates',
      'shaper_helixultimate',
      'html',
      options.extension,
      options.view
    );

    const fullDir = PathGuard.assertWithin(relativeDir, this.workspaceRoot);
    if (!fs.existsSync(fullDir)) {
      fs.mkdirSync(fullDir, { recursive: true });
    }

    const relativePath = path.join(relativeDir, layoutFileName);
    const fullPath = PathGuard.assertWithin(relativePath, this.workspaceRoot);

    if (fs.existsSync(fullPath)) {
      throw new Error(`Override file already exists at: ${relativePath}`);
    }

    const content = `<?php
/**
 * @package     Helix Ultimate Framework
 * @subpackage  shaper_helixultimate
 * @copyright   Copyright (C) ${new Date().getFullYear()} JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\\CMS\\Factory;
use Joomla\\CMS\\HTML\\HTMLHelper;
use Joomla\\CMS\\Language\\Text;

/**
 * Modern Joomla 4/5/6 Helix Ultimate override for ${options.extension} / ${options.view}
 */
?>
<div class="${options.extension}-${options.view} helix-override">
    <div class="container my-3">
        <div class="row">
            <div class="col-12">
                <!-- Custom Helix Override content goes here -->
                <p class="lead"><?php echo Text::_('JGLOBAL_ARTICLE_CONTENT'); ?></p>
            </div>
        </div>
    </div>
</div>
`;

    fs.writeFileSync(fullPath, content, 'utf-8');
    Logger.info(`Scaffolded override: ${relativePath}`);

    return { relativePath, fullPath };
  }

  /**
   * Scaffolds a new Helix feature class inside templates/shaper_helixultimate/features/
   */
  public scaffoldFeature(options: ScaffoldFeatureOptions): { relativePath: string; fullPath: string } {
    const normalizedName = options.featureName.toLowerCase().replace(/[^a-z0-9_]/g, '_');
    const className = normalizedName
      .split('_')
      .map(part => part.charAt(0).toUpperCase() + part.slice(1))
      .join('');

    const relativePath = path.join(
      'templates',
      'shaper_helixultimate',
      'features',
      `${normalizedName}.php`
    );

    const fullPath = PathGuard.assertWithin(relativePath, this.workspaceRoot);
    if (fs.existsSync(fullPath)) {
      throw new Error(`Feature file already exists at: ${relativePath}`);
    }

    const content = `<?php
/**
 * @package     Helix Ultimate Framework
 * @subpackage  shaper_helixultimate
 * @copyright   Copyright (C) ${new Date().getFullYear()} JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\\CMS\\Factory;
use Joomla\\CMS\\Language\\Text;

/**
 * Helix Ultimate Feature: ${className}
 */
class HelixUltimateFeature${className}
{
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Initializes assets or dependencies for the feature
     */
    public function init()
    {
        // Feature initialization logic (Vanilla JS & SCSS preferred)
    }

    /**
     * Renders the feature output
     *
     * @return string HTML output
     */
    public function render()
    {
        $enabled = $this->params->get('${normalizedName}_enable', 1);

        if (!$enabled) {
            return '';
        }

        ob_start();
        ?>
        <div class="helix-feature-${normalizedName} d-flex align-items-center">
            <div class="feature-content">
                <!-- Feature output markup -->
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
`;

    fs.writeFileSync(fullPath, content, 'utf-8');
    Logger.info(`Scaffolded Helix feature: ${relativePath}`);

    return { relativePath, fullPath };
  }
}
