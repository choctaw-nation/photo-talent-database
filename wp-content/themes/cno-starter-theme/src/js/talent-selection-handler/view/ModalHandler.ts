import Modal from 'bootstrap/js/dist/modal';
import LocalStorage, { PostData } from '../LocalStorage';

export default class ModalHandler {
	modal;
	modalEl: HTMLDivElement;
	modalTrigger: HTMLButtonElement;
	private isLoading: boolean = false;

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

	constructor() {
		this.modalEl = document.getElementById(
			'create-email-modal'
		) as HTMLDivElement;
		this.initModal();
		this.modalTrigger = document.getElementById(
			'create-email-modal-trigger'
		) as HTMLButtonElement;
	}

	initModal() {
		this.modal = Modal.getOrCreateInstance( this.modalEl );
	}

	/**
	 * Hides the modal trigger button
	 */
	hideModalTrigger() {
		if ( this.modalTrigger ) {
			this.modalTrigger.classList.add( 'd-none' );
		}
	}

	/**
	 * Shows the modal trigger button
	 */
	showModalTrigger() {
		if ( this.modalTrigger ) {
			this.modalTrigger.classList.remove( 'd-none' );
		}
	}

	/**
	 * Enables the clear selection button
	 */
	enableClearSelectionButton() {
		if ( this.clearSelectionButton ) {
			this.clearSelectionButton.disabled = false;
		}
	}

	/**
	 * Disables the clear selection button
	 */
	disableClearSelectionButton() {
		if ( this.clearSelectionButton ) {
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
	 * Clears the list, hides the buttons
	 */
	resetModalState() {
		this.hideClearConfirmationButtons();
		this.clearSelectedList();
		this.cancelButton?.removeEventListener(
			'click',
			this.hideClearConfirmationButtons.bind( this )
		);
		this.disableClearSelectionButton();
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
				console.log( 'updating existing list' );
				const existingPosts =
					this.list.querySelectorAll< HTMLLIElement >( 'li' );
				const existingPostIds = [ ...existingPosts ].map( ( li ) =>
					Number( li.id )
				);
				this.createPlaceholderListItems(
					Array.from( db.getIds().values() )
				);
				this.renderListItems( db, this.list, existingPostIds );
			}
		} else {
			console.log( 'initial render' );
			const ul = this.appendUl();
			this.appendClearActions();
			this.enableClearSelectionButton();
			this.createPlaceholderListItems(
				Array.from( db.getIds().values() ),
				ul
			);
			this.renderListItems( db, ul );
		}
	}

	private appendClearActions() {
		const actionsContainer = document.getElementById(
			'actions-container'
		) as HTMLDivElement;
		actionsContainer.insertAdjacentHTML(
			'beforeend',
			`<div class="d-flex gap-2 w-auto" id="actions-buttons-container"><p class="m-0 text-danger fw-bold fs-6 d-none" id="clear-selection-warning">Are you sure?</p>
				<button type="reset" form="create-email-form" class="btn btn-link link-offset-1 link-offset-2-hover text-danger p-0 btn-sm fw-normal flex-grow-1">Clear Selection</button>
				<button class="flex-grow-1 btn btn-link link-offset-1 link-offset-2-hover p-0 text-secondary d-none btn-sm fw-normal" id="create-email-form-button-cancel">Cancel</button></div>`
		);
	}

	/**
	 * Adds a new UL element to the modal's list container
	 */
	private appendUl(): HTMLUListElement {
		const ul = document.createElement( 'ul' );
		ul.classList.add(
			'list-unstyled',
			'mb-0',
			'd-flex',
			'flex-column',
			'align-items-stretch',
			'row-gap-3'
		);
		this.listContainer.appendChild( ul );
		return ul;
	}

	private async renderListItems(
		db: LocalStorage,
		ul: HTMLUListElement,
		existingPostIds?: number[]
	) {
		this.isLoading = true;
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
		this.isLoading = false;
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
				'row',
				'row-cols-2',
				'align-items-center',
				'gx-0',
				'gap-2'
			);
			li.innerHTML = `
			<div class="col-2">
				<figure class="ratio ratio-1x1 mb-0 rounded-circle overflow-hidden">
					<svg aria-label="Placeholder" class="" height="180" preserveAspectRatio="xMidYMid slice" role="img" width="100%" xmlns="http://www.w3.org/2000/svg"><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"></rect></svg>
				</figure>
			</div>
			<div>
				<h3 class="mb-0 fs-6 d-flex flex-wrap gap-2 placeholder-glow"><span class="placeholder col-6"></span></h3>
			</div>
		`;
			li.id = `talent-${ id }`;
			list.appendChild( li );
		} );
	}

	private createTalentListItem( data: PostData ): string {
		return `
			<div class="col-2">
				<figure class="ratio ratio-1x1 mb-0 rounded-circle overflow-hidden">
				${ data.thumbnail }
				</figure>
			</div>
			<div>
				<h3 class="mb-0 fs-6 d-flex flex-wrap gap-2">${ data.title }${
					data.isChoctaw
						? `<span class="badge text-bg-primary fw-normal">
					Choctaw
				</span>`
						: ''
				}</h3>
				
				${
					data.lastUsed
						? `<span class="badge bg-secondary ms-2">Last Used: ${ data.lastUsed }</span>`
						: ''
				}
			</div>
		`;
	}

	/**
	 * Removes the selected list UL element from the modal
	 */
	protected clearSelectedList() {
		if ( this.list ) {
			this.list.remove();
		}
	}
}
