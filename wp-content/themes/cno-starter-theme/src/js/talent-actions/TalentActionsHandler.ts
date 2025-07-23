import ToastHandler from '../utils/ToastHandler';

type ActionType = 'approve' | 'reject';

export default class TalentActionsHandler {
	actionsContainers: NodeListOf< HTMLElement >;
	talentId: number;
	toaster: ToastHandler;

	constructor() {
		this.actionsContainers = document.querySelectorAll(
			'.post-actions-container'
		);
		if ( this.actionsContainers.length === 0 ) {
			throw new Error( 'No post actions containers found.' );
		}
		this.talentId = this.getPostIdFromBody();
		this.toaster = new ToastHandler();
	}

	init() {
		this.actionsContainers.forEach( ( container ) => {
			container.addEventListener( 'click', async ( event ) => {
				const target = event.target as HTMLElement;
				const action = target.getAttribute(
					'data-action'
				) as ActionType;
				if ( ! action ) {
					return;
				}
				try {
					this.useIsLoading( true, container as HTMLDivElement );
					const { message, success } =
						await this.updatePost( action );
					const actionCallback = {
						approve: this.approvalCallback.bind( this ),
						reject: this.rejectionCallback.bind( this ),
					};
					this.toaster.showToast(
						message,
						success ? 'success' : 'error',
						actionCallback[ action ]
					);
				} catch ( error ) {
					console.error( 'Error updating post:', error );
					this.toaster.showToast(
						'An error occurred while processing your request.',
						'error'
					);
				} finally {
					this.useIsLoading( false, container as HTMLDivElement );
				}
			} );
		} );
	}

	private getPostIdFromBody(): number {
		const idClass = [ ...document.body.classList ].filter( ( className ) =>
			className.startsWith( 'postid-' )
		)[ 0 ];
		if ( ! idClass ) {
			throw new Error( 'No post ID found in body class.' );
		}
		const idString = idClass.replace( 'postid-', '' );
		const postId = parseInt( idString, 10 );
		if ( isNaN( postId ) ) {
			throw new Error( 'Invalid post ID found in body class.' );
		}
		return postId;
	}

	private useIsLoading( isLoading: boolean, container: HTMLDivElement ) {
		const buttons =
			container.querySelectorAll< HTMLButtonElement >( 'button.btn' );
		if ( isLoading ) {
			buttons.forEach( ( button ) => {
				button.disabled = true;
			} );
			container.insertAdjacentHTML(
				'beforeend',
				`<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`
			);
		} else {
			buttons.forEach( ( button ) => {
				button.disabled = false;
			} );
			container.querySelector( '.spinner-border' )?.remove();
		}
	}

	private async updatePost(
		action: string
	): Promise< { message: string; success: boolean; data?: any } > {
		const method = action === 'approve' ? 'PATCH' : 'DELETE';
		try {
			const response = await fetch(
				`/wp-json/cno/v1/talent/${ this.talentId }`,
				{
					method,
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': window.cnoApi.nonce,
					},
				}
			);
			const data = await response.json();
			return data;
		} catch ( error ) {
			console.error( 'Error in updatePost:', error );
			throw error;
		}
	}

	private approvalCallback() {
		this.actionsContainers.forEach( ( container ) => {
			container.remove();
			window.location.href = '/talent';
		} );
	}

	private rejectionCallback() {
		window.location.href = '/talent';
	}
}
