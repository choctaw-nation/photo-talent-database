import LocalStorage from '../LocalStorage';
import ModalHandler from './ModalHandler';

export default class DomHandler extends ModalHandler {
	TARGET_SELECTOR: string;
	BUTTON_ATTRIBUTE: string;
	selectionTracker: HTMLSpanElement;

	constructor() {
		super();
		this.TARGET_SELECTOR = '#talent';
		this.BUTTON_ATTRIBUTE = 'data-post-id';
		this.initSelectionTracker();
	}

	init( db: LocalStorage ) {
		this.modalEl.addEventListener( 'show.bs.modal', () => {
			this.buildSelectedList( db );
		} );
		if ( db.getIds().size > 0 ) {
			this.showModalTrigger();
			this.selectionTracker.classList.remove( 'd-none' );
			this.selectionTracker.textContent = String( db.getIds().size );
			this.enableClearSelectionButton();
		}
		this.handleAddSelectionListener( db );
		this.clearSelectionButton.addEventListener( 'click', ( ev ) => {
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
			if (
				ev.target instanceof HTMLButtonElement &&
				ev.target.hasAttribute( this.BUTTON_ATTRIBUTE )
			) {
				const postId = ev.target.getAttribute( this.BUTTON_ATTRIBUTE );
				if ( ! postId ) return;
				try {
					const idDidSave = db.saveId( postId );
					if ( idDidSave ) {
						this.incrementSelectionCounter();
					}
				} catch ( error ) {
					console.error( 'Failed to save ID:', error );
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

	private handleClearSelection( ev: MouseEvent, db: LocalStorage ) {
		const { cancelButton, isSecondClick } = this.getClickState();
		if ( isSecondClick ) {
			db.clearIds();
			this.selectionTracker.textContent = '0';
			this.selectionTracker.classList.add( 'd-none' );
			this.resetModalState();
			this.modal.hide();
			this.hideModalTrigger();
			console.log( 'Selection cleared successfully.' );
		} else {
			ev.preventDefault();
			this.showClearConfirmationButtons();
			cancelButton.addEventListener(
				'click',
				this.hideClearConfirmationButtons.bind( this )
			);
			this.clearSelectionButton.textContent = 'Clear Selection';
		}
	}
}
