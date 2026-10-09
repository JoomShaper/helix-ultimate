import fs from 'node:fs';
import path from 'node:path';
import { Logger } from '../../core/logger.js';
import { PathGuard } from '../../core/path-guard.js';

export interface SnapshotMeta {
  id: string;
  timestamp: string;
  action: string;
  filePath: string;
  originalContent: string;
}

export class BackupService {
  private snapshotDir: string;

  constructor(private workspaceRoot: string) {
    this.snapshotDir = path.join(this.workspaceRoot, '.helix-mcp', 'snapshots');
    if (!fs.existsSync(this.snapshotDir)) {
      fs.mkdirSync(this.snapshotDir, { recursive: true });
    }
  }

  /**
   * Captures a snapshot of a file before mutation.
   */
  public captureSnapshot(relativePath: string, action: string): string {
    const fullPath = PathGuard.assertWithin(relativePath, this.workspaceRoot);
    if (!fs.existsSync(fullPath)) {
      throw new Error(`Cannot snapshot non-existent file: ${relativePath}`);
    }

    const content = fs.readFileSync(fullPath, 'utf-8');
    const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
    const snapshotId = `${timestamp}_${action.replace(/[^a-zA-Z0-9_-]/g, '_')}`;
    const snapshotFilePath = path.join(this.snapshotDir, `${snapshotId}.json`);

    const meta: SnapshotMeta = {
      id: snapshotId,
      timestamp: new Date().toISOString(),
      action,
      filePath: relativePath,
      originalContent: content
    };

    fs.writeFileSync(snapshotFilePath, JSON.stringify(meta, null, 2), 'utf-8');
    Logger.info(`Snapshot captured: ${snapshotId} for ${relativePath}`);
    return snapshotId;
  }

  /**
   * Restores the most recent snapshot.
   */
  public rollbackLatest(): { snapshotId: string; restoredFile: string } {
    const files = fs.readdirSync(this.snapshotDir)
      .filter(f => f.endsWith('.json'))
      .sort()
      .reverse();

    if (files.length === 0) {
      throw new Error('No snapshots available for rollback.');
    }

    const latestFile = path.join(this.snapshotDir, files[0]);
    const raw = fs.readFileSync(latestFile, 'utf-8');
    const meta: SnapshotMeta = JSON.parse(raw);

    const targetPath = PathGuard.assertWithin(meta.filePath, this.workspaceRoot);
    fs.writeFileSync(targetPath, meta.originalContent, 'utf-8');

    // Remove the applied snapshot
    fs.unlinkSync(latestFile);
    Logger.info(`Rollback successful: Restored ${meta.filePath} to snapshot ${meta.id}`);

    return {
      snapshotId: meta.id,
      restoredFile: meta.filePath
    };
  }
}
