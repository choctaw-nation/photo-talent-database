// eslint-disable-next-line camelcase
interface API_Data {
	success: boolean;
	message: string;
}

// eslint-disable-next-line camelcase
export interface SuccessfulApiResponse extends API_Data {
	success: true;
	data: {
		html: string;
	};
}

// eslint-disable-next-line camelcase
export interface ErrorApiResponse extends API_Data {
	success: false;
}

export default class Model {
	async getTalentData(
		id: number
	): Promise< SuccessfulApiResponse | ErrorApiResponse > {
		const response = await fetch( `/wp-json/cno/v1/talent/${ id }` );
		if ( ! response.ok ) {
			return {
				success: false,
				message: 'Failed to fetch talent data',
			};
		}
		const data = await response.json();
		return data as SuccessfulApiResponse | ErrorApiResponse;
	}
}
