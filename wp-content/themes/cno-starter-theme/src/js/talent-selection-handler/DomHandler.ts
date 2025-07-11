import LocalStorage from './LocalStorage';

export default class DomHandler {
	TARGET_SELECTOR: string;
	BUTTON_ATTRIBUTE: string;

	selectionTracker: HTMLSpanElement;
	clearSelectionButton: HTMLButtonElement;

	constructor() {
		this.TARGET_SELECTOR = '#talent';
		this.BUTTON_ATTRIBUTE = 'data-post-id';
		this.initSelectionTracker();
	}

	init( db: LocalStorage ) {
		this.handleAddSelectionListener( db );
		this.handleClearSelectionListener();
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
		const clearSelectionButton =
			document.querySelector< HTMLButtonElement >(
				'button[type="reset"]'
			);
		if ( ! clearSelectionButton ) return;
		this.clearSelectionButton = clearSelectionButton;
		if (
			'' === this.selectionTracker.innerText ||
			this.selectionTracker.classList.contains( 'd-none' )
		) {
			this.clearSelectionButton.disabled = true;
		}
	}

	/**
	 * Add click listeners to all target buttons
	 */
	private handleAddSelectionListener( db: LocalStorage ) {
		console.log( db );
		const container = document.querySelector( this.TARGET_SELECTOR );
		if ( ! container ) return;
		container.addEventListener( 'click', ( ev ) => {
			if (
				ev.target instanceof HTMLButtonElement &&
				ev.target.hasAttribute( this.BUTTON_ATTRIBUTE )
			) {
				const postId = ev.target.getAttribute( this.BUTTON_ATTRIBUTE );
				if ( ! postId ) return;
				this.incrementSelectionCounter();
				try {
					db.saveId( postId );
					console.log( 'Talent IDs saved successfully:', postId );
				} catch ( error ) {
					console.trace( 'Failed to save talent ID:', error );
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
			this.selectionTracker.classList.remove( 'd-none' );
			this.clearSelectionButton.disabled = false;
		}
		this.selectionTracker.textContent = String( currentCount + 1 );
	}

	private handleClearSelectionListener() {
		this.clearSelectionButton.addEventListener( 'click', ( ev ) => {
			ev.preventDefault();
			const warning = document.createElement( 'p' );
			warning.className = 'm-0 text-danger fs-6 fw-bold';
			warning.textContent = 'Are you sure?';

			this.clearSelectionButton.parentNode?.insertBefore(
				warning,
				this.clearSelectionButton
			);

			const cancelButton = document.createElement( 'button' );
			cancelButton.type = 'button';
			cancelButton.textContent = 'Cancel';
			cancelButton.className = 'btn btn-secondary m-0';

			this.clearSelectionButton.parentNode?.insertBefore(
				cancelButton,
				this.clearSelectionButton.nextSibling
			);

			cancelButton.addEventListener( 'click', () => {
				warning.remove();
				cancelButton.remove();
			} );
			this.clearSelectionButton.textContent = 'Clear Selection';
		} );
	}
}
