import fs from 'node:fs';
import path from 'node:path';
import { XMLParser } from 'fast-xml-parser';
import { Logger } from './logger.js';
import { PathGuard } from './path-guard.js';

export interface HelixWorkspaceInfo {
  joomlaRoot: string;
  hasConfiguration: boolean;
  pluginPath: string | null;
  pluginVersion: string | null;
  templatePath: string | null;
  templateVersion: string | null;
  registeredPositions: string[];
}

export class JoomlaDetector {
  private workspaceRoot: string;
  private xmlParser: XMLParser;

  constructor(startPath: string = process.cwd()) {
    this.workspaceRoot = this.findJoomlaRoot(startPath);
    this.xmlParser = new XMLParser({
      ignoreAttributes: false,
      attributeNamePrefix: '@_'
    });
  }

  public getRoot(): string {
    return this.workspaceRoot;
  }

  /**
   * Discovers the root directory by checking configuration.php or standard Joomla folders.
   */
  private findJoomlaRoot(startPath: string): string {
    let current = path.resolve(startPath);

    while (current !== path.dirname(current)) {
      const hasConfig = fs.existsSync(path.join(current, 'configuration.php'));
      const hasPlugins = fs.existsSync(path.join(current, 'plugins', 'system', 'helixultimate'));
      const hasTemplates = fs.existsSync(path.join(current, 'templates', 'shaper_helixultimate'));

      if (hasConfig || (hasPlugins && hasTemplates)) {
        Logger.debug(`Detected Joomla/Helix root at: ${current}`);
        return current;
      }
      current = path.dirname(current);
    }

    // Default to the provided startPath if not found
    return path.resolve(startPath);
  }

  /**
   * Scans and returns workspace metadata.
   */
  public detect(): HelixWorkspaceInfo {
    const joomlaRoot = this.workspaceRoot;
    const hasConfiguration = fs.existsSync(path.join(joomlaRoot, 'configuration.php'));

    // Check plugin
    const pluginRel = path.join('plugins', 'system', 'helixultimate');
    const pluginFull = path.join(joomlaRoot, pluginRel);
    let pluginPath: string | null = null;
    let pluginVersion: string | null = null;

    if (fs.existsSync(pluginFull)) {
      pluginPath = pluginRel;
      const manifestPath = path.join(pluginFull, 'helixultimate.xml');
      if (fs.existsSync(manifestPath)) {
        try {
          const content = fs.readFileSync(manifestPath, 'utf-8');
          const parsed = this.xmlParser.parse(content);
          pluginVersion = parsed?.extension?.version || null;
        } catch (err) {
          Logger.warn(`Failed to parse plugin XML manifest: ${err}`);
        }
      }
    }

    // Check template
    const templateRel = path.join('templates', 'shaper_helixultimate');
    const templateFull = path.join(joomlaRoot, templateRel);
    let templatePath: string | null = null;
    let templateVersion: string | null = null;
    let registeredPositions: string[] = [];

    if (fs.existsSync(templateFull)) {
      templatePath = templateRel;
      const templateXml = path.join(templateFull, 'templateDetails.xml');
      if (fs.existsSync(templateXml)) {
        try {
          const content = fs.readFileSync(templateXml, 'utf-8');
          const parsed = this.xmlParser.parse(content);
          templateVersion = parsed?.extension?.version || null;

          const positionsNode = parsed?.extension?.positions?.position;
          if (Array.isArray(positionsNode)) {
            registeredPositions = positionsNode.map(String);
          } else if (positionsNode) {
            registeredPositions = [String(positionsNode)];
          }
        } catch (err) {
          Logger.warn(`Failed to parse templateDetails.xml: ${err}`);
        }
      }
    }

    return {
      joomlaRoot,
      hasConfiguration,
      pluginPath,
      pluginVersion,
      templatePath,
      templateVersion,
      registeredPositions
    };
  }

  /**
   * Safely reads a file within the workspace.
   */
  public readFile(relativePath: string): string {
    const safePath = PathGuard.assertWithin(relativePath, this.workspaceRoot);
    if (!fs.existsSync(safePath)) {
      throw new Error(`File not found: ${relativePath}`);
    }
    return fs.readFileSync(safePath, 'utf-8');
  }

  /**
   * Safely checks if a file exists within the workspace.
   */
  public fileExists(relativePath: string): boolean {
    try {
      const safePath = PathGuard.assertWithin(relativePath, this.workspaceRoot);
      return fs.existsSync(safePath);
    } catch {
      return false;
    }
  }
}
