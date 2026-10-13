import fs from 'node:fs';
import path from 'node:path';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';

export interface ScaffoldOverrideOptions {
  type: 'component' | 'module';
  extension: string; // e.g., 'com_content', 'mod_menu'
  view: string;      // e.g., 'article', 'default'
  layoutFile?: string; // e.g., 'default.php'
  templateName?: string;
}

export interface ScaffoldFeatureOptions {
  featureName: string; // e.g., 'promo_bar'
  position?: string;   // e.g., 'top1'
  templateName?: string;
}

export class ScaffoldService {
  constructor(
    private workspaceRoot: string,
    private defaultTemplate: string = 'shaper_helixultimate'
  ) {}

  /**
   * Scaffolds a clean Joomla 4/5/6 template override inside templates/<templateName>/html/
   */
  public scaffoldOverride(options: ScaffoldOverrideOptions): { relativePath: string; fullPath: string } {
    const template = options.templateName || this.defaultTemplate;
    const layoutFileName = options.layoutFile || 'default.php';
    const relativeDir = path.join(
      'templates',
      template,
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
 * @subpackage  ${template}
 * @copyright   Copyright (C) ${new Date().getFullYear()} JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\\CMS\\Factory;
use Joomla\\CMS\\HTML\\HTMLHelper;
use Joomla\\CMS\\Language\\Text;

/**
 * Modern Joomla 4/5/6 Template Override
 * Standardized for Helix Ultimate template: ${template}
 */
?>
<div class="${options.extension}-${options.view} helix-override">
    <!-- Helix Ultimate template override content -->
    <header class="page-header mb-4">
        <h2><?php echo Text::_('JGLOBAL_ARTICLES'); ?></h2>
    </header>

    <div class="content-body">
        <?php // Custom template override logic goes here ?>
    </div>
</div>
`;

    fs.writeFileSync(fullPath, content, 'utf-8');
    Logger.info(`Scaffolded override: ${relativePath}`);

    return { relativePath, fullPath };
  }

  /**
   * Scaffolds a new Helix feature class inside templates/<templateName>/features/
   */
  public scaffoldFeature(options: ScaffoldFeatureOptions): { relativePath: string; fullPath: string } {
    const template = options.templateName || this.defaultTemplate;
    const normalizedName = options.featureName.toLowerCase().replace(/[^a-z0-9_]/g, '_');
    const className = normalizedName
      .split('_')
      .map(part => part.charAt(0).toUpperCase() + part.slice(1))
      .join('');

    const relativeDir = path.join('templates', template, 'features');
    const fullDir = PathGuard.assertWithin(relativeDir, this.workspaceRoot);
    if (!fs.existsSync(fullDir)) {
      fs.mkdirSync(fullDir, { recursive: true });
    }

    const relativePath = path.join(relativeDir, `${normalizedName}.php`);
    const fullPath = PathGuard.assertWithin(relativePath, this.workspaceRoot);
    if (fs.existsSync(fullPath)) {
      throw new Error(`Feature file already exists at: ${relativePath}`);
    }

    const content = `<?php
/**
 * @package     Helix Ultimate Framework
 * @subpackage  ${template}
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
    protected $helix;
    protected $params;
    protected $position = '${options.position || 'top1'}';

    public function __construct($helix = null)
    {
        $this->helix  = $helix;
        $this->params = $helix ? $helix->params : null;
    }

    /**
     * Initializes feature settings and prerequisites.
     */
    public function init()
    {
        // Feature initialization logic
    }

    /**
     * Renders feature HTML output.
     *
     * @return string|void
     */
    public function render()
    {
        if (!$this->params || !$this->params->get('${normalizedName}_enable', 1)) {
            return;
        }

        $output  = '<div class="helix-feature-${normalizedName} d-flex align-items-center">';
        $output .= '    <span class="feature-content">';
        $output .= '        ' . htmlspecialchars($this->params->get('${normalizedName}_text', '${className} Feature'), ENT_QUOTES, 'UTF-8');
        $output .= '    </span>';
        $output .= '</div>';

        return $output;
    }
}
`;

    fs.writeFileSync(fullPath, content, 'utf-8');
    Logger.info(`Scaffolded Helix feature: ${relativePath}`);

    return { relativePath, fullPath };
  }
}
