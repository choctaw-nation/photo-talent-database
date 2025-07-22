import { ImageDetails, PostData } from '../../../utils/types';
import LocalStorage from '../model/LocalStorage';

export default class ListHandler {
	/**
	 * Gets the clear selection button
	 */
	get clearSelectionButton(): HTMLButtonElement | null {
		return document.querySelector< HTMLButtonElement >(
			'button[type="reset"]'
		);
	}

	get warningElement(): HTMLParagraphElement | null {
		return document.getElementById(
			'clear-selection-warning'
		) as HTMLParagraphElement;
	}

	get cancelButton(): HTMLButtonElement | null {
		return document.getElementById(
			'create-email-form-button-cancel'
		) as HTMLButtonElement;
	}

	get isSecondClick(): boolean {
		return [ this.warningElement, this.cancelButton ].every(
			( el ) => ! el || ! el.classList.contains( 'd-none' )
		);
	}

	get clearActionsContainer(): HTMLDivElement {
		return document.getElementById(
			'actions-buttons-container'
		) as HTMLDivElement;
	}

	get listContainer(): HTMLDivElement {
		return document.getElementById(
			'selected-talent-list'
		) as HTMLDivElement;
	}
	get list(): HTMLUListElement | null {
		const listContainer = this.listContainer;
		return listContainer
			? listContainer.querySelector< HTMLUListElement >( 'ul' )
			: null;
	}
	/**
	 * Generates the list of selected talents in the modal. Called on modal open.
	 * @param db LocalStorage instance to fetch selected data
	 */
	async buildSelectedList( db: LocalStorage ) {
		const ids = db.getIds();
		if ( this.list ) {
			if ( ids.size === this.list.children.length ) {
				return;
			} else {
				const existingPosts =
					this.list.querySelectorAll< HTMLLIElement >( 'li' );
				const existingPostIds = [ ...existingPosts ].map( ( li ) =>
					Number( li.id )
				);
				this.createPlaceholderListItems(
					Array.from( db.getIds().values() )
				);
				await this.renderListItems( db, this.list, existingPostIds );
			}
		} else {
			const ul = this.appendUl();
			this.appendClearActions();
			this.enableClearSelectionButton();
			this.createPlaceholderListItems(
				Array.from( db.getIds().values() ),
				ul
			);
			await this.renderListItems( db, ul );
		}
	}

	/**
	 * Disables the clear selection button
	 */
	disableClearSelectionButton() {
		if ( this.list && this.clearSelectionButton ) {
			this.clearSelectionButton.disabled = true;
		}
	}

	/**
	 * Hides the warning and cancel buttons
	 */
	hideClearConfirmationButtons( shouldDestroy: boolean = true ) {
		if ( shouldDestroy ) {
			this.clearActionsContainer?.remove();
		} else {
			this.warningElement?.classList.add( 'd-none' );
			this.cancelButton?.classList.add( 'd-none' );
			if (
				this.clearActionsContainer?.classList.contains( 'flex-grow-1' )
			) {
				this.clearActionsContainer.classList.remove( 'flex-grow-1' );
			}
		}
	}

	showClearConfirmationButtons() {
		this.warningElement?.classList.remove( 'd-none' );
		this.cancelButton?.classList.remove( 'd-none' );
		if (
			! this.clearActionsContainer.classList.contains( 'flex-grow-1' )
		) {
			this.clearActionsContainer.classList.add( 'flex-grow-1' );
		}
	}
	/**
	 * Enables the clear selection button
	 */
	enableClearSelectionButton() {
		if ( this.list && this.clearSelectionButton ) {
			this.clearSelectionButton.disabled = false;
		}
	}

	/**
	 * Adds a new UL element to the modal's list container
	 */
	private appendUl(): HTMLUListElement {
		const ul = document.createElement( 'ul' );
		ul.classList.add( 'align-items-stretch', 'list-group' );
		this.listContainer.appendChild( ul );
		return ul;
	}

	private appendClearActions() {
		const actionsContainer = document.getElementById(
			'actions-container'
		) as HTMLDivElement;
		actionsContainer.insertAdjacentHTML(
			'beforeend',
			`<div class="d-flex gap-2 w-auto" id="actions-buttons-container"><p class="m-0 text-danger fw-bold fs-6 d-none" id="clear-selection-warning">Are you sure?</p>
				<button type="reset" class="btn btn-link link-offset-1 link-offset-2-hover text-danger p-0 btn-sm fw-normal flex-grow-1">Clear All</button>
				<button class="flex-grow-1 btn btn-link link-offset-1 link-offset-2-hover p-0 text-secondary d-none btn-sm fw-normal" id="create-email-form-button-cancel">Cancel</button></div>`
		);
	}

	private async renderListItems(
		db: LocalStorage,
		ul: HTMLUListElement,
		existingPostIds?: number[]
	) {
		const posts = await db.getSelectedData();
		const ulEl = ul || this.list!;
		const postsToRender = existingPostIds
			? posts.filter( ( post ) => ! existingPostIds.includes( post.id ) )
			: posts;
		postsToRender.forEach( ( post ) => {
			const listItemMarkup = this.createTalentListItem( post );
			const liEl = ulEl.querySelector(
				`#talent-${ post.id }`
			) as HTMLLIElement;
			if ( liEl ) {
				liEl.innerHTML = listItemMarkup;
			}
		} );
	}

