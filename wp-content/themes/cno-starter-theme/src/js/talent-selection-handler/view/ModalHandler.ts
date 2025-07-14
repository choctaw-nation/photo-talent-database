import Modal from 'bootstrap/js/dist/modal';
import LocalStorage, { PostData } from '../LocalStorage';

export default class ModalHandler {
	clearSelectionButton: HTMLButtonElement;

	modal;
	modalEl: HTMLDivElement;
	modalTrigger: HTMLButtonElement;

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

	constructor() {
		const clearSelectionButton =
			document.querySelector< HTMLButtonElement >(
				'button[type="reset"]'
			);
		if ( ! clearSelectionButton ) {
			throw new Error( 'Clear selection button not found' );
		}
		this.modalEl = document.getElementById(
			'create-email-modal'
		) as HTMLDivElement;
		this.clearSelectionButton = clearSelectionButton;
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
	 *
	 * @returns Object containing warning element, cancel button, and whether it's a second click
	 */
	getClickState(): {
		warning: HTMLParagraphElement;
		cancelButton: HTMLButtonElement;
		isSecondClick: boolean;
	} {
		const warning = document.getElementById(
			'clear-selection-warning'
		) as HTMLParagraphElement;

		const cancelButton = document.getElementById(
			'create-email-form-button-cancel'
		) as HTMLButtonElement;
		const isSecondClick = [ warning, cancelButton ].every(
			( el ) => ! el.classList.contains( 'd-none' )
		);

		return {
			warning,
			cancelButton,
			isSecondClick,
		};
	}

	/**
	 * Hides the warning and cancel buttons
	 */
	hideClearConfirmationButtons() {
		const { warning, cancelButton } = this.getClickState();
		warning.classList.add( 'd-none' );
		cancelButton.classList.add( 'd-none' );
	}

	showClearConfirmationButtons() {
		const { warning, cancelButton } = this.getClickState();
		warning.classList.remove( 'd-none' );
		cancelButton.classList.remove( 'd-none' );
	}

	resetModalState() {
		const { cancelButton } = this.getClickState();
		this.hideClearConfirmationButtons();
		this.clearSelectedList();
		cancelButton.removeEventListener(
			'click',
			this.hideClearConfirmationButtons.bind( this )
		);
		this.disableClearSelectionButton();
	}

	async buildSelectedList( db: LocalStorage ) {
		const ids = db.getIds();
		if ( this.list ) {
			if ( ids.size === this.list.children.length ) {
				return;
			}
		} else {
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
			const posts = await db.getSelectedData();
			posts.forEach( ( post ) => {
				const listItem = this.createTalentListItem( post );
				ul.appendChild( listItem );
			} );
		}
	}

	private createTalentListItem( data: PostData ): HTMLLIElement {
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
		return li;
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
