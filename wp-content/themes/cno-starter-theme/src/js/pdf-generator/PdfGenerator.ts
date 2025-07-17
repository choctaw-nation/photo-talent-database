import jsPDF, { jsPDFOptions } from 'jspdf';
import { ImageDetails, PostData } from '../utils/types';

export default class PdfGenerator {
	/**
	 * Document Margin as [x, y] or { top, right, bottom, left }
	 */
	margin:
		| [ number, number ]
		| { top: number; right: number; bottom: number; left: number };
	doc: jsPDF;
	fontSize: { [ key: string ]: number };
	pageOptions: jsPDFOptions;

	/**
	 * Current vertical position for text and images
	 */
	currentVerticalPosition: number = 1;

	constructor() {
		this.margin = [ 1, 1 ];
		this.pageOptions = {
			unit: 'in',
			format: 'letter',
			orientation: 'portrait',
		};
		this.doc = new jsPDF( this.pageOptions );
		this.fontSize = {
			title: 18,
			subtitle: 14,
			body: 12,
		};
	}

	buildPdf( talentData: PostData[] ): jsPDF {
		talentData.forEach( ( talent, index ) => {
			this.buildPages( talent, index );
		} );
		return this.doc;
	}

	private buildPages( talent: PostData, index: number ) {
		if ( index > 0 ) {
			this.doc.addPage( this.pageOptions.format );
			this.currentVerticalPosition = this.margin[ 1 ] as number;
		}
		this.addImages( talent );
		this.doc.setFontSize( this.fontSize.headers );
		this.doc.text(
			talent.title,
			this.margin[ 0 ],
			this.currentVerticalPosition
		);
		this.currentVerticalPosition += 0.5;
		this.doc.setFontSize( this.fontSize.body );
		if ( talent.isChoctaw ) {
			this.doc.text(
				'Choctaw',
				this.margin[ 0 ],
				this.currentVerticalPosition
			);
		}
	}

	private addImages( talent: PostData ) {
		const images = talent.images;
		if ( ! images ) {
			return;
		}
		if ( images.all ) {
			Object.entries( images.all ).forEach( ( [ key, image ] ) => {
				this.addImage( image as ImageDetails );
			} );
		} else {
			Object.entries( images ).forEach( ( [ key, image ] ) => {
				this.addImage( image as ImageDetails );
			} );
		}
	}

	private addImage( image: ImageDetails ) {
		this.doc.addImage( btoa( image.url ), 'JPEG', 15, 15, 40, 40 );
		this.currentVerticalPosition += 0.5;
	}
}
