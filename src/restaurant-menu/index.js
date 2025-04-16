/**
 * Registrazione del blocco Restaurant Menu per Gutenberg
 */
import { registerBlockType } from '@wordpress/blocks';
import { 
    InspectorControls,
    ColorPalette,
    useBlockProps
} from '@wordpress/block-editor';
import { 
    PanelBody, 
    SelectControl, 
    RangeControl,
    ToggleControl,
    RadioControl,
    BoxControl,
    Button,
    __experimentalBoxControl as BoxControlNew,
    Flex,
    FlexItem
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { Fragment, useState, useEffect } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';
import apiFetch from '@wordpress/api-fetch';

import './editor.scss';
import './style.scss';

// Importa la configurazione dal file block.json
import metadata from './block.json';

// Ottieni il BoxControl compatibile con la versione attuale di WP
const BoxCtrl = BoxControlNew || BoxControl;

// Funzione di utilità per convertire un oggetto margin/padding in stringa CSS
const getSpacingCssValue = (spacingObj) => {
    if (!spacingObj) return null;
    return `${spacingObj.top}px ${spacingObj.right}px ${spacingObj.bottom}px ${spacingObj.left}px`;
};

// Funzione per resettare un oggetto di spaziatura ai valori predefiniti
const getDefaultSpacing = (defaultValues = {}) => {
    return {
        top: defaultValues.top || 0,
        right: defaultValues.right || 0,
        bottom: defaultValues.bottom || 0,
        left: defaultValues.left || 0
    };
};

registerBlockType(metadata.name, {
    edit: function(props) {
        const { attributes, setAttributes } = props;
        const blockProps = useBlockProps();
        
        // Stato per memorizzare le sezioni caricate dall'API
        const [sections, setSections] = useState([]);
        const [isLoading, setIsLoading] = useState(true);
        
        // Carica le sezioni quando il componente è montato
        useEffect(() => {
            setIsLoading(true);
            
            apiFetch({ 
                path: '/easy-restaurant-menu/v1/sections'
            }).then(data => {
                setSections(data || []);
                setIsLoading(false);
            }).catch(error => {
                console.error('Errore nel caricamento delle sezioni:', error);
                setIsLoading(false);
            });
        }, []);
        
        // Funzione per reset di tutte le spaziature
        const resetAllSpacing = () => {
            setAttributes({
                imageMargin: getDefaultSpacing(),
                imagePadding: getDefaultSpacing(),
                titleMargin: getDefaultSpacing(),
                priceMargin: getDefaultSpacing(),
                descriptionMargin: getDefaultSpacing(),
                contentPadding: getDefaultSpacing({ top: 15, right: 15, bottom: 15, left: 15 })
            });
        };
        
        // Prepara le opzioni per il SelectControl
        const sectionOptions = [];
        
        if (isLoading) {
            sectionOptions.push({ value: '', label: __('Caricamento sezioni...', 'easy-restaurant-menu') });
        } else if (sections && sections.length > 0) {
            sectionOptions.push({ value: '', label: __('Seleziona una sezione', 'easy-restaurant-menu') });
            
            sections.forEach((section) => {
                sectionOptions.push({
                    value: section.id.toString(),
                    label: section.nome
                });
            });
        } else {
            sectionOptions.push({ value: '', label: __('Nessuna sezione disponibile', 'easy-restaurant-menu') });
        }
        
        // Opzioni per il tipo di visualizzazione
        const displayOptions = [
            { value: 'grid', label: __('Griglia', 'easy-restaurant-menu') },
            { value: 'list', label: __('Lista', 'easy-restaurant-menu') }
        ];
        
        // Opzioni per l'effetto hover
        const hoverOptions = [
            { value: 'none', label: __('Nessuno', 'easy-restaurant-menu') },
            { value: 'zoom', label: __('Zoom', 'easy-restaurant-menu') },
            { value: 'shadow', label: __('Ombra', 'easy-restaurant-menu') },
            { value: 'border', label: __('Bordo', 'easy-restaurant-menu') }
        ];
        
        // Presets di spaziatura
        const applySpacingPreset = (preset) => {
            switch(preset) {
                case 'compact':
                    setAttributes({
                        imageMargin: { top: 0, right: 0, bottom: 10, left: 0 },
                        imagePadding: { top: 0, right: 0, bottom: 0, left: 0 },
                        titleMargin: { top: 0, right: 0, bottom: 5, left: 0 },
                        priceMargin: { top: 0, right: 0, bottom: 0, left: 10 },
                        descriptionMargin: { top: 5, right: 0, bottom: 0, left: 0 },
                        contentPadding: { top: 10, right: 10, bottom: 10, left: 10 }
                    });
                    break;
                    
                case 'normal':
                    setAttributes({
                        imageMargin: { top: 0, right: 0, bottom: 15, left: 0 },
                        imagePadding: { top: 0, right: 0, bottom: 0, left: 0 },
                        titleMargin: { top: 0, right: 0, bottom: 10, left: 0 },
                        priceMargin: { top: 0, right: 0, bottom: 5, left: 15 },
                        descriptionMargin: { top: 10, right: 0, bottom: 0, left: 0 },
                        contentPadding: { top: 15, right: 15, bottom: 15, left: 15 }
                    });
                    break;
                    
                case 'spacious':
                    setAttributes({
                        imageMargin: { top: 0, right: 0, bottom: 20, left: 0 },
                        imagePadding: { top: 5, right: 5, bottom: 5, left: 5 },
                        titleMargin: { top: 0, right: 0, bottom: 15, left: 0 },
                        priceMargin: { top: 0, right: 0, bottom: 10, left: 20 },
                        descriptionMargin: { top: 15, right: 0, bottom: 0, left: 0 },
                        contentPadding: { top: 20, right: 20, bottom: 20, left: 20 }
                    });
                    break;
            }
        };
        
        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title={__('Impostazioni Contenuto', 'easy-restaurant-menu')} initialOpen={true}>
                        <SelectControl
                            label={__('Sezione Menu', 'easy-restaurant-menu')}
                            value={attributes.section_id}
                            options={sectionOptions}
                            onChange={(value) => setAttributes({ section_id: value })}
                        />
                        
                        <SelectControl
                            label={__('Tipo di visualizzazione', 'easy-restaurant-menu')}
                            value={attributes.displayType}
                            options={displayOptions}
                            onChange={(value) => setAttributes({ displayType: value })}
                        />
                        
                        {attributes.displayType === 'grid' && (
                            <RangeControl
                                label={__('Numero di colonne', 'easy-restaurant-menu')}
                                value={attributes.columns}
                                onChange={(value) => setAttributes({ columns: value })}
                                min={1}
                                max={4}
                            />
                        )}
                        
                        <ToggleControl
                            label={__('Mostra immagini', 'easy-restaurant-menu')}
                            checked={attributes.showImages}
                            onChange={(value) => setAttributes({ showImages: value })}
                        />
                        
                        {attributes.showImages && (
                            <>
                                <ToggleControl
                                    label={__('Immagini quadrate', 'easy-restaurant-menu')}
                                    checked={attributes.imageSquare}
                                    onChange={(value) => setAttributes({ imageSquare: value })}
                                    help={__('Se disattivato, le immagini manterranno il loro rapporto originale', 'easy-restaurant-menu')}
                                />
                                
                                {attributes.displayType === 'grid' && (
                                    <RangeControl
                                        label={__('Altezza immagini griglia (px)', 'easy-restaurant-menu')}
                                        value={attributes.imageSizeGrid}
                                        onChange={(value) => setAttributes({ imageSizeGrid: value })}
                                        min={100}
                                        max={400}
                                        step={10}
                                    />
                                )}
                                
                                {attributes.displayType === 'list' && (
                                    <RangeControl
                                        label={__('Dimensione immagini lista (px)', 'easy-restaurant-menu')}
                                        value={attributes.imageSizeList}
                                        onChange={(value) => setAttributes({ imageSizeList: value })}
                                        min={60}
                                        max={200}
                                        step={10}
                                    />
                                )}
                            </>
                        )}
                        
                        <ToggleControl
                            label={__('Mostra prezzi', 'easy-restaurant-menu')}
                            checked={attributes.showPrices}
                            onChange={(value) => setAttributes({ showPrices: value })}
                        />
                        
                        <ToggleControl
                            label={__('Mostra descrizioni', 'easy-restaurant-menu')}
                            checked={attributes.showDescriptions}
                            onChange={(value) => setAttributes({ showDescriptions: value })}
                        />
                    </PanelBody>
                    
                    <PanelBody title={__('Stile e Colori', 'easy-restaurant-menu')} initialOpen={false}>
                        <p>{__('Colore titoli', 'easy-restaurant-menu')}</p>
                        <ColorPalette
                            value={attributes.titleColor}
                            onChange={(value) => setAttributes({ titleColor: value })}
                        />
                        
                        <p>{__('Colore prezzi', 'easy-restaurant-menu')}</p>
                        <ColorPalette
                            value={attributes.priceColor}
                            onChange={(value) => setAttributes({ priceColor: value })}
                        />
                        
                        <p>{__('Colore descrizioni', 'easy-restaurant-menu')}</p>
                        <ColorPalette
                            value={attributes.descriptionColor}
                            onChange={(value) => setAttributes({ descriptionColor: value })}
                        />
                        
                        <p>{__('Colore sfondo elementi', 'easy-restaurant-menu')}</p>
                        <ColorPalette
                            value={attributes.backgroundColor}
                            onChange={(value) => setAttributes({ backgroundColor: value })}
                        />
                        
                        <RangeControl
                            label={__('Spaziatura tra elementi (px)', 'easy-restaurant-menu')}
                            value={attributes.itemSpacing}
                            onChange={(value) => setAttributes({ itemSpacing: value })}
                            min={0}
                            max={50}
                        />
                    </PanelBody>
                    
                    <PanelBody title={__('Bordi e Effetti', 'easy-restaurant-menu')} initialOpen={false}>
                        <RangeControl
                            label={__('Raggio bordo (px)', 'easy-restaurant-menu')}
                            value={attributes.borderRadius}
                            onChange={(value) => setAttributes({ borderRadius: value })}
                            min={0}
                            max={20}
                        />
                        
                        <RangeControl
                            label={__('Spessore bordo (px)', 'easy-restaurant-menu')}
                            value={attributes.borderWidth}
                            onChange={(value) => setAttributes({ borderWidth: value })}
                            min={0}
                            max={10}
                        />
                        
                        {attributes.borderWidth > 0 && (
                            <>
                                <p>{__('Colore bordo', 'easy-restaurant-menu')}</p>
                                <ColorPalette
                                    value={attributes.borderColor}
                                    onChange={(value) => setAttributes({ borderColor: value })}
                                />
                            </>
                        )}
                        
                        <ToggleControl
                            label={__('Ombra elemento', 'easy-restaurant-menu')}
                            checked={attributes.boxShadow}
                            onChange={(value) => setAttributes({ boxShadow: value })}
                        />
                        
                        <RadioControl
                            label={__('Effetto hover', 'easy-restaurant-menu')}
                            selected={attributes.hoverEffect}
                            options={hoverOptions}
                            onChange={(value) => setAttributes({ hoverEffect: value })}
                        />
                    </PanelBody>
                    
                    <PanelBody title={__('Spaziatura Elementi', 'easy-restaurant-menu')} initialOpen={false}>
                        <p className="erm-spacing-intro">
                            {__('Personalizza la spaziatura degli elementi del menu per ottenere il layout desiderato.', 'easy-restaurant-menu')}
                        </p>
                        
                        <Flex direction="row" justify="space-between" align="center" style={{ marginBottom: '15px' }}>
                            <FlexItem>
                                <p><strong>{__('Preset rapidi', 'easy-restaurant-menu')}</strong></p>
                            </FlexItem>
                            <FlexItem>
                                <Button 
                                    isSecondary
                                    isSmall
                                    onClick={() => applySpacingPreset('compact')}
                                >
                                    {__('Compatto', 'easy-restaurant-menu')}
                                </Button>
                            </FlexItem>
                            <FlexItem>
                                <Button 
                                    isSecondary
                                    isSmall
                                    onClick={() => applySpacingPreset('normal')}
                                >
                                    {__('Normale', 'easy-restaurant-menu')}
                                </Button>
                            </FlexItem>
                            <FlexItem>
                                <Button 
                                    isSecondary
                                    isSmall
                                    onClick={() => applySpacingPreset('spacious')}
                                >
                                    {__('Ampio', 'easy-restaurant-menu')}
                                </Button>
                            </FlexItem>
                        </Flex>
                        
                        <div style={{ textAlign: 'right', marginBottom: '20px' }}>
                            <Button 
                                isDestructive
                                isSmall
                                onClick={resetAllSpacing}
                            >
                                {__('Reimposta tutte le spaziature', 'easy-restaurant-menu')}
                            </Button>
                        </div>
                        
                        <h3>{__('Contenuto', 'easy-restaurant-menu')}</h3>
                        <BoxCtrl
                            label={__('Padding contenuto', 'easy-restaurant-menu')}
                            values={attributes.contentPadding}
                            onChange={(value) => setAttributes({ contentPadding: value })}
                            units={[{ value: 'px', label: 'px', default: 15 }]}
                            allowReset={true}
                            resetValues={getDefaultSpacing({ top: 15, right: 15, bottom: 15, left: 15 })}
                        />
                        
                        {attributes.showImages && (
                            <>
                                <h3>{__('Immagine', 'easy-restaurant-menu')}</h3>
                                <BoxCtrl
                                    label={__('Margin immagine', 'easy-restaurant-menu')}
                                    values={attributes.imageMargin}
                                    onChange={(value) => setAttributes({ imageMargin: value })}
                                    units={[{ value: 'px', label: 'px', default: 0 }]}
                                    allowReset={true}
                                    resetValues={getDefaultSpacing()}
                                />
                                
                                <BoxCtrl
                                    label={__('Padding immagine', 'easy-restaurant-menu')}
                                    values={attributes.imagePadding}
                                    onChange={(value) => setAttributes({ imagePadding: value })}
                                    units={[{ value: 'px', label: 'px', default: 0 }]}
                                    allowReset={true}
                                    resetValues={getDefaultSpacing()}
                                />
                            </>
                        )}
                        
                        <h3>{__('Testo', 'easy-restaurant-menu')}</h3>
                        <BoxCtrl
                            label={__('Margin titolo', 'easy-restaurant-menu')}
                            values={attributes.titleMargin}
                            onChange={(value) => setAttributes({ titleMargin: value })}
                            units={[{ value: 'px', label: 'px', default: 0 }]}
                            allowReset={true}
                            resetValues={getDefaultSpacing()}
                        />
                        
                        {attributes.showPrices && (
                            <BoxCtrl
                                label={__('Margin prezzo', 'easy-restaurant-menu')}
                                values={attributes.priceMargin}
                                onChange={(value) => setAttributes({ priceMargin: value })}
                                units={[{ value: 'px', label: 'px', default: 0 }]}
                                allowReset={true}
                                resetValues={getDefaultSpacing()}
                            />
                        )}
                        
                        {attributes.showDescriptions && (
                            <BoxCtrl
                                label={__('Margin descrizione', 'easy-restaurant-menu')}
                                values={attributes.descriptionMargin}
                                onChange={(value) => setAttributes({ descriptionMargin: value })}
                                units={[{ value: 'px', label: 'px', default: 0 }]}
                                allowReset={true}
                                resetValues={getDefaultSpacing()}
                            />
                        )}
                    </PanelBody>
                </InspectorControls>
                
                <div {...blockProps}>
                    <div className="erm-block-preview">
                        {!attributes.section_id ? (
                            <p>{__('Seleziona una sezione del menu dal pannello laterale.', 'easy-restaurant-menu')}</p>
                        ) : (
                            <ServerSideRender
                                block={metadata.name}
                                attributes={attributes}
                            />
                        )}
                    </div>
                </div>
            </Fragment>
        );
    },
    
    save: function() {
        // Il rendering viene gestito lato server
        return null;
    }
}); 