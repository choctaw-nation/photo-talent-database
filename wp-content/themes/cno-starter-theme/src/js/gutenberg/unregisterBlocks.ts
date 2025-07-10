import { registerPlugin } from '@wordpress/plugins';
import { unregisterBlockVariation, getBlockTypes } from '@wordpress/blocks';

registerPlugin( 'cno-remove-block-variations', {
	render: () => {
		getBlockTypes().forEach( ( blockType ) => {
			const blocksWithVariations = {
				'core/group': [ 'group-row', 'group-stack', 'group-grid' ],
			};
			if ( blocksWithVariations[ blockType.name ] ) {
				blocksWithVariations[ blockType.name ].forEach(
					( variation: string ) =>
						unregisterBlockVariation( blockType.name, variation )
				);
			}
		} );

		// The plugin is only used to disable blocks, so you don't need to render anything.
		return null;
	},
} );
