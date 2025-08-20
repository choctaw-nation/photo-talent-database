import ModalHandler from './view';
import Model, { SuccessfulApiResponse } from './model';
import { insertSpinner } from '../../utils/spinner';

window.addEventListener( 'DOMContentLoaded', () => {
	try {
		const Modal = new ModalHandler();
		const WP = new Model();
		Modal.onShow( async ( id ) => {
			Modal.clearBody();
			insertSpinner( Modal.body );
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
