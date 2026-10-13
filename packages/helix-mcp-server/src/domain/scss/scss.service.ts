import fs from 'node:fs';
import path from 'node:path';
import * as sass from 'sass';
import { PathGuard } from '../../core/path-guard.js';
import { Logger } from '../../core/logger.js';

export interface CompileScssOptions {
  targetFile?: string; // e.g. "templates/<templateName>/scss/theme.scss"
  templateName?: string;
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
  constructor(
    private workspaceRoot: string,
    private defaultTemplate: string = 'shaper_helixultimate'
  ) {}

  /**
   * Compiles SCSS file using embedded Dart Sass compiler for any Helix Ultimate template.
   */
  public compile(options: CompileScssOptions = {}): CompileScssResult {
    const startTime = Date.now();
    const targetTemplate = options.templateName || this.defaultTemplate;
    const relativeTarget = options.targetFile || path.join('templates', targetTemplate, 'scss', 'theme.scss');
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
      path.join(this.workspaceRoot, 'templates', targetTemplate, 'scss'),
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
        durationMs,
        cssSize,
        compiledCss: result.css
      };
    } catch (err: any) {
      const durationMs = Date.now() - startTime;
      Logger.error(`SCSS compilation error in ${relativeTarget}: ${err?.message || err}`);

      return {
        success: false,
        durationMs,
        error: {
          message: err?.message || 'SCSS compilation failed',
          line: err?.span?.start?.line !== undefined ? err.span.start.line + 1 : undefined,
          column: err?.span?.start?.column !== undefined ? err.span.start.column + 1 : undefined,
          file: err?.span?.url?.pathname || relativeTarget,
          formatted: err?.toString()
        }
      };
    }
  }
}
