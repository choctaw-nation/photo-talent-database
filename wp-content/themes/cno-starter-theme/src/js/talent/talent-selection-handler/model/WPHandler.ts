import dateAsYmd from '../../../utils/dateAsYmd';
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
		const today = dateAsYmd();
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

	async createTalentList(
		data: Record< string, any >
	): Promise< { success: boolean; message: string; link: string } > {
		try {
			const nonce = this.getNonce();
			const response = await fetch( `${ this.REST_ROUTE }/talent-list`, {
				method: 'POST',
				body: JSON.stringify( data ),
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': nonce,
				},
			} );
			if ( ! response.ok ) {
				const error = await response.json();
				throw new Error( error );
			}
			const {
				success,
				message,
				data: { post },
			} = await response.json();
			return { success, message, link: post.link };
		} catch ( error ) {
			console.error( 'Error creating talent list:', error );
			throw error;
		}
	}

	async deleteTalentList( id: number ): Promise< {
		success: boolean;
		message: string;
	} > {
		try {
			const nonce = this.getNonce();
			const response = await fetch(
				`${ this.REST_ROUTE }/talent-list/${ id }`,
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': nonce,
					},
				}
			);
			if ( ! response.ok ) {
				const error = await response.json();
				throw new Error( error );
			}
			const data = await response.json();
			return { success: data.success, message: data.message };
		} catch ( error ) {
			console.error( 'Error deleting talent list:', error );
			throw error;
		}
	}

	async removeTalentFromTalentList(
		postId: number,
		talentId: number
	): Promise< boolean > {
		try {
			const nonce = this.getNonce();
			const response = await fetch(
				`${ this.REST_ROUTE }/talent-list/${ postId }`,
				{
					method: 'PATCH',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': nonce,
					},
					body: JSON.stringify( { talentId } ),
				}
			);
			const { success, message, data } = await response.json();
			if ( success ) {
				return success;
			} else {
				throw new Error( message );
			}
		} catch ( err ) {
			console.error( 'Error removing talent from list:', err );
			throw err;
		}
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
