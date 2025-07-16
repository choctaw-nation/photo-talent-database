export default function sendEmail() {
	const form = document.getElementById(
		'create-email-form'
	) as HTMLFormElement;
	form.addEventListener( 'submit', ( event ) => {
		event.preventDefault();
		const email = form.email.value;
		const message = form.message.value;
		alert( `Email: ${ email }, Message: ${ message }` );
	} );
}
