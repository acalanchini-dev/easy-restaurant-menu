/**
 * Registrazione del blocco Restaurant Menu per Gutenberg
 */
import { registerBlockType } from '@wordpress/blocks';
import { 
    InspectorControls,
    ColorPalette,
    useBlockProps,
    AlignmentToolbar
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
    FlexItem,
    Notice,
    ButtonGroup,
    Toolbar
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
        
        // Stato per memorizzare menu e sezioni caricate dall'API
        const [menus, setMenus] = useState([]);
        const [sections, setSections] = useState([]);
        const [isMenuLoading, setIsMenuLoading] = useState(true);
        const [isSectionLoading, setIsSectionLoading] = useState(false);
        
        // Nuovo stato per le opzioni globali
        const [globalOptions, setGlobalOptions] = useState(null);
        const [isLoadingOptions, setIsLoadingOptions] = useState(true);
        
        // Funzione per verificare se è un blocco nuovo
        const isNewBlock = () => {
            // Un blocco è considerato nuovo se ha i valori predefiniti dal block.json
            return (
                attributes.displayType === metadata.attributes.displayType.default &&
                attributes.titleColor === metadata.attributes.titleColor.default &&
                attributes.sectionTitleColor === metadata.attributes.sectionTitleColor.default &&
                attributes.priceColor === metadata.attributes.priceColor.default &&
                attributes.descriptionColor === metadata.attributes.descriptionColor.default &&
                attributes.backgroundColor === metadata.attributes.backgroundColor.default &&
                attributes.borderColor === metadata.attributes.borderColor.default &&
                attributes.borderRadius === metadata.attributes.borderRadius.default &&
                // Verificando pochi attributi chiave è sufficiente per determinare se è un blocco nuovo
                true
            );
        };
        
        // Funzione per applicare le impostazioni predefinite dalle opzioni globali
        const applyGlobalDefaults = (options) => {
            if (!options) return;
            
            const { preset_details, default_layout } = options;
            
            // Aggiorna gli attributi con i valori delle opzioni globali
            const updates = {
                displayType: default_layout || 'list'
            };
            
            // Se è disponibile un preset, applica le sue impostazioni
            if (preset_details) {
                updates.titleColor = preset_details.menu_title_color;
                updates.sectionTitleColor = preset_details.section_title_color;
                updates.priceColor = preset_details.price_color;
                updates.descriptionColor = preset_details.description_color;
                updates.backgroundColor = preset_details.background_color;
                updates.borderColor = preset_details.border_color;
                updates.borderRadius = preset_details.border_radius;
                
                // Imposta gli allineamenti in base al preset
                updates.menuTitleAlignment = preset_details.text_alignment;
                updates.sectionTitleAlignment = preset_details.text_alignment;
                updates.menuDescriptionAlignment = preset_details.text_alignment;
                updates.sectionDescriptionAlignment = preset_details.text_alignment;
                
                // Imposta le dimensioni del carattere
                if (preset_details.spacing) {
                    updates.itemSpacing = preset_details.spacing;
                }
            }
            
            // Applica tutti gli aggiornamenti
            setAttributes(updates);
        };
        
        // Carica le opzioni globali all'inizio
        useEffect(() => {
            setIsLoadingOptions(true);
            
            apiFetch({ 
                path: '/easy-restaurant-menu/v1/options'
            }).then(data => {
                setGlobalOptions(data);
                setIsLoadingOptions(false);
                
                // Se è un nuovo blocco, applica le impostazioni predefinite dalle opzioni globali
                if (isNewBlock()) {
                    applyGlobalDefaults(data);
                }
            }).catch(error => {
                console.error('Errore nel caricamento delle opzioni globali:', error);
                setIsLoadingOptions(false);
            });
        }, []);
        
        // Carica i menu quando il componente è montato
        useEffect(() => {
            setIsMenuLoading(true);
            
            apiFetch({ 
                path: '/easy-restaurant-menu/v1/menus'
            }).then(data => {
                setMenus(data || []);
                setIsMenuLoading(false);
            }).catch(error => {
                console.error('Errore nel caricamento dei menu:', error);
                setIsMenuLoading(false);
            });
        }, []);
        
        // Carica le sezioni quando viene selezionato un menu
        useEffect(() => {
            if (!attributes.menu_id) {
                setSections([]);
                return;
            }
            
            setIsSectionLoading(true);
            
            apiFetch({ 
                path: `/easy-restaurant-menu/v1/menus/${attributes.menu_id}/sections`
            }).then(data => {
                setSections(data || []);
                setIsSectionLoading(false);
                
                // Solo se showAllSections è false, selezioniamo la prima sezione di default
                if (!attributes.showAllSections && !attributes.section_id && data && data.length > 0) {
                    setAttributes({ section_id: data[0].id.toString() });
                } else if (!attributes.showAllSections && attributes.section_id) {
                    // Verifica se la sezione selezionata esiste nel nuovo menu
                    const sectionExists = data.some(section => section.id.toString() === attributes.section_id);
                    if (!sectionExists && data.length > 0) {
                        setAttributes({ section_id: data[0].id.toString() });
                    }
                }
            }).catch(error => {
                console.error('Errore nel caricamento delle sezioni:', error);
                setIsSectionLoading(false);
            });
        }, [attributes.menu_id]);
        
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
        
        // Quando si cambia l'opzione di visualizzare tutte le sezioni
        const handleShowAllSectionsChange = (value) => {
            setAttributes({ showAllSections: value });
            
            // Se si passa a visualizzare tutte le sezioni, reset della sezione selezionata
            if (value) {
                setAttributes({ section_id: '' });
            } else if (sections && sections.length > 0) {
                // Se si passa a visualizzare una sola sezione, seleziona la prima se non ce n'è una già selezionata
                if (!attributes.section_id) {
                    setAttributes({ section_id: sections[0].id.toString() });
                }
            }
        };
        
        // Prepara le opzioni per il SelectControl dei menu
        const menuOptions = [];
        
        if (isMenuLoading) {
            menuOptions.push({ value: '', label: __('Caricamento menu...', 'easy-restaurant-menu') });
        } else if (menus && menus.length > 0) {
            menuOptions.push({ value: '', label: __('Seleziona un menu', 'easy-restaurant-menu') });
            
            menus.forEach((menu) => {
                menuOptions.push({
                    value: menu.id.toString(),
                    label: menu.nome
                });
            });
        } else {
            menuOptions.push({ value: '', label: __('Nessun menu disponibile', 'easy-restaurant-menu') });
        }
        
        // Prepara le opzioni per il SelectControl delle sezioni
        const sectionOptions = [];
        
        if (!attributes.menu_id) {
            sectionOptions.push({ value: '', label: __('Prima seleziona un menu', 'easy-restaurant-menu') });
        } else if (isSectionLoading) {
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
            sectionOptions.push({ value: '', label: __('Nessuna sezione disponibile in questo menu', 'easy-restaurant-menu') });
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

        // Opzioni per l'allineamento dell'immagine nella lista
        const alignmentOptions = [
            { value: 'center', label: __('Allineamento centrato', 'easy-restaurant-menu') },
            { value: 'top', label: __('Allineamento in alto', 'easy-restaurant-menu') },
        ];
        
        // Opzioni per l'allineamento del testo
        const textAlignmentOptions = [
            { value: 'left', label: __('Sinistra', 'easy-restaurant-menu') },
            { value: 'center', label: __('Centro', 'easy-restaurant-menu') },
            { value: 'right', label: __('Destra', 'easy-restaurant-menu') }
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
                    <PanelBody title={__('Impostazioni Generali', 'easy-restaurant-menu')} initialOpen={true}>
                        <SelectControl
                            label={__('Menu', 'easy-restaurant-menu')}
                            value={attributes.menu_id}
                            options={menuOptions}
                            onChange={(value) => setAttributes({ menu_id: value, section_id: '' })}
                        />
                        
                        {attributes.menu_id && sections && sections.length > 0 && (
                            <Fragment>
                                <ToggleControl
                                    label={__('Mostra tutte le sezioni', 'easy-restaurant-menu')}
                                    checked={attributes.showAllSections}
                                    onChange={handleShowAllSectionsChange}
                                    help={attributes.showAllSections ? 
                                        __('Visualizza tutte le sezioni del menu', 'easy-restaurant-menu') : 
                                        __('Visualizza solo una sezione specifica', 'easy-restaurant-menu')}
                                />
                                
                                {!attributes.showAllSections && (
                                    <SelectControl
                                        label={__('Sezione Menu', 'easy-restaurant-menu')}
                                        value={attributes.section_id}
                                        options={sectionOptions}
                                        onChange={(value) => setAttributes({ section_id: value })}
                                        disabled={!attributes.menu_id || isSectionLoading}
                                    />
                                )}
                            </Fragment>
                        )}
                        
                        {attributes.menu_id && sections && sections.length === 0 && !isSectionLoading && (
                            <Notice status="warning" isDismissible={false}>
                                {__('Questo menu non ha sezioni. Aggiungine almeno una nell\'area amministrativa.', 'easy-restaurant-menu')}
                            </Notice>
                        )}
                        
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
                        
                        {attributes.displayType === 'list' && attributes.showImages && (
                            <RadioControl
                                label={__('Allineamento immagine-testo', 'easy-restaurant-menu')}
                                selected={attributes.listImageAlignment}
                                options={alignmentOptions}
                                onChange={(value) => setAttributes({ listImageAlignment: value })}
                                help={__('Sceglie come allineare verticalmente il testo rispetto all\'immagine nella vista lista', 'easy-restaurant-menu')}
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
                            label={__('Mostra descrizioni elementi', 'easy-restaurant-menu')}
                            checked={attributes.showDescriptions}
                            onChange={(value) => setAttributes({ showDescriptions: value })}
                        />
                        
                        <ToggleControl
                            label={__('Mostra descrizione menu', 'easy-restaurant-menu')}
                            checked={attributes.showMenuDescription}
                            onChange={(value) => setAttributes({ showMenuDescription: value })}
                            help={__('Mostra/nascondi la descrizione del menu completo', 'easy-restaurant-menu')}
                        />
                        
                        <ToggleControl
                            label={__('Mostra descrizioni sezioni', 'easy-restaurant-menu')}
                            checked={attributes.showSectionDescriptions}
                            onChange={(value) => setAttributes({ showSectionDescriptions: value })}
                            help={__('Mostra/nascondi le descrizioni delle sezioni', 'easy-restaurant-menu')}
                        />
                        
                        <ToggleControl
                            label={__('Mostra titolo menu', 'easy-restaurant-menu')}
                            checked={attributes.showMenuTitle}
                            onChange={(value) => setAttributes({ showMenuTitle: value })}
                            help={__('Mostra/nascondi il titolo principale del menu', 'easy-restaurant-menu')}
                        />
                    </PanelBody>
                    
                    <PanelBody title={__('Stile e Colori', 'easy-restaurant-menu')} initialOpen={false}>
                        <p>{__('Colore titoli elementi', 'easy-restaurant-menu')}</p>
                        <ColorPalette
                            value={attributes.titleColor}
                            onChange={(value) => setAttributes({ titleColor: value })}
                        />
                        
                        <p>{__('Colore titoli sezioni', 'easy-restaurant-menu')}</p>
                        <ColorPalette
                            value={attributes.sectionTitleColor}
                            onChange={(value) => setAttributes({ sectionTitleColor: value })}
                        />
                        
                        <p>{__('Colore linea sotto i titoli sezioni', 'easy-restaurant-menu')}</p>
                        <ColorPalette
                            value={attributes.sectionTitleUnderlineColor}
                            onChange={(value) => setAttributes({ sectionTitleUnderlineColor: value })}
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
                    
                    <PanelBody title={__('Allineamento Testi', 'easy-restaurant-menu')} initialOpen={false}>
                        <p>{__('Allineamento titolo menu', 'easy-restaurant-menu')}</p>
                        <ButtonGroup>
                            {textAlignmentOptions.map(option => (
                                <Button
                                    key={option.value}
                                    isPrimary={attributes.menuTitleAlignment === option.value}
                                    isSecondary={attributes.menuTitleAlignment !== option.value}
                                    onClick={() => setAttributes({ menuTitleAlignment: option.value })}
                                >
                                    {option.label}
                                </Button>
                            ))}
                        </ButtonGroup>
                        
                        <p style={{ marginTop: '15px' }}>{__('Allineamento titoli sezioni', 'easy-restaurant-menu')}</p>
                        <ButtonGroup>
                            {textAlignmentOptions.map(option => (
                                <Button
                                    key={option.value}
                                    isPrimary={attributes.sectionTitleAlignment === option.value}
                                    isSecondary={attributes.sectionTitleAlignment !== option.value}
                                    onClick={() => setAttributes({ sectionTitleAlignment: option.value })}
                                >
                                    {option.label}
                                </Button>
                            ))}
                        </ButtonGroup>
                        
                        <p style={{ marginTop: '15px' }}>{__('Allineamento descrizione menu', 'easy-restaurant-menu')}</p>
                        <ButtonGroup>
                            {textAlignmentOptions.map(option => (
                                <Button
                                    key={option.value}
                                    isPrimary={attributes.menuDescriptionAlignment === option.value}
                                    isSecondary={attributes.menuDescriptionAlignment !== option.value}
                                    onClick={() => setAttributes({ menuDescriptionAlignment: option.value })}
                                >
                                    {option.label}
                                </Button>
                            ))}
                        </ButtonGroup>
                        
                        <p style={{ marginTop: '15px' }}>{__('Allineamento descrizioni sezioni', 'easy-restaurant-menu')}</p>
                        <ButtonGroup>
                            {textAlignmentOptions.map(option => (
                                <Button
                                    key={option.value}
                                    isPrimary={attributes.sectionDescriptionAlignment === option.value}
                                    isSecondary={attributes.sectionDescriptionAlignment !== option.value}
                                    onClick={() => setAttributes({ sectionDescriptionAlignment: option.value })}
                                >
                                    {option.label}
                                </Button>
                            ))}
                        </ButtonGroup>
                    </PanelBody>
                    
                    <PanelBody title={__('Impostazioni Globali', 'easy-restaurant-menu')} initialOpen={false}>
                        <div style={{ marginBottom: '15px' }}>
                            <p>{__('Applica le impostazioni globali del plugin a questo blocco.', 'easy-restaurant-menu')}</p>
                            <Button 
                                isPrimary
                                onClick={() => {
                                    if (globalOptions) {
                                        applyGlobalDefaults(globalOptions);
                                    }
                                }}
                                disabled={isLoadingOptions}
                                style={{ width: '100%', justifyContent: 'center', marginTop: '10px' }}
                            >
                                {isLoadingOptions 
                                    ? __('Caricamento...', 'easy-restaurant-menu') 
                                    : __('Applica Impostazioni Globali', 'easy-restaurant-menu')}
                            </Button>
                        </div>
                        <Notice status="info" isDismissible={false}>
                            {__('Questo sovrascriverà le impostazioni di colore, allineamento e layout attuali del blocco.', 'easy-restaurant-menu')}
                        </Notice>
                    </PanelBody>
                </InspectorControls>
                
                <div {...blockProps}>
                    {!attributes.menu_id ? (
                        <div className="erm-editor-placeholder">
                            <div className="erm-editor-placeholder-icon dashicons dashicons-food"></div>
                            <div className="erm-editor-placeholder-title">{__('Menu Ristorante', 'easy-restaurant-menu')}</div>
                            <div className="erm-editor-placeholder-description">{__('Seleziona un menu dal pannello laterale per iniziare.', 'easy-restaurant-menu')}</div>
                        </div>
                    ) : (
                        <div className="erm-editor-preview">
                            <ServerSideRender
                                block="easy-restaurant-menu/restaurant-menu"
                                attributes={attributes}
                            />
                        </div>
                    )}
                </div>
            </Fragment>
        );
    },
    
    save: function() {
        // Il rendering viene gestito lato server
        return null;
    }
}); 