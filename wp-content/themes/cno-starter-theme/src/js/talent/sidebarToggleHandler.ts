import { BS_BREAKPOINTS } from '../utils/consts';

export default function sidebarToggleHandler() {
	const viewportWidth = window.innerWidth;
	const sidebar = document.getElementById(
		'sidebar-filters'
	) as HTMLDetailsElement;
	if ( viewportWidth > BS_BREAKPOINTS.lg ) {
		sidebar.setAttribute( 'open', 'true' );
	}

	window.addEventListener( 'resize', ( ev ) => {
		const target = ev.target as Window;
		if ( target.innerWidth > BS_BREAKPOINTS.lg ) {
			sidebar.setAttribute( 'open', 'true' );
		} else {
			sidebar.removeAttribute( 'open' );
		}
	} );
}
