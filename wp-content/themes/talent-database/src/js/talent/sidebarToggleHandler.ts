import Collapse from 'bootstrap/js/dist/collapse';
import { BS_BREAKPOINTS } from '../utils/consts';

export default function sidebarToggleHandler() {
	const viewportWidth = window.innerWidth;
	const sidebar = document.getElementById( 'sidebar-filters' ) as HTMLElement;
	if ( viewportWidth < BS_BREAKPOINTS.lg ) {
		const collapse = Collapse.getOrCreateInstance( sidebar );
		collapse.hide();
	}
}
