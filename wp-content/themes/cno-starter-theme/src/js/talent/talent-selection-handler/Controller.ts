import LocalStorage from './model/LocalStorage';
import ListHandler from './view/ListHandler';
import ModalHandler from './view/ModalHandler';
import ToastHandler from '../../utils/ToastHandler';
import CardHandler from './view/CardHandler';

export default class Controller {
	TARGET_SELECTOR: string;
	BUTTON_ATTRIBUTE: string;

	/**
	 * The Toast handler for displaying messages
	 */
	toast: ToastHandler;
	db: LocalStorage;
	Modal: ModalHandler;
	ListHandler: ListHandler;

	/**
	 * The Badge that tracks the number of selected talents
	 */
	selectionTracker: HTMLSpanElement;

	get talentContainer(): HTMLElement | null {
		return document.querySelector( this.TARGET_SELECTOR );
	}

	constructor() {
		this.db = new LocalStorage();
		this.ListHandler = new ListHandler();
		this.Modal = new ModalHandler( this.onShowCallback.bind( this ) );
		this.toast = new ToastHandler();
		this.selectionTracker = document.getElementById(
			'selection-counter'
		) as HTMLSpanElement;
		this.TARGET_SELECTOR = '#talent';
		this.BUTTON_ATTRIBUTE = 'data-post-id';
		this.init();
	}

	private init() {
		this.Modal.modalEl.addEventListener( 'submit', async ( ev ) => {
			const target = ev.target as HTMLFormElement;
			if ( target.id !== 'save-list-form' ) {
				return;
			}
			ev.preventDefault();

			try {
				this.Modal.useLoadingSpinner( true, 'list' );
				const { success, message, link } =
					await this.db.handleSaveList( target );
				if ( success ) {
					this.toast.showToast(
						`${ message }\n<a href="${ link }">Preview the post.</a>`,
						'success'
					);
				}
			} catch ( error ) {
				this.toast.showToast(
					'There was an error saving your list. Please try again.',
					'error'
				);
				console.error( 'Error handling save list:', error );
			} finally {
				this.Modal.useLoadingSpinner( false, 'list' );
			}
		} );

		// if already has selection, show modal trigger and init button
		if ( this.db.getIds().size > 0 ) {
			this.Modal.showTrigger();
			this.selectionTracker.classList.remove( 'd-none' );
			this.selectionTracker.textContent = String( this.db.getIds().size );
			this.ListHandler.enableClearSelectionButton();
		}

		// add click listener for "Select Talent" buttons
		this.addSelectTalentListener( this.db );
		this.addLastUsedListener( this.db );
		if ( this.talentContainer ) {
			const observer = new MutationObserver( () => {
				this.addSelectTalentListener( this.db );
				this.addLastUsedListener( this.db );
			} );
			observer.observe( this.talentContainer, {
				childList: true,
				subtree: false,
			} );
		}
	}

	/**
	 * Callback function to handle modal show event
	 */
	private onShowCallback() {
		if ( this.ListHandler.clearSelectionButton ) {
			this.ListHandler.clearSelectionButton.removeEventListener(
				'click',
				this.handleClearSelection.bind( this )
			);
		}
		this.Modal.useLoadingSpinner( true, 'pdf' );
		// builds the list when the modal is shown
		this.ListHandler.buildSelectedList( this.db ).then( () => {
			this.Modal.useLoadingSpinner( false, 'pdf' );
		} );
		// wire event listener to remove elements on click; removes list if length === 0
		this.ListHandler.list!.addEventListener( 'click', ( ev ) => {
			if ( ev.target instanceof HTMLButtonElement ) {
				const postId = ev.target.dataset.postId;
				if ( postId ) {
					const liEl = this.ListHandler.list!.querySelector(
						`#talent-${ postId }`
					) as HTMLLIElement;
					if ( liEl ) {
						liEl.remove();
						this.db.removeId( Number( postId ) );
						if ( this.ListHandler.list!.children.length === 0 ) {
							this.ListHandler.clearSelectedList();
							this.ListHandler.hideClearConfirmationButtons();
							this.Modal.hide();
						}
						// side effect: decrement selection counter
						this.decrementSelectionCounter();
					}
				}
			}
		} );
		if ( this.ListHandler.clearSelectionButton ) {
			this.ListHandler.clearSelectionButton.addEventListener(
				'click',
				this.handleClearSelection.bind( this )
			);
		}
	}

