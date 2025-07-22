import Toast from 'bootstrap/js/dist/toast';
import { ToastType } from './types';

export default class ToastHandler {
	toastContainer: HTMLDivElement;

	constructor() {
		this.init();
	}

	private init() {
		const toastContainer = document.getElementById(
			'toast-container'
		) as HTMLDivElement;
		if ( ! toastContainer ) {
			const newToastContainer = document.createElement( 'div' );
			newToastContainer.className =
				'toast-container position-fixed top-0 start-50 translate-middle-x p-3';
			newToastContainer.id = 'toast-container';
			document.body.appendChild( newToastContainer );
			this.toastContainer = newToastContainer;
		} else {
			this.toastContainer = toastContainer;
		}
	}

	showToast(
		message: string,
		type: ToastType = 'success',
		onHiddenCallback?: () => void
	) {
		const toastElement = this.createToastElement( message, type );
		toastElement.addEventListener( 'hidden.bs.toast', () => {
			toastElement.remove();
			if ( onHiddenCallback ) {
				onHiddenCallback();
			}
		} );
		this.toastContainer.insertAdjacentElement( 'beforeend', toastElement );
		return Toast.getOrCreateInstance( toastElement, {
			delay: 2500,
		} ).show();
	}

	private createToastElement( message: string, type: ToastType ) {
		const toastEl = document.createElement( 'div' );
		const color = type === 'error' ? 'danger' : type;
		const title = this.getToastTitle( type );

		toastEl.className = `toast bg-${ color }-subtle`;
		toastEl.setAttribute( 'role', 'alert' );
		toastEl.setAttribute( 'aria-live', 'assertive' );
		toastEl.setAttribute( 'aria-atomic', 'true' );

		toastEl.innerHTML = `
			<div class="toast-header d-flex flex-wrap justify-content-between align-items-center">
				<span class="d-inline-flex align-items-center gap-2">
					<div class="text-${ color } rounded-5 flex-grow-0 m-0" style="height: 20px; width: 20px;">
						<svg aria-hidden="true" class="bd-placeholder-img rounded me-2" height="100%" preserveAspectRatio="xMidYMid slice" width="100%" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="currentColor"></rect></svg>
					</div>
					<strong class="fs-5 fw-normal mt-1 mb-0">${ title }</strong>
				</span>
				<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
			</div>
			<div class="toast-body fs-6">
				${ message }
			</div>
		`;

		return toastEl;
	}

	private getToastTitle( type: ToastType ): string {
		switch ( type ) {
			case 'success':
				return 'Success';
			case 'error':
				return 'Error';
			case 'info':
				return 'Update';
			case 'warning':
				return 'Warning';
			default:
				return 'Notification';
		}
	}
}
