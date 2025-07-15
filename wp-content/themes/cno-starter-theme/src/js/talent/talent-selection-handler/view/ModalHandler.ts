import Modal from 'bootstrap/js/dist/modal';
import Tab from 'bootstrap/js/dist/tab';

export default class ModalHandler {
	modal: Modal;
	modalEl: HTMLDivElement;
	modalTrigger: HTMLButtonElement;

	get sendEmailButton(): HTMLButtonElement | null {
		return document.querySelector< HTMLButtonElement >(
			'input[type="submit"][value="Send Email"]'
		);
	}

	constructor() {
		this.modalEl = document.getElementById(
			'create-email-modal'
		) as HTMLDivElement;
		this.modalTrigger = document.getElementById(
			'create-email-modal-trigger'
		) as HTMLButtonElement;
		this.initModal();
	}

	initModal() {
		this.modal = Modal.getOrCreateInstance( this.modalEl );
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

	onShow( callback: Function ) {
		this.modalEl.addEventListener( 'show.bs.modal', () => {
			this.initTabs();
			callback();
		} );
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
					this.showOtherTrigger( trigger, triggers );
					this.disableSendEmailButton();
					trigger.disabled = true;
				}
				if ( 'back-to-form' === trigger.id ) {
					this.showOtherTrigger( trigger, triggers );
					this.enableSendEmailButton();
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

	private disableSendEmailButton() {
		if ( this.sendEmailButton ) {
			this.sendEmailButton.disabled = true;
		}
	}

	private enableSendEmailButton() {
		if ( this.sendEmailButton ) {
			this.sendEmailButton.disabled = false;
		}
	}
}
