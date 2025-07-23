import { AllowedImageTypes } from './types';

export default async function convertImage(
	mime: AllowedImageTypes,
	url: string
): Promise< { data: string; width: number; height: number } > {
	const types = new Set< AllowedImageTypes >( [
		'webp',
		'avif',
		'jpeg',
		'png',
	] );

	if ( ! types.has( mime ) ) {
		return Promise.reject( new Error( 'Unsupported image format' ) );
	}
	try {
		return await ImageHandler.imageToDataUrl( url );
	} catch ( error ) {
		throw new Error( 'Error converting image: ' + error.message );
	}
}

class ImageHandler {
	private static retinaScale = 2; // 2x for retina quality
	private static displayWidth = 2; // inches
	private static displayHeight = 3; // inches
	private static baseDPI = 96;

	private static get targetWidth() {
		return this.displayWidth * this.baseDPI * this.retinaScale;
	}

	private static get targetHeight() {
		return this.displayHeight * this.baseDPI * this.retinaScale;
	}

	static async imageToDataUrl( url: string ) {
		const img = await this.loadImage( url );
		const canvas = document.createElement( 'canvas' );
		canvas.width = this.targetWidth;
		canvas.height = this.targetHeight;
		const ctx = canvas.getContext( '2d' );
		if ( ! ctx ) throw new Error( 'Canvas context not available' );
		const { sx, sy, sw, sh } = this.getCoverCrop( img.width, img.height );

		ctx.drawImage(
			img,
			sx,
			sy,
			sw,
			sh,
			0,
			0,
			this.targetWidth,
			this.targetHeight
		);
		return {
			data: canvas.toDataURL( 'image/jpg' ),
			width: this.displayWidth, // Return display size, not canvas size
			height: this.displayHeight,
		};
	}

	/**
	 * Loads an image from a URL.
	 * @param url The URL of the image to load.
	 * @returns A promise that resolves to the loaded HTMLImageElement.
	 */
	private static loadImage( url: string ): Promise< HTMLImageElement > {
		return new Promise( ( resolve, reject ) => {
			const img = new Image();
			img.crossOrigin = 'Anonymous';
			img.src = url;
			img.onload = () => resolve( img );
			img.onerror = reject;
		} );
	}

	static getCoverCrop( srcWidth: number, srcHeight: number ) {
		const srcAspect = srcWidth / srcHeight;
		const targetAspect = this.targetWidth / this.targetHeight;

		let sx = 0,
			sy = 0,
			sw = srcWidth,
			sh = srcHeight;

		if ( srcAspect > targetAspect ) {
			// Source is wider than target: crop sides
			sw = srcHeight * targetAspect;
			sx = ( srcWidth - sw ) / 2;
		} else {
			// Source is taller than target: crop top/bottom
			sh = srcWidth / targetAspect;
			sy = ( srcHeight - sh ) / 2;
		}
		return { sx, sy, sw, sh };
	}
}
