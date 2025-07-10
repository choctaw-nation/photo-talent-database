import { addFilter } from '@wordpress/hooks';

function alterBlockClasses( settings, name: string ) {
	const blocksToEdit = {
		'core/columns': {
			title: 'Row',
			description:
				"The Bootstrap 'row' element to hold a group of columns.",
			attributes: {
				...settings.attributes,
				className: {
					type: 'string',
					default: 'row',
				},
			},
		},
		'core/col': {
			attributes: {
				...settings.attributes,
				className: {
					type: 'string',
					default: 'col',
				},
			},
		},
	};
	if ( ! blocksToEdit.hasOwnProperty( name ) ) {
		return settings;
	}

	return {
		...settings,
		...blocksToEdit[ name ],
	};
}

addFilter(
	'blocks.registerBlockType',
	'cno-starter-theme/alter-block-classes',
	alterBlockClasses
);
