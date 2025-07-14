import Modal from 'bootstrap/js/dist/modal';

export default class ModalHandler {
	clearSelectionButton: HTMLButtonElement;

	private modal;
	modalEl: HTMLDivElement;
	modalTrigger: HTMLButtonElement;

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
		this.modal = Modal.getOrCreateInstance( this.modalEl );
		this.modalTrigger = document.getElementById(
			'create-email-modal-trigger'
		) as HTMLButtonElement;
		this.clearSelectionButton = clearSelectionButton;
	}

	hideModalTrigger() {
		if ( this.modalTrigger ) {
			this.modalTrigger.classList.add( 'd-none' );
		}
	}

	showModalTrigger() {
		if ( this.modalTrigger ) {
			this.modalTrigger.classList.remove( 'd-none' );
		}
	}

	enableClearSelectionButton() {
		if ( this.clearSelectionButton ) {
			this.clearSelectionButton.disabled = false;
		}
	}

	disableClearSelectionButton() {
		if ( this.clearSelectionButton ) {
			this.clearSelectionButton.disabled = true;
		}
	}

	handleClearSelectionListener( callback: () => void ) {
		this.clearSelectionButton.addEventListener( 'click', ( ev ) => {
			const { warning, cancelButton, isSecondClick } =
				this.getClickState();
			if ( isSecondClick ) {
				callback();
				this.resetModalState( warning, cancelButton );
				this.hideModal();
				this.hideModalTrigger();
				console.log( 'Selection cleared successfully.' );
			} else {
				ev.preventDefault();
				warning.classList.remove( 'd-none' );
				cancelButton.classList.remove( 'd-none' );
				cancelButton.addEventListener(
					'click',
					this.hideCancelButtonClasses.bind( this )
				);
				this.clearSelectionButton.textContent = 'Clear Selection';
			}
		} );
	}

	/**
	 *
	 * @returns Object containing warning element, cancel button, and whether it's a second click
	 */
	private getClickState(): {
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

	private hideCancelButtonClasses() {
		const { warning, cancelButton } = this.getClickState();
		warning.classList.add( 'd-none' );
		cancelButton.classList.add( 'd-none' );
	}

	private hideModal() {
		const backdrop = document.querySelector( '.modal-backdrop' );
		this.modal.hide();
		if ( backdrop ) {
			backdrop.remove();
		}
		if ( window && window.focus ) {
			window.focus();
		}
	}

	private resetModalState(
		warning: HTMLParagraphElement,
		cancelButton: HTMLButtonElement
	) {
		warning.classList.remove( 'd-none' );
		cancelButton.classList.remove( 'd-none' );
		cancelButton.removeEventListener(
			'click',
			this.hideCancelButtonClasses.bind( this )
		);
		this.disableClearSelectionButton();
	}
}
