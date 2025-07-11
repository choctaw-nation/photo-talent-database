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

	saveId( id: number | string ) {
		console.log( id );
		if ( typeof id === 'string' ) {
			id = Number( id );
		}
		if ( isNaN( id ) ) {
			throw new Error( 'Invalid ID: must be a number' );
		}
		if ( id <= 0 ) {
			throw new Error( 'Invalid ID: must be greater than zero' );
		}
		const ids = this.getIds();
		ids.add( id );
		try {
			this.saveIds( ids );
		} catch ( error ) {
			console.error( 'Failed to save talent:', error );
		}
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
}
