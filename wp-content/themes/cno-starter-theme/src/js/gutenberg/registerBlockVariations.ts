import { registerBlockVariation } from '@wordpress/blocks';
import type { WPBlockVariation } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { group, homeButton, button } from '@wordpress/icons';
import { __, _x } from '@wordpress/i18n';

/**
 * An args object for a block variation.
 * Key should be the name of the block it is a variation of.
 */
type VariationArgs = {
	[ blockName: string ]: WPBlockVariation[];
};

const groupVariations: VariationArgs = {
	'core/group': [
		{
			name: 'group',
			title: __( 'Group' ),
			description: __( 'Gather blocks in a container.' ),
			attributes: { layout: { type: 'constrained' } },
			isDefault: false,
			scope: [ 'block', 'inserter', 'transform' ],
			isActive: ( blockAttributes ) =>
				! blockAttributes.layout ||
				! blockAttributes.layout?.type ||
				blockAttributes.layout?.type === 'default' ||
				blockAttributes.layout?.type === 'constrained',
			icon: group,
		},
		{
			name: 'group-container',
			title: __( 'Container' ),
			description: __( 'Gather blocks in a bootstrap container.' ),
			attributes: {
				className: 'container',
				layout: {
					type: 'flex',
					orientation: 'vertical',
					justifyContent: 'stretch',
				},
				spacing: { blockGap: 'var(--wp--default--spacing--base)' },
			},
			isDefault: true,
			scope: [ 'block', 'inserter', 'transform' ],
			isActive: ( blockAttributes ) =>
				blockAttributes.className === 'container',
			icon: homeButton,
		},
	],
};

/**
 * Registers block variations
 *
 * @param variations The Block Variation args object
 */
function registerBlockVariations( variations: VariationArgs ) {
	Object.entries( variations ).forEach( ( [ blockName, variation ] ) => {
		variation.forEach( ( variation ) =>
			registerBlockVariation( blockName, variation as WPBlockVariation )
		);
	} );
}

domReady( () => {
	const variations = [ groupVariations ];
	variations.forEach( ( variation ) => registerBlockVariations( variation ) );
} );
