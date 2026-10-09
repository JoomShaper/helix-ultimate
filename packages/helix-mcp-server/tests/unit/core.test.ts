import { describe, it, expect } from 'vitest';
import path from 'node:path';
import { PathGuard } from '../../src/core/path-guard.js';
import { JoomlaDetector } from '../../src/core/joomla-detector.js';

describe('PathGuard', () => {
  const root = '/Users/siddiqur/Sites/helixultimatedev';

  it('allows safe relative paths inside workspace', () => {
    const resolved = PathGuard.assertWithin('templates/shaper_helixultimate', root);
    expect(resolved).toBe(path.join(root, 'templates/shaper_helixultimate'));
  });

  it('throws an error on path traversal attempts outside workspace', () => {
    expect(() => {
      PathGuard.assertWithin('../../etc/passwd', root);
    }).toThrowError(/Security Violation/);
  });
});

describe('JoomlaDetector', () => {
  it('detects workspace root and helix components correctly', () => {
    const detector = new JoomlaDetector(process.cwd());
    const info = detector.detect();

    expect(info.joomlaRoot).toBeDefined();
    expect(info.registeredPositions.length).toBeGreaterThan(0);
    expect(info.templatePath).toContain('templates/shaper_helixultimate');
    expect(info.pluginPath).toContain('plugins/system/helixultimate');
  });
});
