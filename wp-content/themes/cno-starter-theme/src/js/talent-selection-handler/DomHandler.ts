import LocalStorage, { PostData } from './LocalStorage';
import ModalHandler from './ModalHandler';

export default class DomHandler {
	TARGET_SELECTOR: string;
	BUTTON_ATTRIBUTE: string;

	modalHandler: ModalHandler;
	selectionTracker: HTMLSpanElement;

	constructor() {
		this.TARGET_SELECTOR = '#talent';
		this.BUTTON_ATTRIBUTE = 'data-post-id';
		this.modalHandler = new ModalHandler();
		this.initSelectionTracker();
	}

	init( db: LocalStorage ) {
		this.modalHandler.modalEl.addEventListener(
			'show.bs.modal',
			this.buildSelectedList.bind( this, db )
		);
		if ( db.getIds().size > 0 ) {
			this.modalHandler.showModalTrigger();
			this.selectionTracker.classList.remove( 'd-none' );
			this.selectionTracker.textContent = String( db.getIds().size );
			this.modalHandler.enableClearSelectionButton();
		}
		this.handleAddSelectionListener( db );
		this.modalHandler.handleClearSelectionListener( () => {
			db.clearIds();
			this.selectionTracker.textContent = '0';
			this.selectionTracker.classList.add( 'd-none' );
		} );
		const container = document.querySelector( this.TARGET_SELECTOR );
		if ( container ) {
			const observer = new MutationObserver( () => {
				this.handleAddSelectionListener( db );
			} );
			observer.observe( container, { childList: true, subtree: true } );
		}
	}

	private initSelectionTracker() {
		this.selectionTracker = document.getElementById(
			'selection-counter'
		) as HTMLSpanElement;

		if (
			'' === this.selectionTracker.innerText ||
			this.selectionTracker.classList.contains( 'd-none' )
		) {
			this.modalHandler.disableClearSelectionButton();
		}
	}

	/**
	 * Add click listeners to all target buttons
	 */
	private handleAddSelectionListener( db: LocalStorage ) {
		const container = document.querySelector( this.TARGET_SELECTOR );
		if ( ! container ) return;
		container.addEventListener( 'click', ( ev ) => {
			if (
				ev.target instanceof HTMLButtonElement &&
				ev.target.hasAttribute( this.BUTTON_ATTRIBUTE )
			) {
				const postId = ev.target.getAttribute( this.BUTTON_ATTRIBUTE );
				if ( ! postId ) return;
				try {
					const idDidSave = db.saveId( postId );
					if ( idDidSave ) {
						this.incrementSelectionCounter();
					}
				} catch ( error ) {
					console.error( 'Failed to save ID:', error );
				}
			}
		} );
	}

	private incrementSelectionCounter() {
		const currentCount = Number( this.selectionTracker.textContent ) || 0;
		if (
			0 === currentCount &&
			this.selectionTracker.classList.contains( 'd-none' )
		) {
			this.modalHandler.showModalTrigger();
			this.selectionTracker.classList.remove( 'd-none' );
			this.modalHandler.enableClearSelectionButton();
		}
		this.selectionTracker.textContent = String( currentCount + 1 );
	}

	private async buildSelectedList( db: LocalStorage ) {
		const ids = db.getIds();
		const listContainer = document.getElementById(
			'selected-talent-list'
		) as HTMLDivElement;
		const list = listContainer.querySelector( 'ul' );
		if ( list ) {
			if ( ids.size === list.children.length ) {
				return;
			}
		} else {
			const ul = document.createElement( 'ul' );
			ul.classList.add(
				'list-unstyled',
				'mb-0',
				'd-flex',
				'flex-column',
				'align-items-stretch',
				'row-gap-3'
			);
			listContainer.appendChild( ul );
			const posts = await db.getSelectedData();
			posts.forEach( ( post ) => {
				const listItem = this.createTalentListItem( post );
				ul.appendChild( listItem );
			} );
		}
	}

	private createTalentListItem( data: PostData ): HTMLLIElement {
		const li = document.createElement( 'li' );
		li.classList.add(
			'row',
			'row-cols-2',
			'align-items-center',
			'gx-0',
			'gap-2'
		);
		li.innerHTML = `
			<div class="col-2">
				<figure class="ratio ratio-1x1 mb-0 rounded-circle overflow-hidden">
				${ data.thumbnail }
				</figure>
			</div>
			<div>
				<h3 class="mb-0 fs-6 d-flex flex-wrap gap-2">${ data.title }${
					data.isChoctaw
						? `<span class="badge text-bg-primary fw-normal">
					Choctaw
				</span>`
						: ''
				}</h3>
				
				${
					data.lastUsed
						? `<span class="badge bg-secondary ms-2">Last Used: ${ data.lastUsed }</span>`
						: ''
				}
			</div>
		`;
		return li;
	}
}
