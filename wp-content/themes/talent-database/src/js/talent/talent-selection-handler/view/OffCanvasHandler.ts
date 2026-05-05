import Offcanvas from 'bootstrap/js/dist/offcanvas';
import { insertSpinner, removeSpinner } from '@utils/spinner';

export default class OffCanvasHandler {
	offcanvasEl: HTMLDivElement;
	customDateForm: HTMLFormElement;
	id: string;
	private offcanvas: Offcanvas | null = null;

	constructor( id: string ) {
		this.id = id;
		const offcanvas = document.getElementById(
			'custom-date-offcanvas'
		) as HTMLDivElement;
		if ( ! offcanvas ) {
			throw new Error( "Couldn't find the offcanvas element!" );
		}
		this.offcanvasEl = offcanvas;
		this.offcanvas = new Offcanvas( this.offcanvasEl );

		const form = this.offcanvasEl.querySelector< HTMLFormElement >(
			'#custom-date-picker'
		);
		if ( ! form ) {
			throw new Error( "Couldn't find the custom date picker form!" );
		}
		this.customDateForm = form;
	}

	toggleOffcanvas() {
		this.offcanvas?.toggle();
	}

	handleFormSubmission( callback: ( date: string ) => Promise< void > ) {
		this.customDateForm.addEventListener( 'submit', ( ev ) => {
			ev.preventDefault();
			const formData = new FormData( this.customDateForm );
			const dateFields = [ 'date-year', 'date-month', 'date-day' ];
			const dateValues = dateFields.map( ( field ) =>
				formData.get( field )
			);
			const date = dateValues.reduce( ( acc, value, index ) => {
				if ( typeof value !== 'string' || value.trim() === '' ) {
					throw new Error(
						`Invalid date: ${ dateFields[ index ] } is required`
					);
				}
				const paddedValue = value.padStart( index === 0 ? 4 : 2, '0' );
				return acc + paddedValue;
			}, '' ) as string;
			this.setFormElementsDisability( true );
			const submitButton = this.customDateForm.querySelector(
				'button[type="submit"]'
			) as HTMLButtonElement;
			insertSpinner( submitButton, 'beforeend', [
				'text-white',
				'spinner-border-sm',
				'ms-3',
			] );
			callback( date ).then( () => {
				this.setFormElementsDisability( false );
				removeSpinner(
					submitButton.querySelector( '.spinner-border' )
				);
				this.toggleOffcanvas();
			} );
		} );
	}

	private setFormElementsDisability( disabled: boolean ) {
		Array.from( this.customDateForm.elements ).forEach( ( el ) => {
			if (
				el instanceof HTMLInputElement ||
				el instanceof HTMLSelectElement ||
				el instanceof HTMLTextAreaElement ||
				el instanceof HTMLButtonElement
			) {
				el.disabled = disabled;
			}
		} );
	}
}
