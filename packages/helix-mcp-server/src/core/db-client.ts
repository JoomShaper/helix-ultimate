import fs from 'node:fs';
import path from 'node:path';
import mysql, { Connection } from 'mysql2/promise';
import { Logger } from './logger.js';

export interface JoomlaDbConfig {
  host: string;
  user: string;
  password: string;
  database: string;
  prefix: string;
}

export class JoomlaDbClient {
  private config: JoomlaDbConfig | null = null;

  constructor(private workspaceRoot: string) {
    this.config = this.parseConfiguration();
  }

  private parseConfiguration(): JoomlaDbConfig | null {
    const configPath = path.join(this.workspaceRoot, 'configuration.php');
    if (!fs.existsSync(configPath)) {
      return null;
    }

    try {
      const content = fs.readFileSync(configPath, 'utf-8');
      const getVal = (key: string): string => {
        const match = content.match(new RegExp(`public \\$${key} = '([^']*)';`));
        return match ? match[1] : '';
      };

      const host = getVal('host') || 'localhost';
      const user = getVal('user') || 'root';
      const password = getVal('password');
      const database = getVal('db');
      const prefix = getVal('dbprefix');

      if (!database || !prefix) {
        return null;
      }

      return { host, user, password, database, prefix };
    } catch (err) {
      Logger.warn(`Could not parse configuration.php: ${err}`);
      return null;
    }
  }

  public isConfigured(): boolean {
    return this.config !== null;
  }

  private async getConnection(): Promise<Connection | null> {
    if (!this.config) return null;
    try {
      return await mysql.createConnection({
        host: this.config.host,
        user: this.config.user,
        password: this.config.password,
        database: this.config.database
      });
    } catch (err) {
      Logger.warn(`MySQL connection could not be established: ${err}`);
      return null;
    }
  }

  public async getActiveHelixStyleId(): Promise<number | null> {
    const conn = await this.getConnection();
    if (!conn) return null;

    try {
      const tableName = `${this.config!.prefix}template_styles`;
      const [rows]: any = await conn.execute(
        `SELECT id FROM \`${tableName}\` WHERE template = 'shaper_helixultimate' AND client_id = 0 ORDER BY home DESC, id ASC LIMIT 1`
      );

      await conn.end();
      if (Array.isArray(rows) && rows.length > 0) {
        return rows[0].id;
      }
      return null;
    } catch (err) {
      Logger.warn(`Error querying active template style: ${err}`);
      try { await conn.end(); } catch {}
      return null;
    }
  }

  public async getStyleParams(styleId: number): Promise<Record<string, any> | null> {
    const conn = await this.getConnection();
    if (!conn) return null;

    try {
      const tableName = `${this.config!.prefix}template_styles`;
      const [rows]: any = await conn.execute(
        `SELECT params FROM \`${tableName}\` WHERE id = ?`,
        [styleId]
      );

      await conn.end();
      if (Array.isArray(rows) && rows.length > 0) {
        const rawParams = rows[0].params;
        return typeof rawParams === 'string' ? JSON.parse(rawParams) : rawParams;
      }
      return null;
    } catch (err) {
      Logger.warn(`Error reading params for style ${styleId}: ${err}`);
      try { await conn.end(); } catch {}
      return null;
    }
  }

  public async updateStyleParams(styleId: number, params: Record<string, any>): Promise<boolean> {
    const conn = await this.getConnection();
    if (!conn) return null as any;

    try {
      const tableName = `${this.config!.prefix}template_styles`;
      const jsonStr = JSON.stringify(params);

      await conn.execute(
        `UPDATE \`${tableName}\` SET params = ? WHERE id = ?`,
        [jsonStr, styleId]
      );

      await conn.end();
      Logger.info(`Updated database params for style #${styleId} in ${tableName}`);
      return true;
    } catch (err) {
      Logger.error(`Error updating style params in DB: ${err}`);
      try { await conn.end(); } catch {}
      return false;
    }
  }
}
