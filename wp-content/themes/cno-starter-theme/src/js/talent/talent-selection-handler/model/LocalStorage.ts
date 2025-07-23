import { APIResponse, PostData } from '../../../utils/types';
import createError from '../utils/createError';
import WPHandler from './WPHandler';

/**
 * Model. Interacts with localStorage to manage post IDs.
 */
export default class LocalStorage extends WPHandler {
	STORAGE_KEY: string;

	constructor() {
		super();
		this.STORAGE_KEY = 'selectedTalentIds';
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

	removeId( id: number | string ): boolean {
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
		const ids = this.getIds();
		if ( ! ids.has( id ) ) {
			const error = createError(
				'warning',
				`This person is not selected`
			);
			throw error;
		}
		ids.delete( id );
		this.saveIds( ids );
		return true;
	}

	async getSelectedData(): Promise< PostData[] | [] > {
		const ids = this.getIds();
		if ( ids.size === 0 ) {
			console.warn( 'No IDs found in localStorage' );
			return [];
		}
		const posts = await this.getPosts( ids );
		return posts;
	}
}
