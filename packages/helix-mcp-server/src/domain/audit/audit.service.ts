import fs from 'node:fs';
import path from 'node:path';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';
import { LayoutService } from '../layout/layout.service.js';

export interface AuditViolation {
  file: string;
  line: number;
  rule: string;
  severity: 'error' | 'warning' | 'info';
  snippet: string;
  recommendation: string;
}

export interface AuditReport {
  timestamp: string;
  totalFilesScanned: number;
  totalViolations: number;
  violations: AuditViolation[];
}

export class AuditService {
  constructor(
    private workspaceRoot: string,
    private layoutService?: LayoutService,
    private registeredPositions: string[] = []
  ) {}

  /**
   * Performs full compatibility and architecture audit.
   */
  public async runAudit(targetRelativeDir?: string): Promise<AuditReport> {
    const startDir = targetRelativeDir || path.join('templates', 'shaper_helixultimate');
    const fullDir = PathGuard.assertWithin(startDir, this.workspaceRoot);

    const violations: AuditViolation[] = [];
    let scannedFilesCount = 0;

    const walkAndScan = (currentDir: string): void => {
      const entries = fs.readdirSync(currentDir, { withFileTypes: true });

      for (const entry of entries) {
        const fullPath = path.join(currentDir, entry.name);
        const relativePath = path.relative(this.workspaceRoot, fullPath);

        // Skip node_modules, .git, vendor, cache, tmp
        if (entry.isDirectory()) {
          if (!['node_modules', '.git', 'vendor', 'cache', 'tmp', 'dist'].includes(entry.name)) {
            walkAndScan(fullPath);
          }
          continue;
        }

        // Only scan PHP and JS files
        const ext = path.extname(entry.name).toLowerCase();
        if (ext === '.php' || ext === '.js') {
          scannedFilesCount++;
          this.scanFile(fullPath, relativePath, ext, violations);
        }
      }
    };

    if (fs.existsSync(fullDir)) {
      walkAndScan(fullDir);
    }

    // Also audit layout module positions if layout service is available
    if (this.layoutService && this.registeredPositions.length > 0) {
      try {
        const layout = await this.layoutService.getLayout();
        layout.forEach((row, rIndex) => {
          row.attr?.forEach((col) => {
            if (col.settings?.column_type === 0 && col.settings?.name) {
              const posName = col.settings.name;
              if (!this.registeredPositions.includes(posName)) {
                violations.push({
                  file: 'templates/shaper_helixultimate/options.json',
                  line: rIndex + 1,
                  rule: 'unregistered-module-position',
                  severity: 'warning',
                  snippet: `Position: "${posName}" in row "${row.settings?.name || rIndex}"`,
                  recommendation: `Add <position>${posName}</position> to templates/shaper_helixultimate/templateDetails.xml.`
                });
              }
            }
          });
        });
      } catch (err) {
        Logger.warn(`Could not audit layout positions: ${err}`);
      }
    }

    Logger.info(`Audit completed: ${scannedFilesCount} files scanned, ${violations.length} violations found.`);

    return {
      timestamp: new Date().toISOString(),
      totalFilesScanned: scannedFilesCount,
      totalViolations: violations.length,
      violations
    };
  }

  private scanFile(fullPath: string, relativePath: string, ext: string, violations: AuditViolation[]): void {
    const content = fs.readFileSync(fullPath, 'utf-8');
    const lines = content.split('\n');

    lines.forEach((lineText, index) => {
      const lineNum = index + 1;
      const trimmed = lineText.trim();

      // Ignore comments
      if (trimmed.startsWith('//') || trimmed.startsWith('*') || trimmed.startsWith('/*')) {
        return;
      }

      if (ext === '.php') {
        // Rule: Deprecated JFactory / Joomla 3 legacy calls
        if (lineText.includes('JFactory::')) {
          violations.push({
            file: relativePath,
            line: lineNum,
            rule: 'deprecated-jfactory',
            severity: 'error',
            snippet: trimmed,
            recommendation: 'Replace legacy JFactory with Joomla\\CMS\\Factory and namespaced classes.'
          });
        }

        if (lineText.includes('JRequest::')) {
          violations.push({
            file: relativePath,
            line: lineNum,
            rule: 'deprecated-jrequest',
            severity: 'error',
            snippet: trimmed,
            recommendation: 'Replace JRequest with Joomla\\CMS\\Factory::getApplication()->getInput().'
          });
        }

        if (lineText.includes('JText::_(')) {
          violations.push({
            file: relativePath,
            line: lineNum,
            rule: 'deprecated-jtext',
            severity: 'warning',
            snippet: trimmed,
            recommendation: 'Replace JText::_ with Joomla\\CMS\\Language\\Text::_().'
          });
        }
      }

      if (ext === '.js') {
        // Rule: jQuery usage in frontend scripts
        if (
          /(^|[^\w$])(jQuery|\$)\s*\(/.test(lineText) &&
          !lineText.includes('function($)') &&
          !lineText.includes('jQuery.extend')
        ) {
          violations.push({
            file: relativePath,
            line: lineNum,
            rule: 'legacy-jquery-usage',
            severity: 'warning',
            snippet: trimmed,
            recommendation: 'Refactor jQuery to modern ES6 Vanilla JS (document.querySelector, addEventListener).'
          });
        }

        if (lineText.includes('$.ajax') || lineText.includes('jQuery.ajax')) {
          violations.push({
            file: relativePath,
            line: lineNum,
            rule: 'legacy-jquery-ajax',
            severity: 'error',
            snippet: trimmed,
            recommendation: 'Replace $.ajax with standard fetch() API.'
          });
        }
      }
    });
  }
}
