export interface HelixColumnSettings {
  column_type: number; // 0 for module position, 1 for component/mainbody
  name?: string; // module position name, e.g. "title", "left", "right"
  grid_size: number; // 1 to 12
  custom_class?: string;
  sm_col?: string;
  xs_col?: string;
  hidden_xs?: number;
  hidden_sm?: number;
  hidden_md?: number;
}

export interface HelixColumn {
  type: 'sp_col';
  settings: HelixColumnSettings;
}

export interface HelixRowSettings {
  name: string;
  fluidrow?: number;
  custom_class?: string;
  padding?: string;
  margin?: string;
  color?: string;
  link_color?: string;
  link_hover_color?: string;
  background_color?: string;
  background_image?: string;
  hidden_xs?: number;
  hidden_sm?: number;
  hidden_md?: number;
}

export interface HelixRow {
  type: 'row';
  layout: string | number; // e.g. 12, "6+6", "4+4+4", "3+3+3+3"
  settings: HelixRowSettings;
  attr: HelixColumn[];
}

export type HelixLayout = HelixRow[];
