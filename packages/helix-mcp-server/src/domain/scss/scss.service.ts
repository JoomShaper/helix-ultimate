import fs from 'node:fs';
import path from 'node:path';
import * as sass from 'sass';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';

export interface CompileScssOptions {
  targetFile?: string; // e.g. "templates/shaper_helixultimate/scss/theme.scss"
  outputStyle?: 'expanded' | 'compressed';
  sourceMap?: boolean;
}

export interface CompileScssResult {
  success: boolean;
  cssSize?: number;
  durationMs: number;
  compiledCss?: string;
  error?: {
    message: string;
    line?: number;
    column?: number;
    file?: string;
    formatted?: string;
  };
}

export class ScssService {
  constructor(private workspaceRoot: string) {}

  /**
   * Compiles SCSS file using embedded Dart Sass compiler.
   */
  public compile(options: CompileScssOptions = {}): CompileScssResult {
    const startTime = Date.now();
    const relativeTarget = options.targetFile || path.join('templates', 'shaper_helixultimate', 'scss', 'theme.scss');
    const fullTarget = PathGuard.assertWithin(relativeTarget, this.workspaceRoot);

    if (!fs.existsSync(fullTarget)) {
      return {
        success: false,
        durationMs: Date.now() - startTime,
        error: {
          message: `Target SCSS file not found: ${relativeTarget}`
        }
      };
    }

    const scssDir = path.dirname(fullTarget);
    const loadPaths = [
      scssDir,
      path.join(this.workspaceRoot, 'templates', 'shaper_helixultimate', 'scss'),
      path.join(this.workspaceRoot, 'media')
    ];

    try {
      const result = sass.compile(fullTarget, {
        style: options.outputStyle || 'expanded',
        sourceMap: options.sourceMap ?? false,
        loadPaths
      });

      const durationMs = Date.now() - startTime;
      const cssSize = Buffer.byteLength(result.css, 'utf-8');

      Logger.info(`Compiled SCSS successfully: ${relativeTarget} (${cssSize} bytes in ${durationMs}ms)`);

      return {
        success: true,
        cssSize,
        durationMs,
        compiledCss: result.css
      };
    } catch (err: any) {
      const durationMs = Date.now() - startTime;
      Logger.error(`SCSS compilation error in ${relativeTarget}: ${err?.message}`);

      // Sass span.start.line is 0-indexed, normalize to 1-indexed for developers & AI
      const startLine = typeof err?.span?.start?.line === 'number' ? err.span.start.line + 1 : undefined;
      const startCol = typeof err?.span?.start?.column === 'number' ? err.span.start.column + 1 : undefined;

      return {
        success: false,
        durationMs,
        error: {
          message: err?.message || String(err),
          line: startLine,
          column: startCol,
          file: err?.span?.url?.pathname || relativeTarget,
          formatted: err?.toString()
        }
      };
    }
  }
}
