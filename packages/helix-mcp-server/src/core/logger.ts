/**
 * Production Stderr-Only Logger
 * 
 * CRITICAL: In STDIO-based MCP servers, stdout is reserved strictly for JSON-RPC messages.
 * Any write to stdout corrupts the communication frame and crashes the AI client.
 * All logging MUST go to stderr.
 */

export type LogLevel = 'debug' | 'info' | 'warn' | 'error';

export class Logger {
  private static verbose = false;

  public static setVerbose(enabled: boolean): void {
    this.verbose = enabled;
  }

  public static debug(message: string, context?: Record<string, unknown>): void {
    if (!this.verbose) return;
    this.write('debug', message, context);
  }

  public static info(message: string, context?: Record<string, unknown>): void {
    this.write('info', message, context);
  }

  public static warn(message: string, context?: Record<string, unknown>): void {
    this.write('warn', message, context);
  }

  public static error(message: string, context?: Record<string, unknown>): void {
    this.write('error', message, context);
  }

  private static write(level: LogLevel, message: string, context?: Record<string, unknown>): void {
    const timestamp = new Date().toISOString();
    const prefix = `[${timestamp}] [helix-mcp] [${level.toUpperCase()}]`;
    const formatted = context 
      ? `${prefix} ${message} ${JSON.stringify(context)}`
      : `${prefix} ${message}`;
    
    process.stderr.write(`${formatted}\n`);
  }
}
