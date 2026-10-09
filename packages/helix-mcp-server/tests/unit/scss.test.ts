import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { ScssService } from '../../src/domain/scss/scss.service.js';

describe('ScssService', () => {
  let tempDir: string;

  beforeEach(() => {
    tempDir = fs.mkdtempSync(path.join(os.tmpdir(), 'helix-scss-test-'));
    fs.mkdirSync(path.join(tempDir, 'templates', 'shaper_helixultimate', 'scss'), { recursive: true });
  });

  afterEach(() => {
    fs.rmSync(tempDir, { recursive: true, force: true });
  });

  it('successfully compiles valid SCSS', () => {
    const scssPath = path.join(tempDir, 'templates', 'shaper_helixultimate', 'scss', 'theme.scss');
    fs.writeFileSync(scssPath, '$primary: #0345bf; body { color: $primary; }', 'utf-8');

    const service = new ScssService(tempDir);
    const result = service.compile();

    expect(result.success).toBe(true);
    expect(result.compiledCss).toContain('color: #0345bf;');
    expect(result.cssSize).toBeGreaterThan(0);
  });

  it('returns structured line and column diagnostics on syntax error', () => {
    const scssPath = path.join(tempDir, 'templates', 'shaper_helixultimate', 'scss', 'theme.scss');
    fs.writeFileSync(scssPath, 'body { color: $undefined_variable; }', 'utf-8');

    const service = new ScssService(tempDir);
    const result = service.compile();

    expect(result.success).toBe(false);
    expect(result.error).toBeDefined();
    expect(result.error?.message).toContain('Undefined variable');
    expect(result.error?.line).toBe(1);
  });
});
