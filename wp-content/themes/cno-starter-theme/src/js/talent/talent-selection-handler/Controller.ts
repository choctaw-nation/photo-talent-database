import LocalStorage from './LocalStorage';
import ListHandler from './view/ListHandler';
import ModalHandler from './view/ModalHandler';
import ToastHandler from '../../utils/ToastHandler';

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

	constructor() {
		this.db = new LocalStorage();
		this.Modal = new ModalHandler();
		this.ListHandler = new ListHandler();
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
					await this.Modal.handleSaveList( target, this.db.getIds() );
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
			}
		} );
		this.Modal.onShow( () => {
			// builds the list when the modal is shown
			this.ListHandler.buildSelectedList( this.db );

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
							if (
								this.ListHandler.list!.children.length === 0
							) {
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

			this.ListHandler.clearSelectionButton?.addEventListener(
				'click',
				() => {
					this.handleClearSelection();
				}
			);
		} );

		// if already has selection, show modal trigger and init button
		if ( this.db.getIds().size > 0 ) {
			this.Modal.showTrigger();
			this.selectionTracker.classList.remove( 'd-none' );
			this.selectionTracker.textContent = String( this.db.getIds().size );
			this.ListHandler.enableClearSelectionButton();
		}
		this.addSelectTalentListener( this.db );
		const container = document.querySelector( this.TARGET_SELECTOR );
		if ( container ) {
			const observer = new MutationObserver( () => {
				this.addSelectTalentListener( this.db );
			} );
			observer.observe( container, { childList: true, subtree: true } );
		}
	}

	/**
	 * Add click listeners to all "Select Talent" buttons
	 */
	private addSelectTalentListener( db: LocalStorage ) {
		const container = document.querySelector( this.TARGET_SELECTOR );
		if ( ! container ) return;
		container.addEventListener( 'click', ( ev ) => {
			if ( ev.target instanceof HTMLButtonElement ) {
				const postId = ev.target.getAttribute( this.BUTTON_ATTRIBUTE );
				const talentName = ev.target.getAttribute( 'data-talent-name' );
				if ( ! postId || ! talentName ) return;
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
