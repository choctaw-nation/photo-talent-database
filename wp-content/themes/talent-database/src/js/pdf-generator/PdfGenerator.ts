import jsPDF from 'jspdf';
import { ImageDetails, PostData } from '../utils/types';
import convertImage from './ImageHandler';
import { AllowedImageTypes } from './types';
import PdfUtilities from './PdfUtilities';

export default class PdfGenerator extends PdfUtilities {
	image = {
		width: 2,
		height: 3,
	};

	async buildPdf( talentData: PostData[] ): Promise< jsPDF > {
		for ( const [ index, talent ] of talentData.entries() ) {
			try {
				await this.buildPages( talent, index );
			} catch ( error ) {
				throw error;
			}
		}
		return this.doc;
	}

	private async buildPages( talent: PostData, index: number ) {
		if ( index > 0 ) {
			this.doc.addPage();
			this.currentPosition.y = this.margin[ 1 ];
			this.currentPosition.x = this.margin[ 0 ];
		}
		this.doc.setFontSize( this.fontSize.headers.h1 );
		const title = `${ talent.title } ${
			talent.isChoctaw ? '(Choctaw)' : ''
		}`;
		this.doc.text( title, this.margin[ 0 ], this.currentPosition.y );
		this.addTalentDetails( talent );
		await this.addImages( talent );
	}

	private addTalentDetails( talent: PostData ) {
		this.addSpace( 'y', 'lg' );
		this.doc.setFontSize( this.fontSize.headers.h4 );
		this.doc.text(
			'Contact Information',
			this.margin[ 0 ],
			this.currentPosition.y
		);
		this.doc.setFontSize( this.fontSize.body );

		const data = {
			email: `Email: ${ talent.contact!.email || '' }`,
			phone: `Phone: ${ talent.contact!.phone || '' }`,
		};
		Object.values( data ).forEach( ( value ) => {
			this.addSpace( 'y', 'md' );
			this.doc.text( value, this.margin[ 0 ], this.currentPosition.y );
		} );
	}

	private async addImages( talent: PostData ) {
		const images = talent.images;
		if ( ! images ) {
			return;
		}
		this.addSpace( 'y', 'lg' );
		this.addSpace( 'y', 'md' );
		if ( Object.hasOwn( images, 'all' ) ) {
			const imagesArray = Object.entries( images.all );
			if ( imagesArray.length === 0 ) {
				return;
			}
			this.doc.setFontSize( this.fontSize.headers.h2 );
			this.doc.text( 'Images', this.margin[ 0 ], this.currentPosition.y );
			this.currentPosition.y += this.spacers.sm;
			this.doc.setFontSize( this.fontSize.body );
			let index = 0;
			for ( const [ label, image ] of imagesArray ) {
				await this.addImage( image as ImageDetails, index, label );
				index++;
			}
		} else {
			const imagesArray = Object.entries( images );
			for ( const [ label, image ] of imagesArray ) {
				if ( ! image || ! image.url ) {
					continue;
				}
				this.doc.setFontSize( this.fontSize.headers.h2 );
				this.doc.text(
					`${ image.name } Image`,
					this.margin[ 0 ],
					this.currentPosition.y
				);
				this.currentPosition.y += this.spacers.sm;
				await this.addImage( image as ImageDetails, 0, label );
			}
		}
		this.currentPosition.x = this.margin[ 0 ];
	}

	private getScaledDimensions(
		naturalWidth: number,
		naturalHeight: number,
		maxWidth: number,
		maxHeight: number
	) {
		const widthRatio = maxWidth / naturalWidth;
		const heightRatio = maxHeight / naturalHeight;
		const scale = Math.min( widthRatio, heightRatio, 1 ); // Don't upscale
		return {
			width: naturalWidth * scale,
			height: naturalHeight * scale,
		};
	}

	private async addImage(
		image: ImageDetails,
		index: number = 0,
		slug: string = ''
	) {
		const type = this.getImageTypeFromUrl( image.url );
		const {
			data: encodedImage,
			width,
			height,
		} = await convertImage( type, image.url );
		if ( 3 === index ) {
			this.currentPosition.x = this.margin[ 0 ];
			this.currentPosition.y += this.image.height + this.spacers.md;
		}
		this.doc.addImage(
			encodedImage,
			type,
			this.currentPosition.x,
			this.currentPosition.y,
			width,
			height
		);
		this.addImageLabel( slug );
		this.currentPosition.x += width + this.spacers.sm; // Add some space before the next image
	}

	private addImageLabel( slug: string ) {
		const slugToLabel = {
			front: 'Front',
			back: 'Back',
			left: 'Left',
			right: 'Right',
			three_quarters: 'Three Quarters',
		} as const;
		const offset = {
			x: this.spacers.xl,
			y: this.image.height + this.spacers.sm,
		};
		this.currentPosition.y += offset.y;
		this.currentPosition.x += offset.x;
		this.doc.setFontSize( this.fontSize.small );
		this.doc.text(
			slugToLabel[ slug as keyof typeof slugToLabel ],
			this.currentPosition.x,
			this.currentPosition.y,
			{
				align: 'center',
			}
		);
		this.currentPosition.y -= offset.y;
		this.currentPosition.x -= offset.x;
	}

	private getImageTypeFromUrl( url: string ): AllowedImageTypes {
		if ( url.endsWith( '.webp' ) ) {
			return 'webp';
		} else if ( url.endsWith( '.avif' ) ) {
			return 'avif';
		} else if ( url.endsWith( '.jpeg' ) || url.endsWith( '.jpg' ) ) {
			return 'jpeg';
		} else if ( url.endsWith( '.png' ) ) {
			return 'png';
		}
		throw new Error( 'Unsupported image format' );
	}
}
