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

// /talent GET response
export interface TalentPost {
	id: number;
	title: string;
	isChoctaw: boolean;
	thumbnail: string;
	lastUsed: string | null;
}

export interface GetTalentResponse {
	success: boolean;
	posts: TalentPost[];
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
	isChoctaw: boolean;
	images: Image;
	lastUsed: string;
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
