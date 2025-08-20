import dateAsYmd from '../utils/dateAsYmd';
import { insertSpinner, removeSpinner } from '../utils/spinner';
import ToastHandler from '../utils/ToastHandler';
import { GetTalentResponse, PostData } from '../utils/types';
import PdfGenerator from './PdfGenerator';

export function handleGeneratePdfButtonClick() {
	const generatePdfButton = document.getElementById(
		'generate-pdf-btn'
	) as HTMLButtonElement | null;

	if ( ! generatePdfButton ) {
		return;
	}

	generatePdfButton.addEventListener( 'click', async () => {
		if ( generatePdfButton.disabled ) return;
		const toaster = new ToastHandler();
		const talentItems = document.querySelectorAll< HTMLButtonElement >(
			'#selected-talent-list .btn-close'
		);
		const ids = Array.from( talentItems )
			.map( ( item ) => Number( item.dataset.postId ) )
			.filter( Boolean );
		if ( ! ids.length ) {
			toaster.showToast( 'No talent selected.', 'info' );
			return;
		}
		let spinner: HTMLElement | null = null;
		try {
			const parentElement = getParentElement();
			if ( parentElement ) {
				spinner = insertSpinner( parentElement );
				generatePdfButton.disabled = true;
				generatePdfButton.classList.add( 'disabled' );
				const talentData = await fetchTalentData( ids );
				await generatePdf( talentData );
			}
		} catch ( error ) {
			toaster.showToast( 'Error building the PDF.', 'error' );
			console.error( 'Error generating PDF:', error );
			return;
		} finally {
			removeSpinner( spinner );
			generatePdfButton.disabled = false;
			generatePdfButton.classList.remove( 'disabled' );
		}
	} );
}

function getParentElement(): HTMLElement | null {
	const elements = [
		'#create-pdf-modal .modal-footer',
		'#selected-talent-list-footer',
	];
	for ( const selector of elements ) {
		const element = document.querySelector< HTMLElement >( selector );
		if ( element ) {
			return element;
		}
	}
	return null;
}

export async function fetchTalentData( ids: number[] ): Promise< PostData[] > {
	const response = await fetch(
		`/wp-json/cno/v1/talent?talent-ids=${ ids.join(
			','
		) }&images=all&fields=isChoctaw,contact`,
		{
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': window.cnoApi.nonce,
			},
		}
	);
	if ( ! response.ok ) throw new Error( 'Failed to fetch talent data' );
	const result: GetTalentResponse = await response.json();
	return result.posts || [];
}

export async function generatePdf( talentData: PostData[] ) {
	const pdfGenerator = new PdfGenerator();
	try {
		const pdf = await pdfGenerator.buildPdf( talentData );
		pdf.save( `${ dateAsYmd() }-talent-list.pdf` );
	} catch ( err ) {
		throw err;
	}
}
