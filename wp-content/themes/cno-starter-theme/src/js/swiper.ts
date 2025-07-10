import Swiper from 'swiper';
import { Navigation, A11y, Pagination } from 'swiper/modules';
import 'swiper/scss';
import 'swiper/scss/navigation';
import 'swiper/scss/pagination';
import 'swiper/scss/a11y';
import { SwiperOptions } from 'swiper/types/swiper-options';

const defaultArgs = {
	modules: [ Navigation, Pagination, A11y ],
	direction: 'horizontal',
	loop: false,

	// If we need pagination
	pagination: {
		el: '.swiper-pagination',
	},

	// Navigation arrows
	navigation: {
		nextEl: '.swiper-button-next',
		prevEl: '.swiper-button-prev',
	},
	breakpoints: {
		767: {
			slidesPerView: 3,
			slidesPerGroup: 3,
		},
	},
	spaceBetween: 20,
} as SwiperOptions;

/**
 * Simple API to init new Swiper object with default and overridable args
 *
 * @param {HTMLElement} el the element to create a slider on
 * @param {SwiperOptions} args the Swiper Options arg to override
 * @returns swiper instance
 */
export function newSlider( el: HTMLElement, args: SwiperOptions = {} ): Swiper {
	const newArgs = Object.assign( {}, defaultArgs, args );
	const swiper = new Swiper( el, newArgs );
	return swiper;
}
