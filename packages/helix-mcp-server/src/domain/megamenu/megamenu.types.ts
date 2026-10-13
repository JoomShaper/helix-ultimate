export interface MegaMenuItemCell {
  type: 'menu_item' | 'module';
  item_id?: string;
  moduleId?: string;
}

export interface MegaMenuColumn {
  type: 'column';
  colGrid: string; // "1" - "12"
  menuParentId?: string;
  moduleId?: string;
  items?: MegaMenuItemCell[];
}

export interface MegaMenuRow {
  type: 'row';
  attr: MegaMenuColumn[];
}

export interface MegaMenuItemSettings {
  megamenu: number; // 0 or 1
  menualign?: 'left' | 'right' | 'center' | 'full';
  dropdown?: 'left' | 'right';
  badge_position?: 'top' | 'bottom';
  width?: string;
  customclass?: string;
  faicon?: string;
  badge?: string;
  badge_bg_color?: string;
  badge_text_color?: string;
  layout?: MegaMenuRow[];
}

export type HelixMegaMenuMap = Record<string, MegaMenuItemSettings>;
