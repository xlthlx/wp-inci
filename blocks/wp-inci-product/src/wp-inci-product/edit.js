import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';
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

export default function Edit( props ) {
	const { attributes } = props;
	const { setAttributes } = props;
	const products = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'postType', 'product', {
			status: 'publish',
		} );
	}, [] );

	const productId = attributes.productId ? attributes.productId : '0';
	const customTitle = attributes.customTitle;
	const hasLink = attributes.productLink === 'Yes';
	const hasProductContent = attributes.productContent === 'Yes';
	const hasList = attributes.ingredientsList === 'No';
	const hasSafety = attributes.ingredientsSafety === 'No';
	const hasDisclaimer = attributes.disclaimer === 'No';
	const safety = attributes.ingredientsSafety === 'No' ? '' : 'true';
	const disclaimer = attributes.disclaimer === 'No' ? 'true' : 'false';

	const updateProduct = ( newValue ) => {
		setAttributes( { productId: newValue } );
	};
	const updateTitle = ( newValue ) => {
		setAttributes( { customTitle: newValue } );
	};
	const setLink = ( newValue ) => {
		setAttributes( { productLink: newValue ? 'Yes' : '' } );
	};
	const setProductContent = ( newValue ) => {
		setAttributes( { productContent: newValue ? 'Yes' : '' } );
	};
	const setList = ( newValue ) => {
		setAttributes( { ingredientsList: newValue ? 'No' : '' } );
	};
	const setSafety = ( newValue ) => {
		setAttributes( { ingredientsSafety: newValue ? 'No' : '' } );
	};

	const setDisclaimer = ( newValue ) => {
		setAttributes( { disclaimer: newValue ? 'No' : '' } );
	};

	const options = [];
	let selLink,
		selContent = '';
	if ( products ) {
		options.push( { value: 0, label: 'Select a product' } );
		products.forEach( ( product ) => {
			options.push( {
				value: product.id,
				label: decodeEntities( product.title.rendered ),
			} );
			if ( product.id === Number( productId ) ) {
				selLink = product.link;
				selContent = HTMLReactParser(
					String( product.content.rendered )
				);
			}
		} );
	} else {
		options.push( { value: 0, label: 'Loading...' } );
	}

	let renderTitle;
	if ( productId === '0' ) {
		renderTitle = __( 'Select a product', 'wp-inci' );
	} else {
		let selProduct = '';
		if ( products ) {
			products.forEach( ( product ) => {
				if ( product.id === Number( productId ) ) {
					selProduct = decodeEntities( product.title.rendered );
				}
			} );
		}
		renderTitle = customTitle ? customTitle : selProduct;
	}

	renderTitle = hasLink ? (
		<a title={ renderTitle } href={ selLink }>
			{ renderTitle }
		</a>
	) : (
		renderTitle
	);

	const [ error, setError ] = useState( null );
	const [ table, setTable ] = useState( null );
	const [ isLoaded, setIsLoaded ] = useState( false );

	useEffect( () => {
		const queryParams = {
			product_id: productId,
			safety,
			disclaimer,
		};
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
	}, [ productId, safety, disclaimer ] );

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
