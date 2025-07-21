import { APIResponse, PostData } from '../../../utils/types';

export default class WPHandler {
	REST_ROUTE: string;

	constructor() {
		this.REST_ROUTE = '/wp-json/cno/v1';
	}

	async getPosts( ids: Set< number > ): Promise< PostData[] | [] > {
		try {
			const nonce = this.getNonce();
			const response = await fetch(
				`${ this.REST_ROUTE }/talent?talent-ids=${ this.idsList(
					ids
				) }&images=front&fields=isChoctaw,lastUsed`,
				{
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': nonce,
					},
				}
			);
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
		} catch ( err ) {
			console.error( 'Error fetching posts:', err );
			return [];
		}
	}

	async setLastUsed( id: number ) {
		const today = this.todayAsYmd();
		try {
			const nonce = this.getNonce();
			const response = await fetch(
				`${ this.REST_ROUTE }/talent/${ id }
				`,
				{
					method: 'PATCH',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': nonce,
					},
					body: JSON.stringify( {
						lastUsed: today,
					} ),
				}
			);
			if ( ! response.ok ) {
				throw new Error(
					`Failed to set last used date: ${ response.statusText }`
				);
			}
			const data = await response.json();
			if ( ! data.success ) {
				throw new Error(
					`API response was not successful: ${ JSON.stringify(
						data
					) }`
				);
			}
			return data;
		} catch ( err ) {
			throw err;
		}
	}

	private todayAsYmd(): string {
		const today = new Date();
		const year = today.getFullYear();
		const month = String( today.getMonth() + 1 ).padStart( 2, '0' );
		const day = String( today.getDate() ).padStart( 2, '0' );
		return `${ year }${ month }${ day }`;
	}

	private idsList( ids: Set< number > ): string {
		return Array.from( ids ).join( ',' );
	}

	private getNonce(): string {
		const nonce = ( window as any ).cnoApi?.nonce ?? null;
		if ( ! nonce ) {
			throw new Error( 'API nonce is not available' );
		}
		return nonce;
	}
}
