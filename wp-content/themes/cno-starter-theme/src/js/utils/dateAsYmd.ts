export default function dateAsYmd( date?: Date ): string {
	const today = date || new Date();
	const year = today.getFullYear();
	const month = String( today.getMonth() + 1 ).padStart( 2, '0' );
	const day = String( today.getDate() ).padStart( 2, '0' );
	return `${ year }${ month }${ day }`;
}
