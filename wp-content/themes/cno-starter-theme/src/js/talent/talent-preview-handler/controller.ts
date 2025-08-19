import ModalHandler from './view';
import Model, { SuccessfulApiResponse } from './model';

window.addEventListener( 'DOMContentLoaded', () => {
	try {
		const Modal = new ModalHandler();
		const WP = new Model();
		Modal.onShow( async ( id ) => {
			const data = await WP.getTalentData( id );
			if ( data.success ) {
				Modal.clearBody();
				Modal.setBodyContent( generateBodyString( data.data ) );
			}
		} );
	} catch ( err ) {
		console.error( err );
	}
} );

function generateBodyString( data: SuccessfulApiResponse[ 'data' ] ): string {
	return data.html;
}
