import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerBlockType('swgtheme/featured-content', {
	edit: ({ attributes, setAttributes }) => {
		const blockProps = useBlockProps({
			className: `swg-featured-content align-${attributes.alignment}`,
		});

		const iconMap = {
			star: '⭐',
			shield: '🛡️',
			lightsaber: '⚔️',
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Settings', 'swgtheme')}>
						<ToggleControl
							label={__('Show Icon', 'swgtheme')}
							checked={attributes.showIcon}
							onChange={(showIcon) => setAttributes({ showIcon })}
						/>
						{attributes.showIcon && (
							<SelectControl
								label={__('Icon Type', 'swgtheme')}
								value={attributes.iconType}
								options={[
									{ label: __('Star', 'swgtheme'), value: 'star' },
									{ label: __('Shield', 'swgtheme'), value: 'shield' },
									{ label: __('Lightsaber', 'swgtheme'), value: 'lightsaber' },
								]}
								onChange={(iconType) => setAttributes({ iconType })}
							/>
						)}
						<SelectControl
							label={__('Alignment', 'swgtheme')}
							value={attributes.alignment}
							options={[
								{ label: __('Left', 'swgtheme'), value: 'left' },
								{ label: __('Center', 'swgtheme'), value: 'center' },
								{ label: __('Right', 'swgtheme'), value: 'right' },
							]}
							onChange={(alignment) => setAttributes({ alignment })}
						/>
					</PanelBody>
				</InspectorControls>

				<div {...blockProps}>
					{attributes.showIcon && (
						<div className="swg-featured-icon">
							{iconMap[attributes.iconType]}
						</div>
					)}
					<RichText
						tagName="h3"
						value={attributes.title}
						onChange={(title) => setAttributes({ title })}
						placeholder={__('Enter title...', 'swgtheme')}
						className="swg-featured-title"
					/>
					<RichText
						tagName="p"
						value={attributes.content}
						onChange={(content) => setAttributes({ content })}
						placeholder={__('Enter content...', 'swgtheme')}
						className="swg-featured-text"
					/>
				</div>
			</>
		);
	},

	save: ({ attributes }) => {
		const blockProps = useBlockProps.save({
			className: `swg-featured-content align-${attributes.alignment}`,
		});

		const iconMap = {
			star: '⭐',
			shield: '🛡️',
			lightsaber: '⚔️',
		};

		return (
			<div {...blockProps}>
				{attributes.showIcon && (
					<div className="swg-featured-icon">
						{iconMap[attributes.iconType]}
					</div>
				)}
				<RichText.Content
					tagName="h3"
					value={attributes.title}
					className="swg-featured-title"
				/>
				<RichText.Content
					tagName="p"
					value={attributes.content}
					className="swg-featured-text"
				/>
			</div>
		);
	},
});
