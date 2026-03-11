// eslint-disable-next-line import/named
import jsPDF, { jsPDFOptions } from 'jspdf';

type FontSizes = {
	body: number;
	headers: {
		h1: number;
		h2: number;
		h3: number;
		h4: number;
		h5: number;
		h6: number;
	};
	small: number;
};

type Spacers = {
	sm: number;
	md: number;
	lg: number;
	xl: number;
};

export default class PdfUtilities {
	doc: jsPDF;
	/**
	 * Document Margin as [x, y] or { top, right, bottom, left }
	 */
	margin:
		| [ number, number ]
		| { top: number; right: number; bottom: number; left: number };

	fontSize: FontSizes;

	spacers = {
		sm: 0.15,
		md: 0.25,
		lg: 0.5,
		xl: 1,
	};

	pageOptions = {
		unit: 'in',
		format: 'letter',
		orientation: 'portrait',
	} as jsPDFOptions;

	currentPosition: {
		x: number;
		y: number;
	};

	constructor() {
		this.margin = [ 1, 1 ];
		this.currentPosition = {
			x: this.margin[ 0 ],
			y: this.margin[ 1 ],
		};
		this.currentPosition = {
			x: this.margin[ 0 ] as number,
			y: this.margin[ 1 ] as number,
		};
		this.fontSize = {
			headers: {
				h1: 35.84,
				h2: 29.86,
				h3: 24.89,
				h4: 20.74,
				h5: 17.28,
				h6: 14.4,
			},
			body: 12,
			small: 10,
		};
		this.doc = new jsPDF( this.pageOptions );
	}

	addSpace( direction: 'x' | 'y', amount: keyof Spacers ) {
		this.currentPosition[ direction ] += this.spacers[ amount ];
	}
}
