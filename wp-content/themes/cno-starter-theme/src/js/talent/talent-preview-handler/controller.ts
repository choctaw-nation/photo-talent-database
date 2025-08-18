import ModalHandler from './view';
import Model from './model';

window.addEventListener( 'DOMContentLoaded', () => {
	try {
		const Modal = new ModalHandler();
		const WP = new Model();
		Modal.onShow( async ( id ) => {
			const data = await WP.getTalentData( id );
			Modal.setBodyContent( generateBodyString( data ) );
		} );
	} catch ( err ) {
		console.error( err );
	}
} );

function generateBodyString( data: {} ): string {
	console.warn( 'function not implemented yet!' );
	return '';
}
