import createError from './utils/createError';

type APIResponse = {
	success: boolean;
	posts: PostData[];
};

export type PostData = {
	id: number;
	title: string;
	isChoctaw: boolean;
	thumbnail: string;
	lastUsed: string;
};

/**
 * Model. Interacts with localStorage to manage post IDs.
 */
export default class LocalStorage {
	STORAGE_KEY: string;

	constructor() {
		this.STORAGE_KEY = 'cartPostIds';
	}
	// Helper to get unique post IDs from localStorage
	getIds(): Set< number > {
		const raw = localStorage.getItem( this.STORAGE_KEY );
		if ( ! raw ) return new Set();
		try {
			const parsed = JSON.parse( raw );
			if ( ! Array.isArray( parsed ) ) return new Set();
			// Ensure all IDs are numbers
			return new Set(
				parsed
					.map( ( id ) => Number( id ) )
					.filter( ( id ) => ! isNaN( id ) )
			);
		} catch {
			return new Set();
		}
	}

	/**
	 * Save a post ID to localStorage.
	 *
	 * @param id The ID to save.
	 * @throws Will throw an error if the ID is not a number or is less than or equal to zero.
	 * @throws Will throw an error if the ID already exists in localStorage.
	 * @returns boolean
	 */
	saveId( id: number | string ): boolean {
		if ( typeof id === 'string' ) {
			id = Number( id );
		}
		if ( isNaN( id ) ) {
			const error = createError(
				'error',
				`Invalid ID: must be a number`
			);
			throw error;
		}
		if ( id <= 0 ) {
			const error = createError(
				'error',
				`Invalid ID: must be greater than zero`
			);
			throw error;
		}
		const ids = this.getIds();
		if ( ids.has( id ) ) {
			const error = createError(
				'warning',
				`You have already selected this person`
			);
			throw error;
		}
		ids.add( id );
		this.saveIds( ids );
		return true;
	}

	// Helper to save unique post IDs to localStorage
	private saveIds( ids: Set< number > ) {
		localStorage.setItem(
			this.STORAGE_KEY,
			JSON.stringify( Array.from( ids ) )
		);
	}

	clearIds() {
		localStorage.removeItem( this.STORAGE_KEY );
	}

	async getSelectedData(): Promise< PostData[] | [] > {
		const ids = this.getIds();
		if ( ids.size === 0 ) {
			console.warn( 'No IDs found in localStorage' );
			return [];
		}
		const nonce = ( window as any ).cnoApi?.nonce ?? null;
		if ( ! nonce ) {
			console.error( 'API nonce is not available' );
			return [];
		}
		const response = await fetch( `/wp-json/cno/v1/talent`, {
			method: 'POST',
			body: JSON.stringify( { ids: Array.from( ids ) } ),
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': ( window as any ).cnoApi?.nonce ?? '',
			},
		} );
		if ( ! response.ok ) {
			console.error(
				'Failed to fetch selected data:',
				response.statusText
			);
			return [];
		}
		const data: APIResponse = await response.json();
		if ( ! data.success ) {
			console.error( 'API response was not successful:', data );
			return [];
		}
		return data.posts;
	}
}