	/**
	 * Add click listeners to all "Select Talent" buttons
	 */
	private addSelectTalentListener( db: LocalStorage ) {
		if ( ! this.talentContainer ) return;
		this.talentContainer.addEventListener( 'click', ( ev ) => {
			if ( ev.target instanceof HTMLButtonElement ) {
				const { postId, talentName, actionType } =
					this.getTalentAttributes( ev.target );
				if (
					! postId ||
					! talentName ||
					'select-talent' !== actionType
				)
					return;
				try {
					const idDidSave = db.saveId( postId );
					if ( idDidSave ) {
						this.incrementSelectionCounter();
						this.toast.showToast(
							`${ talentName } selected.`,
							'success'
						);
					}
				} catch ( error ) {
					this.toast.showToast( error.message, error.type );
				}
			}
		} );
	}

	/**
	 * Get the attributes from a button element
	 * @param button The button element
	 * @returns An object containing the post ID, talent name, and action type
	 */
	private getTalentAttributes( button: HTMLButtonElement ): {
		postId: string | null;
		talentName: string | null;
		actionType: 'select-talent' | 'last-used' | null;
	} {
		const card = button.closest( '.card' );
		if ( ! card ) {
			throw new Error( "Couldn't find the talent card!" );
		}
		let actionType: 'select-talent' | 'last-used' | null = null;
		if ( button.classList.contains( 'btn-select-talent' ) ) {
			actionType = 'select-talent';
		}
		if ( button.classList.contains( 'btn-last-used' ) ) {
			actionType = 'last-used';
		}
		return {
			postId: card.getAttribute( this.BUTTON_ATTRIBUTE ),
			talentName: card.getAttribute( 'data-talent-name' ),
			actionType,
		};
	}

	private addLastUsedListener( db: LocalStorage ) {
		if ( ! this.talentContainer ) return;
		this.talentContainer.addEventListener( 'click', async ( ev ) => {
			if ( ev.target instanceof HTMLButtonElement ) {
				const { postId, talentName, actionType } =
					this.getTalentAttributes( ev.target );
				if ( ! postId || ! talentName || 'last-used' !== actionType )
					return;
				const cardHandler = new CardHandler( postId );
				try {
					cardHandler.useIsLoading( true );
					const data = await db.setLastUsed( Number( postId ) );
					if ( data ) {
						cardHandler.updateLastUsedString();
						this.toast.showToast( data.message, 'success' );
					}
				} catch ( error ) {
					this.toast.showToast( error.message, error.type );
				} finally {
					cardHandler.useIsLoading( false );
				}
			}
		} );
	}

	/**
	 * Increments the selection counter
	 */
	private incrementSelectionCounter() {
		const currentCount = Number( this.selectionTracker.textContent ) || 0;
		if (
			0 === currentCount &&
			this.selectionTracker.classList.contains( 'd-none' )
		) {
			this.Modal.showTrigger();
			this.selectionTracker.classList.remove( 'd-none' );
			this.ListHandler.enableClearSelectionButton();
		}
		this.selectionTracker.textContent = String( currentCount + 1 );
	}

	/**
	 * Decrements the selection counter
	 */
	private decrementSelectionCounter() {
		const currentCount = Number( this.selectionTracker.textContent ) || 0;
		if ( currentCount > 0 ) {
			this.selectionTracker.textContent = String( currentCount - 1 );
		}
		if ( this.selectionTracker.textContent === '0' ) {
			this.selectionTracker.classList.add( 'd-none' );
			this.ListHandler.disableClearSelectionButton();
			this.Modal.hideTrigger();
		}
	}

	private handleClearSelection() {
		if ( this.ListHandler.isSecondClick ) {
			this.db.clearIds();
			this.selectionTracker.textContent = '0';
			this.selectionTracker.classList.add( 'd-none' );
			this.ListHandler.resetListState();
			this.Modal.hide();
			this.Modal.hideTrigger();
			this.toast.showToast( 'Selection cleared successfully.', 'info' );
		} else {
			this.ListHandler.showClearConfirmationButtons();
			const hideActionsEvents = {
				click: this.ListHandler.cancelButton,
				'hide.bs.modal': this.Modal.modalEl,
			};
			Object.entries( hideActionsEvents ).forEach(
				( [ event, element ] ) => {
					element?.addEventListener( event, () =>
						this.ListHandler.hideClearConfirmationButtons( false )
					);
				}
			);
		}
	}
}
