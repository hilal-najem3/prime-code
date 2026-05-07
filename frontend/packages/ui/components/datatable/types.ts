export type Row = Record<string, any>;

export type Column = {
  key: string;
  label: string;
  sortable?: boolean;
  type?: "text" | "image" | "badge";
};

export type DataTableActionEvent = "edit" | "delete" | "view";
export type DataTableBulkActionEvent = "bulk-edit" | "bulk-delete" | "bulk-view";

export type DataTableChangeParams = {
  page: number;
  per_page: number;
  search: string;
  sort?: string;
  direction?: string;
};

export type Action = {
  label: string;
  event: DataTableActionEvent;
  icon?: any;
  title?: string;
};

export type BulkAction = {
  label: string;
  event: DataTableBulkActionEvent;
};

export type Meta = {
  current_page: number;
  per_page: number;
  total: number;
  last_page?: number;
};
