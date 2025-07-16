export type ToastType = 'success' | 'error' | 'info' | 'warning';
export interface SaveListFormData extends FormData {
	listName: string;
	listExpirationLength: string;
	listExpirationUnit: 'days' | 'weeks' | 'months';
	listDescription: string;
}
