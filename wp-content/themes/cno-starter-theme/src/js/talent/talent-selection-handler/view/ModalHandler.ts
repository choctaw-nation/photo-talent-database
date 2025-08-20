import Modal from 'bootstrap/js/dist/modal';
import Tab from 'bootstrap/js/dist/tab';
import { SaveListFormData } from '../../../utils/types';
import dateAsYmd from '../../../utils/dateAsYmd';
import { insertSpinner, removeSpinner } from '../../../utils/spinner';

export default class ModalHandler {
	modal: Modal;
	modalEl: HTMLDivElement;
	modalTrigger: HTMLButtonElement;

	get generatePdfButton(): HTMLButtonElement | null {
		return document.getElementById(
			'generate-pdf-btn'
		) as HTMLButtonElement | null;
	}
	get saveListButton(): HTMLButtonElement | null {
		return document.querySelector< HTMLButtonElement >(
			'input[type="submit"][value="Save List"]'
		);
	}

	get listNameInput(): HTMLInputElement | null {
		return this.modalEl.querySelector< HTMLInputElement >( '#listName' );
	}

	get modalTitle(): string {
		return (
			this.modalEl.querySelector< HTMLElement >( '.modal-title' )
				?.textContent || ''
		);
	}

	set modalTitle( title: string ) {
		this.modalEl.querySelector< HTMLElement >(
			'.modal-title'
		)!.textContent = title;
	}

	constructor( onShow: () => void ) {
		this.modalEl = document.getElementById(
			'create-pdf-modal'
		) as HTMLDivElement;
		this.modalTrigger = document.getElementById(
			'create-pdf-modal-trigger'
		) as HTMLButtonElement;
		this.modal = Modal.getOrCreateInstance( this.modalEl );
		this.init( onShow );
	}

	private init( onShow: () => void ) {
		this.modalTrigger.addEventListener( 'click', () => {
			this.modal.show();
		} );
		this.modalEl.addEventListener( 'show.bs.modal', () => {
			this.initTabs();
			onShow();
		} );
	}

	/**
	 * Hides the modal trigger button
	 */
	hideTrigger() {
		if ( this.modalTrigger ) {
			this.modalTrigger.classList.add( 'd-none' );
		}
	}

	/**
	 * Shows the modal trigger button
	 */
	showTrigger() {
		if ( this.modalTrigger ) {
			this.modalTrigger.classList.remove( 'd-none' );
		}
	}

	hide() {
		this.modal.hide();
	}

	private initTabs() {
		const triggers =
			this.modalEl.querySelectorAll< HTMLButtonElement >(
				'button[role="tab"]'
			);
		if ( ! triggers.length ) {
			return;
		}

		triggers.forEach( ( trigger, index ) => {
			const tab = Tab.getOrCreateInstance( trigger );
			if ( 0 === index ) {
				tab.show();
			}
			trigger.addEventListener( 'click', ( ev ) => {
				if ( 'save-list-tab' === trigger.id ) {
					this.modalTitle = 'Save Selected Talent List';
					this.showOtherTrigger( trigger, triggers );
					this.disableGeneratePdfButton();
					trigger.disabled = true;
					if ( this.listNameInput ) {
						this.listNameInput.addEventListener(
							'change',
							this.renderListNamePreview.bind( this )
						);
					}
				}
				if ( 'back-to-form' === trigger.id ) {
					this.modalTitle = 'Generate PDF';
					this.showOtherTrigger( trigger, triggers );
					this.enableGeneratePdfButton();
					if ( this.listNameInput ) {
						this.listNameInput.removeEventListener(
							'change',
							this.renderListNamePreview.bind( this )
						);
					}
				}
				tab.show();
			} );
		} );
	}

	private showOtherTrigger(
		trigger: HTMLButtonElement,
		triggers: NodeListOf< HTMLButtonElement >
	) {
		const otherTrigger = Array.from( triggers ).find(
			( btn ) => trigger.id !== btn.id
		);
		if ( otherTrigger ) {
			otherTrigger.classList.remove( 'd-none' );
			otherTrigger.disabled = false;
		}
		trigger.disabled = true;
		trigger.classList.add( 'd-none' );
	}

	/**
	 * Renders the preview of the list name.
	 */
	private renderListNamePreview() {
		const listNamePreview =
			this.modalEl.querySelector< HTMLElement >( '#listNamePreview' );
		const listNameHelper =
			this.modalEl.querySelector< HTMLElement >( '#listNameHelper' );
		const today = new Date();
		const ymd = dateAsYmd( today );
		if ( this.listNameInput && listNamePreview ) {
			if ( listNameHelper?.classList.contains( 'd-none' ) ) {
				listNameHelper?.classList.remove( 'd-none' );
			}
			listNamePreview.textContent = `“${ ymd } — ${ this.listNameInput.value }”`;
		}
	}

	disableSaveListButton() {
		if ( this.saveListButton ) {
			this.saveListButton.disabled = true;
		}
	}

	enableSaveListButton() {
		if ( this.saveListButton ) {
			this.saveListButton.disabled = false;
		}
	}

	disableGeneratePdfButton() {
		if ( this.generatePdfButton ) {
			this.generatePdfButton.disabled = true;
			this.generatePdfButton.classList.add( 'disabled' );
		}
	}

	enableGeneratePdfButton() {
		if ( this.generatePdfButton ) {
			this.generatePdfButton.disabled = false;
			this.generatePdfButton.classList.remove( 'disabled' );
		}
	}

	/**
	 * Renders a Bootstrap loading spinner next to a button
	 * @param isLoading Represents whether the button is in a loading state
	 * @param button The button to apply the loading state to
	 * @returns
	 */
	useLoadingSpinner( isLoading: boolean, button: 'list' | 'pdf' ) {
		const buttonElement =
			button === 'list' ? this.saveListButton : this.generatePdfButton;
		if ( ! buttonElement ) {
			return;
		}
		buttonElement.disabled = isLoading;
		const modalFooter =
			this.modalEl.querySelector< HTMLElement >( '.modal-footer' )!;
		if ( isLoading ) {
			insertSpinner( modalFooter );
		} else {
			removeSpinner( modalFooter.querySelector( '.spinner-border' ) );
		}
	}

	handleSaveList( target: HTMLFormElement ) {
		const formData = Object.fromEntries(
			new FormData( target )
		) as unknown as SaveListFormData;
		const expiration = this.getListExpiration(
			formData.listExpirationLength,
			formData.listExpirationUnit
		);
		const jsonData = {
			...formData,
			listExpiry: expiration,
		};
		return jsonData;
	}

	private getListExpiration(
		length: string,
		unit: SaveListFormData[ 'listExpirationUnit' ]
	) {
		const expirationMap: Record< string, number > = {
			days: 1,
			weeks: 7,
			months: 30,
		};
		const expiry = expirationMap[ unit ] * Number( length );
		const expiryDate = new Date();
		expiryDate.setDate( expiryDate.getDate() + expiry );
		return dateAsYmd( expiryDate );
	}
}