	/**
	 * Creates a placeholder list item for the modal.
	 */
	private createPlaceholderListItems( ids: number[], ul?: HTMLUListElement ) {
		const list = ul || this.list!;
		const existingPosts = list.querySelectorAll< HTMLLIElement >( 'li' );
		const existingPostIds = [ ...existingPosts ].map( ( li ) => li.id );
		ids.forEach( ( id ) => {
			if ( existingPostIds.includes( `talent-${ id }` ) ) {
				return;
			}
			const li = document.createElement( 'li' );
			li.classList.add(
				'd-flex',
				'list-group-item',
				'justify-content-between',
				'align-items-center'
			);
			li.innerHTML = `
			<div class="flex-grow-1 row gx-0 gap-2 flex-nowrap align-items-center">
				<div class="col-2 d-sm-none d-md-block">
					<figure class="ratio ratio-1x1 mb-0 rounded-circle overflow-hidden">
						<svg aria-label="Placeholder" class="" height="180" preserveAspectRatio="xMidYMid slice" role="img" width="100%" xmlns="http://www.w3.org/2000/svg"><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"></rect></svg>
					</figure>
				</div>
				<div>
					<h3 class="mb-0 fs-5 d-flex flex-wrap gap-2 placeholder-glow">
						<span class="placeholder col-6"></span>
					</h3>
				</div>
			</div>
		`;
			li.id = `talent-${ id }`;
			list.appendChild( li );
		} );
	}

	private createTalentListItem( data: PostData ): string {
		const { images, title, isChoctaw, id } = data;
		const lastUsed = data.lastUsed ? this.formatDate( data.lastUsed ) : '';

		return `
			<div class="flex-grow-1 row gx-0 gap-2 flex-nowrap align-items-center">
				<div class="col-2">
					<figure class="ratio ratio-1x1 mb-0 rounded-circle overflow-hidden">
					${ this.generateImage( images.front ) }
					</figure>
				</div>
				<div class="col d-flex flex-column align-items-start gap-2">
					<h3 class="mb-0 fs-5">${ title }</h3>
					${
						isChoctaw
							? `<span class="badge text-bg-primary fw-normal w-auto">Choctaw</span>`
							: ''
					}
					${
						lastUsed
							? `<span class="badge bg-secondary fw-normal fs-root w-auto">Last Used: ${ lastUsed }</span>`
							: ''
					}
				</div>
			</div>
			<button class="btn-close" data-post-id="${ id }"><span class="visually-hidden">Close</span></button>
		`;
	}

	private generateImage( image: ImageDetails ): string {
		const { url, alt, sizes, srcset } = image;
		return `<img src="${ url }" alt="${ alt }" sizes="${ sizes }" srcset="${ srcset }" class="w-100 h-100 object-fit-cover" />`;
	}

	/**
	 * Returns a relative time string based on a PHP Ymd date string
	 *
	 * @param date PHP Ymd string (e.g. '20250715')
	 */
	protected formatDate( date: string ): string {
		// Parse PHP Ymd string
		if ( ! date || date.length !== 8 ) return '';
		const year = parseInt( date.slice( 0, 4 ), 10 );
		const month = parseInt( date.slice( 4, 6 ), 10 ) - 1; // JS months are 0-based
		const day = parseInt( date.slice( 6, 8 ), 10 );
		const inputDate = new Date( year, month, day );
		const now = new Date();
		// Zero out time for both dates
		inputDate.setHours( 0, 0, 0, 0 );
		now.setHours( 0, 0, 0, 0 );
		const diffMs = now.getTime() - inputDate.getTime();
		const diffDays = Math.floor( diffMs / ( 1000 * 60 * 60 * 24 ) );
		if ( diffDays === 0 ) return 'today';
		if ( diffDays === 1 ) return 'yesterday';
		if ( diffDays < 7 ) return `${ diffDays } days ago`;
		if ( diffDays < 30 ) {
			const weeks = Math.floor( diffDays / 7 );
			return weeks === 1 ? '1 week ago' : `${ weeks } weeks ago`;
		}
		if ( diffDays < 365 ) {
			const months = Math.floor( diffDays / 30 );
			return months === 1 ? '1 month ago' : `${ months } months ago`;
		}
		const years = Math.floor( diffDays / 365 );
		return years === 1
			? '1 year ago'
			: inputDate.toLocaleDateString( 'en-US', {
					year: 'numeric',
					month: 'long',
					day: 'numeric',
			  } );
	}

	/**
	 * Removes the selected list UL element from the modal
	 */
	clearSelectedList() {
		if ( this.list ) {
			this.list.remove();
		}
	}

	resetListState() {
		this.hideClearConfirmationButtons();
		this.clearSelectedList();
		this.cancelButton?.removeEventListener(
			'click',
			this.hideClearConfirmationButtons.bind( this )
		);
		this.disableClearSelectionButton();
	}
}
