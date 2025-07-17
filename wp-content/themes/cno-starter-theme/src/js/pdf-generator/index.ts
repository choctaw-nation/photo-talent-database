import { GetTalentResponse, TalentPost } from '../utils/types';
import PdfGenerator from './PdfGenerator';

const generatePdfButton = document.getElementById( 'generate-pdf-btn' );
if ( generatePdfButton ) {
	generatePdfButton.addEventListener( 'click', async () => {
		const talentItems = document.querySelectorAll< HTMLButtonElement >(
			'#selected-talent-list .btn-close'
		);
		const ids = Array.from( talentItems )
			.map( ( item ) => Number( item.dataset.postId ) )
			.filter( Boolean );
		if ( ! ids.length ) {
			alert( 'No talent selected.' );
			return;
		}
		try {
			const talentData = await fetchTalentData( ids );
			generatePdf( talentData );
		} catch ( e ) {
			alert( 'Error fetching talent data.' );
			return;
		}
	} );
}

export async function fetchTalentData( ids: number[] ) {
	const response = await fetch(
		`/wp-json/cno/v1/talent?talent-ids=${ ids.join( ',' ) }&images=all`,
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

export async function generatePdf( talentData: TalentPost[] ) {
	const pdfGenerator = new PdfGenerator();
	const pdf = pdfGenerator.buildPdf( talentData );
	window.open( pdf.output( 'bloburl' ), '_blank' );
}
