import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { AuditService } from '../../src/domain/audit/audit.service.js';

describe('AuditService', () => {
  let tempDir: string;

  beforeEach(() => {
    tempDir = fs.mkdtempSync(path.join(os.tmpdir(), 'helix-audit-test-'));
    fs.mkdirSync(path.join(tempDir, 'templates', 'shaper_helixultimate', 'html'), { recursive: true });
    fs.mkdirSync(path.join(tempDir, 'templates', 'shaper_helixultimate', 'js'), { recursive: true });
  });

  afterEach(() => {
    fs.rmSync(tempDir, { recursive: true, force: true });
  });

  it('detects legacy JFactory and JRequest calls in PHP files', async () => {
    const phpPath = path.join(tempDir, 'templates', 'shaper_helixultimate', 'html', 'legacy.php');
    fs.writeFileSync(
      phpPath,
      `<?php
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$id = JRequest::getInt('id');
`,
      'utf-8'
    );

    const audit = new AuditService(tempDir);
    const report = await audit.runAudit();

    expect(report.totalViolations).toBe(2);
    expect(report.violations.some(v => v.rule === 'deprecated-jfactory')).toBe(true);
    expect(report.violations.some(v => v.rule === 'deprecated-jrequest')).toBe(true);
  });

  it('detects legacy jQuery usage in Javascript files', async () => {
    const jsPath = path.join(tempDir, 'templates', 'shaper_helixultimate', 'js', 'custom.js');
    fs.writeFileSync(
      jsPath,
      `
$(document).ready(function() {
  $.ajax({ url: '/api' });
});
`,
      'utf-8'
    );

    const audit = new AuditService(tempDir);
    const report = await audit.runAudit();

    expect(report.violations.some(v => v.rule === 'legacy-jquery-usage')).toBe(true);
    expect(report.violations.some(v => v.rule === 'legacy-jquery-ajax')).toBe(true);
  });
});
