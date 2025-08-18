import Modal from 'bootstrap/js/dist/modal';

export default class ModalHandler {
	modal: Modal;
	modalEl: HTMLDivElement;
	modalTrigger: HTMLButtonElement;

	get modalTitle(): string | null {
		return (
			this.modalEl.querySelector< HTMLElement >( '#talent-name' )
				?.textContent || ''
		);
	}

	set modalTitle( title: string ) {
		this.modalEl.querySelector< HTMLElement >(
			'#talent-name'
		)!.textContent = title;
	}

	get body(): HTMLElement {
		return this.modalEl.querySelector( '.modal-body' ) as HTMLElement;
	}

	constructor() {
		this.modalEl = document.getElementById(
			'talent-details-modal'
		) as HTMLDivElement;
		if ( ! this.modalEl ) {
			throw new Error( `Couldn't find modal!` );
		}
		this.modal = Modal.getOrCreateInstance( this.modalEl );
	}

	onShow( callback: ( id: number ) => void ) {
		this.modalEl.addEventListener( 'show.bs.modal', ( ev: Modal.Event ) => {
			const { name, id } = this.getTalentDetails(
				ev.relatedTarget as HTMLButtonElement
			);
			if ( ! name || ! id ) {
				throw new Error(
					`Couldn't find name or id from modal trigger!`
				);
			}
			this.modalTitle = name;
			callback( id );
		} );
	}

	clearBody(): void {
		this.body.innerHTML = '';
	}

	/**
	 * Set the body of the modal to whatever
	 *
	 * @param content HTML String
	 */
	setBodyContent( content: string ) {
		this.body.innerHTML = content;
	}

	private getTalentDetails( trigger: HTMLButtonElement ): {
		name: string | null;
		id: number | null;
	} {
		const name = trigger.getAttribute( 'data-talent-name' );
		const id = trigger.getAttribute( 'data-post-id' );
		return { name, id: id ? Number( id ) : null };
	}

	hide() {
		this.modal.hide();
	}
}
