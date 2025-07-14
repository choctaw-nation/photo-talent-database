import LocalStorage from '../LocalStorage';
import ModalHandler from './ModalHandler';
import ToastHandler from './ToastHandler';

export default class DomHandler extends ModalHandler {
	TARGET_SELECTOR: string;
	BUTTON_ATTRIBUTE: string;

	/**
	 * The Toast handler for displaying messages
	 */
	toast: ToastHandler;

	/**
	 * The Badge that tracks the number of selected talents
	 */
	selectionTracker: HTMLSpanElement;

	constructor() {
		super();
		this.toast = new ToastHandler( this.modalEl );
		this.TARGET_SELECTOR = '#talent';
		this.BUTTON_ATTRIBUTE = 'data-post-id';
		this.initSelectionTracker();
	}

	init( db: LocalStorage ) {
		this.modalEl.addEventListener( 'show.bs.modal', () => {
			this.buildSelectedList( db );
			this.list!.addEventListener( 'click', ( ev ) => {
				if ( ev.target instanceof HTMLButtonElement ) {
					const postId = ev.target.dataset.postId;
					if ( postId ) {
						const liEl = this.list!.querySelector(
							`#talent-${ postId }`
						) as HTMLLIElement;
						if ( liEl ) {
							liEl.remove();
							db.removeId( Number( postId ) );
							if ( this.list!.children.length === 0 ) {
								this.clearSelectedList();
								this.hideClearConfirmationButtons();
							}
							this.decrementSelectionCounter();
						}
					}
				}
			} );
		} );
		if ( db.getIds().size > 0 ) {
			this.showModalTrigger();
			this.selectionTracker.classList.remove( 'd-none' );
			this.selectionTracker.textContent = String( db.getIds().size );
			this.enableClearSelectionButton();
		}
		this.handleAddSelectionListener( db );
		const form = document.getElementById(
			'create-email-form'
		) as HTMLFormElement;
		form.addEventListener( 'reset', ( ev ) => {
			this.handleClearSelection( ev, db );
		} );

		const container = document.querySelector( this.TARGET_SELECTOR );
		if ( container ) {
			const observer = new MutationObserver( () => {
				this.handleAddSelectionListener( db );
			} );
			observer.observe( container, { childList: true, subtree: true } );
		}
	}

	private initSelectionTracker() {
		this.selectionTracker = document.getElementById(
			'selection-counter'
		) as HTMLSpanElement;

		if (
			'' === this.selectionTracker.innerText ||
			this.selectionTracker.classList.contains( 'd-none' )
		) {
			this.disableClearSelectionButton();
		}
	}

	/**
	 * Add click listeners to all target buttons
	 */
	private handleAddSelectionListener( db: LocalStorage ) {
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

	private incrementSelectionCounter() {
		const currentCount = Number( this.selectionTracker.textContent ) || 0;
		if (
			0 === currentCount &&
			this.selectionTracker.classList.contains( 'd-none' )
		) {
			this.showModalTrigger();
			this.selectionTracker.classList.remove( 'd-none' );
			this.enableClearSelectionButton();
		}
		this.selectionTracker.textContent = String( currentCount + 1 );
	}

	private decrementSelectionCounter() {
		const currentCount = Number( this.selectionTracker.textContent ) || 0;
		if ( currentCount > 0 ) {
			this.selectionTracker.textContent = String( currentCount - 1 );
		}
		if ( this.selectionTracker.textContent === '0' ) {
			this.selectionTracker.classList.add( 'd-none' );
			this.disableClearSelectionButton();
			this.hideModalTrigger();
		}
	}

	handleClearSelection( ev: Event, db: LocalStorage ) {
		if ( this.isSecondClick ) {
			db.clearIds();
			this.selectionTracker.textContent = '0';
			this.selectionTracker.classList.add( 'd-none' );
			this.resetModalState();
			this.modal.hide();
			this.hideModalTrigger();
			this.toast.showToast( 'Selection cleared successfully.', 'info' );
		} else {
			ev.preventDefault();
			this.showClearConfirmationButtons();
			const hideActionsEvents = {
				click: this.cancelButton,
				'hide.bs.modal': this.modalEl,
			};
			Object.entries( hideActionsEvents ).forEach(
				( [ event, element ] ) => {
					element?.addEventListener( event, () =>
						this.hideClearConfirmationButtons( false )
					);
				}
			);
		}
	}
}
