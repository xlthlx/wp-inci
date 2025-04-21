import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';
import { useEntityProp } from '@wordpress/core-data';
import {
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { useState, useEffect } from '@wordpress/element';
import HTMLReactParser from 'html-react-parser';
import './../../../../public/css/wp-inci.css';

export default function Edit() {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const products = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'postType', 'product', {
			status: 'publish',
		} );
	}, [] );

	const productId = meta[ 'wi-product-id' ];
	const customTitle = meta[ 'wi-custom-title' ];
	const hasLink = meta[ 'wi-product-link' ] === 'Yes';
	const hasProductContent = meta[ 'wi-product-content' ] === 'Yes';
	const hasList = meta[ 'wi-ingredients-list' ] === 'Yes';
	const hasSafety = meta[ 'wi-ingredients-safety' ] === 'Yes';
	const hasDisclaimer = meta[ 'wi-disclaimer' ] === 'Yes';
	const safety = meta[ 'wi-ingredients-safety' ] === 'Yes' ? '' : 'true';
	const disclaimer = meta[ 'wi-disclaimer' ] === 'Yes' ? 'true' : 'false';

	const updateProduct = ( newValue ) => {
		setMeta( { ...meta, 'wi-product-id': newValue } );
	};
	const updateTitle = ( newValue ) => {
		setMeta( { ...meta, 'wi-custom-title': newValue } );
	};
	const setLink = ( newValue ) => {
		setMeta( { ...meta, 'wi-product-link': newValue ? 'Yes' : '' } );
	};
	const setProductContent = ( newValue ) => {
		setMeta( { ...meta, 'wi-product-content': newValue ? 'Yes' : '' } );
	};
	const setList = ( newValue ) => {
		setMeta( { ...meta, 'wi-ingredients-list': newValue ? 'Yes' : '' } );
	};
	const setSafety = ( newValue ) => {
		setMeta( { ...meta, 'wi-ingredients-safety': newValue ? 'Yes' : '' } );
	};

	const setDisclaimer = ( newValue ) => {
		setMeta( { ...meta, 'wi-disclaimer': newValue ? 'Yes' : '' } );
	};

	const options = [];
	let selProduct,
		selLink,
		selContent = '';
	if ( products ) {
		options.push( { value: 0, label: 'Select a product' } );
		products.forEach( ( product ) => {
			options.push( {
				value: product.id,
				label: decodeEntities( product.title.rendered ),
			} );
			if ( product.id === Number( productId ) ) {
				selProduct = decodeEntities( product.title.rendered );
				selLink = product.link;
				selContent = HTMLReactParser(
					String( product.content.rendered )
				);
			}
		} );
	} else {
		options.push( { value: 0, label: 'Loading...' } );
	}

	const tempTitle =
		productId !== '' ? selProduct : __( 'Select a product', 'wp-inci' );
	const tempCustom = customTitle !== '' ? customTitle : tempTitle;
	const renderTitle = hasLink ? (
		<a title={ tempCustom } href={ selLink }>
			{ tempCustom }
		</a>
	) : (
		tempCustom
	);

	const queryParams = {
		product_id: productId,
		safety,
		disclaimer,
	};

	const [ error, setError ] = useState( null );
	const [ table, setTable ] = useState( null );
	const [ isLoaded, setIsLoaded ] = useState( false );

	useEffect( () => {
		apiFetch( {
			path: addQueryArgs( '/wp-inci/v1/get-table', queryParams ),
		} ).then(
			( table ) => {
				setIsLoaded( true );
				setTable( table );
			},
			( error ) => {
				setIsLoaded( true );
				setError( error );
			}
		);
	}, [ queryParams ] );

	let renderTable = '';
	if ( error ) {
		renderTable = <p>{ error.message }</p>;
	} else if ( ! isLoaded ) {
		renderTable = <p>Loading...</p>;
	} else if ( table ) {
		renderTable = HTMLReactParser( String( table ) );
	}

	return (
		<>
			<InspectorControls>
				<PanelBody
					className="wp-inci-panel"
					title={ __( 'Product options' ) }
					initialOpen={ true }
				>
					<PanelRow>
						<fieldset>
							<SelectControl
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								label={ __( 'Select a product', 'wp-inci' ) }
								help={ __(
									'Select a product form the list',
									'wp-inci'
								) }
								options={ options }
								value={ productId }
								onChange={ updateProduct }
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<TextControl
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								label={ __( 'Custom title', 'wp-inci' ) }
								help={ __(
									'Replaces the Product title',
									'wp-inci'
								) }
								value={ customTitle }
								onChange={ updateTitle }
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __(
									'Link the Product title?',
									'wp-inci'
								) }
								help={
									hasLink
										? __( 'Yes', 'wp-inci' )
										: __( 'No', 'wp-inci' )
								}
								checked={ hasLink }
								onChange={ setLink }
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __(
									'Show the Product content?',
									'wp-inci'
								) }
								help={
									hasProductContent
										? __( 'Yes', 'wp-inci' )
										: __( 'No', 'wp-inci' )
								}
								checked={ hasProductContent }
								onChange={ setProductContent }
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __(
									"Don't show Ingredients listing?",
									'wp-inci'
								) }
								help={
									hasList
										? __(
												"Don't show Ingredients",
												'wp-inci'
										  )
										: __( 'Show Ingredients', 'wp-inci' )
								}
								checked={ hasList }
								onChange={ setList }
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __(
									"Don't show Ingredients Safety?",
									'wp-inci'
								) }
								help={
									hasSafety
										? __(
												"Don't show Ingredients Safety",
												'wp-inci'
										  )
										: __(
												'Show Ingredients Safety',
												'wp-inci'
										  )
								}
								checked={ hasSafety }
								onChange={ setSafety }
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __(
									"Don't show the Disclaimer?",
									'wp-inci'
								) }
								help={
									hasDisclaimer
										? __(
												"Don't show the Disclaimer",
												'wp-inci'
										  )
										: __( 'Show the Disclaimer', 'wp-inci' )
								}
								checked={ hasDisclaimer }
								onChange={ setDisclaimer }
							/>
						</fieldset>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<div className="wp-block-wp-inci-product">
					<h3>{ renderTitle }</h3>
					{ hasProductContent ? selContent : '' }
					{ hasList ? '' : renderTable }
				</div>
			</div>
		</>
	);
}
