import jsPDF from 'jspdf';
import { TalentPost } from '../utils/types';

export default class PdfGenerator {
	/**
	 * Document Margin as [x, y] or { top, right, bottom, left }
	 */
	margin:
		| [ number, number ]
		| { top: number; right: number; bottom: number; left: number };
	doc: jsPDF;

	fontSize: { [ key: string ]: number };

	constructor() {
		this.margin = [ 1, 1 ];
		this.doc = new jsPDF( {
			unit: 'in',
			format: 'letter',
			orientation: 'portrait',
		} );
		this.fontSize = {
			title: 18,
			subtitle: 14,
			body: 12,
		};
	}

	buildPdf( talentData: TalentPost[] ) {
		talentData.forEach( ( talent, idx ) => {
			if ( idx > 0 ) this.doc.addPage();
			let y = 20;
			// Add thumbnail if available (expects base64 or image url)
			if ( talent.thumbnail ) {
				// If thumbnail is an <img> tag, extract src
				const match = talent.thumbnail.match( /src=["']([^"']+)["']/ );
				if ( match && match[ 1 ] ) {
					// For demo: try to add image, but jsPDF needs base64 or CORS-enabled image
					// this.doc.addImage(match[1], 'JPEG', 15, y, 40, 40); // Uncomment if image is base64
					y += 45;
				}
			}
			this.doc.setFontSize( this.fontSize.headers );
			this.doc.text( talent.title || 'Talent Name', 15, y );
			y += 10;
			this.doc.setFontSize( this.fontSize.body );
			if ( talent.isChoctaw ) this.doc.text( 'Choctaw', 15, y );
			y += 10;
			if ( talent.lastUsed )
				this.doc.text( 'Last Used: ' + talent.lastUsed, 15, y );
			// Add more fields as needed to match single.php
		} );
	}
}
