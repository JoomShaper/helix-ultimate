import fs from 'node:fs';
import path from 'node:path';
import { XMLParser } from 'fast-xml-parser';
import { Logger } from './logger.js';

export interface HelixWorkspaceInfo {
  joomlaRoot: string;
  hasConfiguration: boolean;
  pluginPath: string | null;
  pluginVersion: string | null;
  templateName: string;
  templatePath: string | null;
  templateVersion: string | null;
  registeredPositions: string[];
  detectedTemplates: string[];
}

export class JoomlaDetector {
  private workspaceRoot: string;
  private xmlParser: XMLParser;

  constructor(startPath: string = process.cwd()) {
    this.xmlParser = new XMLParser({
      ignoreAttributes: false,
      attributeNamePrefix: '@_'
    });
    this.workspaceRoot = this.findJoomlaRoot(startPath);
  }

  public getRoot(): string {
    return this.workspaceRoot;
  }

  public fileExists(relativePath: string): boolean {
    return fs.existsSync(path.join(this.workspaceRoot, relativePath));
  }

  public readFile(relativePath: string): string {
    return fs.readFileSync(path.join(this.workspaceRoot, relativePath), 'utf-8');
  }

  /**
   * Discovers the root directory by checking configuration.php or standard Joomla folders.
   */
  private findJoomlaRoot(startPath: string): string {
    let current = path.resolve(startPath);

    while (current !== path.dirname(current)) {
      const hasConfig = fs.existsSync(path.join(current, 'configuration.php'));
      const hasPlugins = fs.existsSync(path.join(current, 'plugins', 'system', 'helixultimate'));
      const templatesDir = path.join(current, 'templates');
      const hasTemplates = fs.existsSync(templatesDir) && this.findHelixTemplates(current).length > 0;

      if (hasConfig || (hasPlugins && hasTemplates)) {
        Logger.debug(`Detected Joomla/Helix root at: ${current}`);
        return current;
      }
      current = path.dirname(current);
    }

    return path.resolve(startPath);
  }

  /**
   * Checks if a directory inside templates/ is a Helix Ultimate based template.
   */
  public isHelixTemplate(fullPath: string): boolean {
    if (!fs.existsSync(fullPath) || !fs.statSync(fullPath).isDirectory()) {
      return false;
    }

    // 1. Has features/ directory (classic Helix feature provider pattern)
    if (fs.existsSync(path.join(fullPath, 'features'))) {
      return true;
    }

    // 2. Has options.json with layout or helixultimatemenulayout
    const optionsPath = path.join(fullPath, 'options.json');
    if (fs.existsSync(optionsPath)) {
      try {
        const content = fs.readFileSync(optionsPath, 'utf-8');
        if (content.includes('"layout"') || content.includes('helixultimatemenulayout')) {
          return true;
        }
      } catch {}
    }

    // 3. templateDetails.xml mentions helix
    const xmlPath = path.join(fullPath, 'templateDetails.xml');
    if (fs.existsSync(xmlPath)) {
      try {
        const content = fs.readFileSync(xmlPath, 'utf-8');
        if (/helix/i.test(content)) {
          return true;
        }
      } catch {}
    }

    // 4. index.php loads Helix Framework
    const indexPath = path.join(fullPath, 'index.php');
    if (fs.existsSync(indexPath)) {
      try {
        const content = fs.readFileSync(indexPath, 'utf-8');
        if (content.includes('HelixUltimate') || content.includes('helix')) {
          return true;
        }
      } catch {}
    }

    return false;
  }

  /**
   * Scans templates/ to discover all templates based on Helix Ultimate.
   */
  public findHelixTemplates(rootDir: string = this.workspaceRoot): string[] {
    const templatesDir = path.join(rootDir, 'templates');
    if (!fs.existsSync(templatesDir)) {
      return [];
    }

    try {
      const entries = fs.readdirSync(templatesDir, { withFileTypes: true });
      const helixTemplates: string[] = [];

      for (const entry of entries) {
        if (entry.isDirectory()) {
          const fullPath = path.join(templatesDir, entry.name);
          if (this.isHelixTemplate(fullPath)) {
            helixTemplates.push(entry.name);
          }
        }
      }

      return helixTemplates;
    } catch (err) {
      Logger.warn(`Could not scan templates directory: ${err}`);
      return [];
    }
  }

  /**
   * Extracts registered module positions for a specific template.
   */
  public getTemplatePositions(templateName: string): string[] {
    const templateXml = path.join(this.workspaceRoot, 'templates', templateName, 'templateDetails.xml');
    if (!fs.existsSync(templateXml)) {
      return [];
    }

    try {
      const content = fs.readFileSync(templateXml, 'utf-8');
      const parsed = this.xmlParser.parse(content);
      const positions = parsed?.extension?.positions?.position;

      if (Array.isArray(positions)) {
        return positions.map(String);
      } else if (typeof positions === 'string') {
        return [positions];
      }
    } catch (err) {
      Logger.warn(`Failed to parse templateDetails.xml for ${templateName}: ${err}`);
    }

    return [];
  }

  /**
   * Scans and returns workspace metadata with dynamic template detection.
   */
  public detect(preferredTemplate?: string): HelixWorkspaceInfo {
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

    // Discover all Helix templates
    const detectedTemplates = this.findHelixTemplates(joomlaRoot);

    // Pick active template (preferred, or shaper_helixultimate if present, or first detected)
    let activeTemplateName = preferredTemplate || '';
    if (!activeTemplateName || !detectedTemplates.includes(activeTemplateName)) {
      if (detectedTemplates.includes('shaper_helixultimate')) {
        activeTemplateName = 'shaper_helixultimate';
      } else if (detectedTemplates.length > 0) {
        activeTemplateName = detectedTemplates[0];
      } else {
        activeTemplateName = 'shaper_helixultimate';
      }
    }

    const templateRel = path.join('templates', activeTemplateName);
    const templateFull = path.join(joomlaRoot, templateRel);
    let templatePath: string | null = null;
    let templateVersion: string | null = null;

    if (fs.existsSync(templateFull)) {
      templatePath = templateRel;
      const templateXml = path.join(templateFull, 'templateDetails.xml');
      if (fs.existsSync(templateXml)) {
        try {
          const content = fs.readFileSync(templateXml, 'utf-8');
          const parsed = this.xmlParser.parse(content);
          templateVersion = parsed?.extension?.version || null;
        } catch (err) {
          Logger.warn(`Failed to parse templateDetails.xml: ${err}`);
        }
      }
    }

    const registeredPositions = this.getTemplatePositions(activeTemplateName);

    return {
      joomlaRoot,
      hasConfiguration,
      pluginPath,
      pluginVersion,
      templateName: activeTemplateName,
      templatePath,
      templateVersion,
      registeredPositions,
      detectedTemplates
    };
  }
}
