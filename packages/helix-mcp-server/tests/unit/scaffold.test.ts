import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { ScaffoldService } from '../../src/domain/scaffolding/scaffold.service.js';
import { BackupService } from '../../src/domain/backup/backup.service.js';

describe('ScaffoldService & BackupService', () => {
  let tempDir: string;

  beforeEach(() => {
    tempDir = fs.mkdtempSync(path.join(os.tmpdir(), 'helix-test-'));
    // Setup dummy templates structure
    fs.mkdirSync(path.join(tempDir, 'templates', 'shaper_helixultimate', 'html'), { recursive: true });
    fs.mkdirSync(path.join(tempDir, 'templates', 'shaper_helixultimate', 'features'), { recursive: true });
  });

  afterEach(() => {
    fs.rmSync(tempDir, { recursive: true, force: true });
  });

  it('scaffolds a clean template override with PHP 8 and Joomla namespaces', () => {
    const scaffolder = new ScaffoldService(tempDir);
    const result = scaffolder.scaffoldOverride({
      type: 'component',
      extension: 'com_content',
      view: 'article',
      layoutFile: 'default.php'
    });

    expect(fs.existsSync(result.fullPath)).toBe(true);
    const content = fs.readFileSync(result.fullPath, 'utf-8');
    expect(content).toContain("use Joomla\\CMS\\Factory;");
    expect(content).toContain("helix-override");
    expect(content).not.toContain("JFactory::");
  });

  it('scaffolds a clean feature class adhering to Helix naming conventions', () => {
    const scaffolder = new ScaffoldService(tempDir);
    const result = scaffolder.scaffoldFeature({
      featureName: 'cookie_banner'
    });

    expect(fs.existsSync(result.fullPath)).toBe(true);
    const content = fs.readFileSync(result.fullPath, 'utf-8');
    expect(content).toContain('class HelixUltimateFeatureCookieBanner');
    expect(content).toContain('public function render()');
  });

  it('captures backup snapshot and successfully rolls back', () => {
    const backup = new BackupService(tempDir);
    const sampleFile = path.join(tempDir, 'templates', 'shaper_helixultimate', 'sample.txt');
    fs.writeFileSync(sampleFile, 'Initial Version', 'utf-8');

    // 1. Snapshot
    const snapshotId = backup.captureSnapshot('templates/shaper_helixultimate/sample.txt', 'test_action');
    expect(snapshotId).toBeDefined();

    // 2. Mutate
    fs.writeFileSync(sampleFile, 'Modified Version', 'utf-8');
    expect(fs.readFileSync(sampleFile, 'utf-8')).toBe('Modified Version');

    // 3. Rollback
    const rollbackResult = backup.rollbackLatest();
    expect(rollbackResult.snapshotId).toBe(snapshotId);
    expect(fs.readFileSync(sampleFile, 'utf-8')).toBe('Initial Version');
  });
});
