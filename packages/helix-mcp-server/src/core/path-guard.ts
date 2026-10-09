import path from 'node:path';

/**
 * PathGuard prevents path traversal attacks across the workspace filesystem.
 */
export class PathGuard {
  /**
   * Asserts that targetPath resolves within the workspace root.
   * Throws an Error if a traversal attempt is detected.
   */
  public static assertWithin(targetPath: string, workspaceRoot: string): string {
    const resolvedRoot = path.resolve(workspaceRoot);
    const resolvedTarget = path.resolve(workspaceRoot, targetPath);

    if (!resolvedTarget.startsWith(resolvedRoot)) {
      throw new Error(`Security Violation: Path '${targetPath}' resolves outside workspace root '${workspaceRoot}'`);
    }

    return resolvedTarget;
  }
}
