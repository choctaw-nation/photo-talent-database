export type ToastType = 'success' | 'error' | 'info' | 'warning';
export interface SaveListFormData extends FormData {
	listName: string;
	listExpirationLength: string;
	listExpirationUnit: 'days' | 'weeks' | 'months';
	listDescription: string;
}

export type APIResponse = {
	success: boolean;
	posts: PostData[];
};

export interface GetTalentResponse {
	success: boolean;
	posts: PostData[];
}

// /talent-list POST response
export interface TalentListPost {
	id: number;
	title: string;
	link: string;
	talent_ids: number[];
	description?: string;
}

export interface SaveTalentListResponse {
	success: boolean;
	message: string;
	data: {
		post: TalentListPost;
	};
}

// /talent-list/{id} DELETE response
export interface RemoveTalentResponse {
	success: boolean;
	message: string;
	data: {
		id: number;
		remainingSelected: number[];
		removedTalent: number;
	};
}

export type PostData = {
	id: number;
	title: string;
	isChoctaw?: boolean;
	images?: Image | { all: Image };
	lastUsed?: string;
	contact?: {
		email: string;
		phone: string;
	};
};

export type Image = {
	front: ImageDetails;
	back: ImageDetails;
	left: ImageDetails;
	right: ImageDetails;
	three_quarters: ImageDetails;
};

export type ImageDetails = {
	url: string;
	alt?: string;
	sizes: string;
	srcset: string;
};
