/**
 * FontAwesome Icons (Solid & Brands are Free)
 *
 * How to use:
 * Use the `<i>` tags in your markup
 * Add it in the `library.add` method (and make sure it gets imported)
 *
 * @link https://fontawesome.com/icons
 */

import { library, dom } from '@fortawesome/fontawesome-svg-core';
import {
	faFacebook,
	faInstagram,
	faLinkedin,
} from '@fortawesome/free-brands-svg-icons';
import { faChevronRight } from '@fortawesome/free-solid-svg-icons';

library.add( faFacebook, faLinkedin, faInstagram, faChevronRight );

/**
 * Replaces any existing <i> tags with <svg>
 * Sets up a MutationObserver to continue doing this as the DOM changes.
 */
dom.i2svg();
