/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/restaurant-menu/index.js":
/*!**************************************!*\
  !*** ./src/restaurant-menu/index.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./editor.scss */ "./src/restaurant-menu/editor.scss");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./style.scss */ "./src/restaurant-menu/style.scss");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./block.json */ "./src/restaurant-menu/block.json");

/**
 * Registrazione del blocco Restaurant Menu per Gutenberg
 */











// Importa la configurazione dal file block.json


// Ottieni il BoxControl compatibile con la versione attuale di WP
const BoxCtrl = _wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalBoxControl || _wordpress_components__WEBPACK_IMPORTED_MODULE_3__.BoxControl;

// Funzione di utilità per convertire un oggetto margin/padding in stringa CSS
const getSpacingCssValue = spacingObj => {
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
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_11__.name, {
  edit: function (props) {
    const {
      attributes,
      setAttributes
    } = props;
    const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)();

    // Stato per memorizzare menu e sezioni caricate dall'API
    const [menus, setMenus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)([]);
    const [sections, setSections] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)([]);
    const [isMenuLoading, setIsMenuLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)(true);
    const [isSectionLoading, setIsSectionLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)(false);

    // Nuovo stato per le opzioni globali
    const [globalOptions, setGlobalOptions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)(null);
    const [isLoadingOptions, setIsLoadingOptions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useState)(true);

    // Funzione per verificare se è un blocco nuovo
    const isNewBlock = () => {
      // Un blocco è considerato nuovo se ha i valori predefiniti dal block.json
      return attributes.displayType === _block_json__WEBPACK_IMPORTED_MODULE_11__.attributes.displayType["default"] && attributes.titleColor === _block_json__WEBPACK_IMPORTED_MODULE_11__.attributes.titleColor["default"] && attributes.sectionTitleColor === _block_json__WEBPACK_IMPORTED_MODULE_11__.attributes.sectionTitleColor["default"] && attributes.priceColor === _block_json__WEBPACK_IMPORTED_MODULE_11__.attributes.priceColor["default"] && attributes.descriptionColor === _block_json__WEBPACK_IMPORTED_MODULE_11__.attributes.descriptionColor["default"] && attributes.backgroundColor === _block_json__WEBPACK_IMPORTED_MODULE_11__.attributes.backgroundColor["default"] && attributes.borderColor === _block_json__WEBPACK_IMPORTED_MODULE_11__.attributes.borderColor["default"] && attributes.borderRadius === _block_json__WEBPACK_IMPORTED_MODULE_11__.attributes.borderRadius["default"] &&
      // Verificando pochi attributi chiave è sufficiente per determinare se è un blocco nuovo
      true;
    };

    // Funzione per applicare le impostazioni predefinite dalle opzioni globali
    const applyGlobalDefaults = options => {
      if (!options) return;
      const {
        preset_details,
        default_layout
      } = options;

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
    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useEffect)(() => {
      setIsLoadingOptions(true);
      _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8___default()({
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
    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useEffect)(() => {
      setIsMenuLoading(true);
      _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8___default()({
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
    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.useEffect)(() => {
      if (!attributes.menu_id) {
        setSections([]);
        return;
      }
      setIsSectionLoading(true);
      _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_8___default()({
        path: `/easy-restaurant-menu/v1/menus/${attributes.menu_id}/sections`
      }).then(data => {
        setSections(data || []);
        setIsSectionLoading(false);

        // Solo se showAllSections è false, selezioniamo la prima sezione di default
        if (!attributes.showAllSections && !attributes.section_id && data && data.length > 0) {
          setAttributes({
            section_id: data[0].id.toString()
          });
        } else if (!attributes.showAllSections && attributes.section_id) {
          // Verifica se la sezione selezionata esiste nel nuovo menu
          const sectionExists = data.some(section => section.id.toString() === attributes.section_id);
          if (!sectionExists && data.length > 0) {
            setAttributes({
              section_id: data[0].id.toString()
            });
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
        contentPadding: getDefaultSpacing({
          top: 15,
          right: 15,
          bottom: 15,
          left: 15
        })
      });
    };

    // Quando si cambia l'opzione di visualizzare tutte le sezioni
    const handleShowAllSectionsChange = value => {
      setAttributes({
        showAllSections: value
      });

      // Se si passa a visualizzare tutte le sezioni, reset della sezione selezionata
      if (value) {
        setAttributes({
          section_id: ''
        });
      } else if (sections && sections.length > 0) {
        // Se si passa a visualizzare una sola sezione, seleziona la prima se non ce n'è una già selezionata
        if (!attributes.section_id) {
          setAttributes({
            section_id: sections[0].id.toString()
          });
        }
      }
    };

    // Prepara le opzioni per il SelectControl dei menu
    const menuOptions = [];
    if (isMenuLoading) {
      menuOptions.push({
        value: '',
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Caricamento menu...', 'easy-restaurant-menu')
      });
    } else if (menus && menus.length > 0) {
      menuOptions.push({
        value: '',
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Seleziona un menu', 'easy-restaurant-menu')
      });
      menus.forEach(menu => {
        menuOptions.push({
          value: menu.id.toString(),
          label: menu.nome
        });
      });
    } else {
      menuOptions.push({
        value: '',
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Nessun menu disponibile', 'easy-restaurant-menu')
      });
    }

    // Prepara le opzioni per il SelectControl delle sezioni
    const sectionOptions = [];
    if (!attributes.menu_id) {
      sectionOptions.push({
        value: '',
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Prima seleziona un menu', 'easy-restaurant-menu')
      });
    } else if (isSectionLoading) {
      sectionOptions.push({
        value: '',
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Caricamento sezioni...', 'easy-restaurant-menu')
      });
    } else if (sections && sections.length > 0) {
      sectionOptions.push({
        value: '',
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Seleziona una sezione', 'easy-restaurant-menu')
      });
      sections.forEach(section => {
        sectionOptions.push({
          value: section.id.toString(),
          label: section.nome
        });
      });
    } else {
      sectionOptions.push({
        value: '',
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Nessuna sezione disponibile in questo menu', 'easy-restaurant-menu')
      });
    }

    // Opzioni per il tipo di visualizzazione
    const displayOptions = [{
      value: 'grid',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Griglia', 'easy-restaurant-menu')
    }, {
      value: 'list',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Lista', 'easy-restaurant-menu')
    }];

    // Opzioni per l'effetto hover
    const hoverOptions = [{
      value: 'none',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Nessuno', 'easy-restaurant-menu')
    }, {
      value: 'zoom',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Zoom', 'easy-restaurant-menu')
    }, {
      value: 'shadow',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Ombra', 'easy-restaurant-menu')
    }, {
      value: 'border',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Bordo', 'easy-restaurant-menu')
    }];

    // Opzioni per l'allineamento dell'immagine nella lista
    const alignmentOptions = [{
      value: 'center',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Allineamento centrato', 'easy-restaurant-menu')
    }, {
      value: 'top',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Allineamento in alto', 'easy-restaurant-menu')
    }];

    // Opzioni per l'allineamento del testo
    const textAlignmentOptions = [{
      value: 'left',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Sinistra', 'easy-restaurant-menu')
    }, {
      value: 'center',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Centro', 'easy-restaurant-menu')
    }, {
      value: 'right',
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Destra', 'easy-restaurant-menu')
    }];

    // Presets di spaziatura
    const applySpacingPreset = preset => {
      switch (preset) {
        case 'compact':
          setAttributes({
            imageMargin: {
              top: 0,
              right: 0,
              bottom: 10,
              left: 0
            },
            imagePadding: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 0
            },
            titleMargin: {
              top: 0,
              right: 0,
              bottom: 5,
              left: 0
            },
            priceMargin: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 10
            },
            descriptionMargin: {
              top: 5,
              right: 0,
              bottom: 0,
              left: 0
            },
            contentPadding: {
              top: 10,
              right: 10,
              bottom: 10,
              left: 10
            }
          });
          break;
        case 'normal':
          setAttributes({
            imageMargin: {
              top: 0,
              right: 0,
              bottom: 15,
              left: 0
            },
            imagePadding: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 0
            },
            titleMargin: {
              top: 0,
              right: 0,
              bottom: 10,
              left: 0
            },
            priceMargin: {
              top: 0,
              right: 0,
              bottom: 5,
              left: 15
            },
            descriptionMargin: {
              top: 10,
              right: 0,
              bottom: 0,
              left: 0
            },
            contentPadding: {
              top: 15,
              right: 15,
              bottom: 15,
              left: 15
            }
          });
          break;
        case 'spacious':
          setAttributes({
            imageMargin: {
              top: 0,
              right: 0,
              bottom: 20,
              left: 0
            },
            imagePadding: {
              top: 5,
              right: 5,
              bottom: 5,
              left: 5
            },
            titleMargin: {
              top: 0,
              right: 0,
              bottom: 15,
              left: 0
            },
            priceMargin: {
              top: 0,
              right: 0,
              bottom: 10,
              left: 20
            },
            descriptionMargin: {
              top: 15,
              right: 0,
              bottom: 0,
              left: 0
            },
            contentPadding: {
              top: 20,
              right: 20,
              bottom: 20,
              left: 20
            }
          });
          break;
      }
    };
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Impostazioni Generali', 'easy-restaurant-menu'),
      initialOpen: true
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.SelectControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Menu', 'easy-restaurant-menu'),
      value: attributes.menu_id,
      options: menuOptions,
      onChange: value => setAttributes({
        menu_id: value,
        section_id: ''
      })
    }), attributes.menu_id && sections && sections.length > 0 && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra tutte le sezioni', 'easy-restaurant-menu'),
      checked: attributes.showAllSections,
      onChange: handleShowAllSectionsChange,
      help: attributes.showAllSections ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Visualizza tutte le sezioni del menu', 'easy-restaurant-menu') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Visualizza solo una sezione specifica', 'easy-restaurant-menu')
    }), !attributes.showAllSections && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.SelectControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Sezione Menu', 'easy-restaurant-menu'),
      value: attributes.section_id,
      options: sectionOptions,
      onChange: value => setAttributes({
        section_id: value
      }),
      disabled: !attributes.menu_id || isSectionLoading
    })), attributes.menu_id && sections && sections.length === 0 && !isSectionLoading && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Notice, {
      status: "warning",
      isDismissible: false
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Questo menu non ha sezioni. Aggiungine almeno una nell\'area amministrativa.', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.SelectControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Tipo di visualizzazione', 'easy-restaurant-menu'),
      value: attributes.displayType,
      options: displayOptions,
      onChange: value => setAttributes({
        displayType: value
      })
    }), attributes.displayType === 'grid' && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Numero di colonne', 'easy-restaurant-menu'),
      value: attributes.columns,
      onChange: value => setAttributes({
        columns: value
      }),
      min: 1,
      max: 4
    }), attributes.displayType === 'list' && attributes.showImages && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RadioControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Allineamento immagine-testo', 'easy-restaurant-menu'),
      selected: attributes.listImageAlignment,
      options: alignmentOptions,
      onChange: value => setAttributes({
        listImageAlignment: value
      }),
      help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Sceglie come allineare verticalmente il testo rispetto all\'immagine nella vista lista', 'easy-restaurant-menu')
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra immagini', 'easy-restaurant-menu'),
      checked: attributes.showImages,
      onChange: value => setAttributes({
        showImages: value
      })
    }), attributes.showImages && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Immagini quadrate', 'easy-restaurant-menu'),
      checked: attributes.imageSquare,
      onChange: value => setAttributes({
        imageSquare: value
      }),
      help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Se disattivato, le immagini manterranno il loro rapporto originale', 'easy-restaurant-menu')
    }), attributes.displayType === 'grid' && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Altezza immagini griglia (px)', 'easy-restaurant-menu'),
      value: attributes.imageSizeGrid,
      onChange: value => setAttributes({
        imageSizeGrid: value
      }),
      min: 100,
      max: 400,
      step: 10
    }), attributes.displayType === 'list' && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Dimensione immagini lista (px)', 'easy-restaurant-menu'),
      value: attributes.imageSizeList,
      onChange: value => setAttributes({
        imageSizeList: value
      }),
      min: 60,
      max: 200,
      step: 10
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra prezzi', 'easy-restaurant-menu'),
      checked: attributes.showPrices,
      onChange: value => setAttributes({
        showPrices: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra descrizioni elementi', 'easy-restaurant-menu'),
      checked: attributes.showDescriptions,
      onChange: value => setAttributes({
        showDescriptions: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra descrizione menu', 'easy-restaurant-menu'),
      checked: attributes.showMenuDescription,
      onChange: value => setAttributes({
        showMenuDescription: value
      }),
      help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra/nascondi la descrizione del menu completo', 'easy-restaurant-menu')
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra descrizioni sezioni', 'easy-restaurant-menu'),
      checked: attributes.showSectionDescriptions,
      onChange: value => setAttributes({
        showSectionDescriptions: value
      }),
      help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra/nascondi le descrizioni delle sezioni', 'easy-restaurant-menu')
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra titolo menu', 'easy-restaurant-menu'),
      checked: attributes.showMenuTitle,
      onChange: value => setAttributes({
        showMenuTitle: value
      }),
      help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Mostra/nascondi il titolo principale del menu', 'easy-restaurant-menu')
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Stile e Colori', 'easy-restaurant-menu'),
      initialOpen: false
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Colore titoli elementi', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.ColorPalette, {
      value: attributes.titleColor,
      onChange: value => setAttributes({
        titleColor: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Dimensione titoli elementi (px)', 'easy-restaurant-menu'),
      value: attributes.titleFontSize,
      onChange: value => setAttributes({
        titleFontSize: value
      }),
      min: 12,
      max: 36,
      step: 1
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Colore titoli sezioni', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.ColorPalette, {
      value: attributes.sectionTitleColor,
      onChange: value => setAttributes({
        sectionTitleColor: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Colore linea sotto i titoli sezioni', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.ColorPalette, {
      value: attributes.sectionTitleUnderlineColor,
      onChange: value => setAttributes({
        sectionTitleUnderlineColor: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Colore prezzi', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.ColorPalette, {
      value: attributes.priceColor,
      onChange: value => setAttributes({
        priceColor: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Dimensione prezzi (px)', 'easy-restaurant-menu'),
      value: attributes.priceFontSize,
      onChange: value => setAttributes({
        priceFontSize: value
      }),
      min: 12,
      max: 36,
      step: 1
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Colore descrizioni', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.ColorPalette, {
      value: attributes.descriptionColor,
      onChange: value => setAttributes({
        descriptionColor: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Dimensione descrizioni (px)', 'easy-restaurant-menu'),
      value: attributes.descriptionFontSize,
      onChange: value => setAttributes({
        descriptionFontSize: value
      }),
      min: 10,
      max: 24,
      step: 1
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Colore sfondo elementi', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.ColorPalette, {
      value: attributes.backgroundColor,
      onChange: value => setAttributes({
        backgroundColor: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Spaziatura tra elementi (px)', 'easy-restaurant-menu'),
      value: attributes.itemSpacing,
      onChange: value => setAttributes({
        itemSpacing: value
      }),
      min: 0,
      max: 50
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Bordi e Effetti', 'easy-restaurant-menu'),
      initialOpen: false
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Raggio bordo (px)', 'easy-restaurant-menu'),
      value: attributes.borderRadius,
      onChange: value => setAttributes({
        borderRadius: value
      }),
      min: 0,
      max: 20
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Spessore bordo (px)', 'easy-restaurant-menu'),
      value: attributes.borderWidth,
      onChange: value => setAttributes({
        borderWidth: value
      }),
      min: 0,
      max: 10
    }), attributes.borderWidth > 0 && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Colore bordo', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.ColorPalette, {
      value: attributes.borderColor,
      onChange: value => setAttributes({
        borderColor: value
      })
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Ombra elemento', 'easy-restaurant-menu'),
      checked: attributes.boxShadow,
      onChange: value => setAttributes({
        boxShadow: value
      })
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RadioControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Effetto hover', 'easy-restaurant-menu'),
      selected: attributes.hoverEffect,
      options: hoverOptions,
      onChange: value => setAttributes({
        hoverEffect: value
      })
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Spaziatura Elementi', 'easy-restaurant-menu'),
      initialOpen: false
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      className: "erm-spacing-intro"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Personalizza la spaziatura degli elementi del menu per ottenere il layout desiderato.', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Flex, {
      direction: "row",
      justify: "space-between",
      align: "center",
      style: {
        marginBottom: '15px'
      }
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.FlexItem, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Preset rapidi', 'easy-restaurant-menu')))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.FlexItem, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      isSecondary: true,
      isSmall: true,
      onClick: () => applySpacingPreset('compact')
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Compatto', 'easy-restaurant-menu'))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.FlexItem, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      isSecondary: true,
      isSmall: true,
      onClick: () => applySpacingPreset('normal')
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Normale', 'easy-restaurant-menu'))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.FlexItem, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      isSecondary: true,
      isSmall: true,
      onClick: () => applySpacingPreset('spacious')
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Ampio', 'easy-restaurant-menu')))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      style: {
        textAlign: 'right',
        marginBottom: '20px'
      }
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      isDestructive: true,
      isSmall: true,
      onClick: resetAllSpacing
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Reimposta tutte le spaziature', 'easy-restaurant-menu'))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Contenuto', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BoxCtrl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Padding contenuto', 'easy-restaurant-menu'),
      values: attributes.contentPadding,
      onChange: value => setAttributes({
        contentPadding: value
      }),
      units: [{
        value: 'px',
        label: 'px',
        default: 15
      }],
      allowReset: true,
      resetValues: getDefaultSpacing({
        top: 15,
        right: 15,
        bottom: 15,
        left: 15
      })
    }), attributes.showImages && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Immagine', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BoxCtrl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Margin immagine', 'easy-restaurant-menu'),
      values: attributes.imageMargin,
      onChange: value => setAttributes({
        imageMargin: value
      }),
      units: [{
        value: 'px',
        label: 'px',
        default: 0
      }],
      allowReset: true,
      resetValues: getDefaultSpacing()
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BoxCtrl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Padding immagine', 'easy-restaurant-menu'),
      values: attributes.imagePadding,
      onChange: value => setAttributes({
        imagePadding: value
      }),
      units: [{
        value: 'px',
        label: 'px',
        default: 0
      }],
      allowReset: true,
      resetValues: getDefaultSpacing()
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Testo', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BoxCtrl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Margin titolo', 'easy-restaurant-menu'),
      values: attributes.titleMargin,
      onChange: value => setAttributes({
        titleMargin: value
      }),
      units: [{
        value: 'px',
        label: 'px',
        default: 0
      }],
      allowReset: true,
      resetValues: getDefaultSpacing()
    }), attributes.showPrices && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BoxCtrl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Margin prezzo', 'easy-restaurant-menu'),
      values: attributes.priceMargin,
      onChange: value => setAttributes({
        priceMargin: value
      }),
      units: [{
        value: 'px',
        label: 'px',
        default: 0
      }],
      allowReset: true,
      resetValues: getDefaultSpacing()
    }), attributes.showDescriptions && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BoxCtrl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Margin descrizione', 'easy-restaurant-menu'),
      values: attributes.descriptionMargin,
      onChange: value => setAttributes({
        descriptionMargin: value
      }),
      units: [{
        value: 'px',
        label: 'px',
        default: 0
      }],
      allowReset: true,
      resetValues: getDefaultSpacing()
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Allineamento Testi', 'easy-restaurant-menu'),
      initialOpen: false
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Allineamento titolo menu', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ButtonGroup, null, textAlignmentOptions.map(option => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      key: option.value,
      isPrimary: attributes.menuTitleAlignment === option.value,
      isSecondary: attributes.menuTitleAlignment !== option.value,
      onClick: () => setAttributes({
        menuTitleAlignment: option.value
      })
    }, option.label))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      style: {
        marginTop: '15px'
      }
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Allineamento titoli sezioni', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ButtonGroup, null, textAlignmentOptions.map(option => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      key: option.value,
      isPrimary: attributes.sectionTitleAlignment === option.value,
      isSecondary: attributes.sectionTitleAlignment !== option.value,
      onClick: () => setAttributes({
        sectionTitleAlignment: option.value
      })
    }, option.label))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      style: {
        marginTop: '15px'
      }
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Allineamento descrizione menu', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ButtonGroup, null, textAlignmentOptions.map(option => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      key: option.value,
      isPrimary: attributes.menuDescriptionAlignment === option.value,
      isSecondary: attributes.menuDescriptionAlignment !== option.value,
      onClick: () => setAttributes({
        menuDescriptionAlignment: option.value
      })
    }, option.label))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      style: {
        marginTop: '15px'
      }
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Allineamento descrizioni sezioni', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ButtonGroup, null, textAlignmentOptions.map(option => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      key: option.value,
      isPrimary: attributes.sectionDescriptionAlignment === option.value,
      isSecondary: attributes.sectionDescriptionAlignment !== option.value,
      onClick: () => setAttributes({
        sectionDescriptionAlignment: option.value
      })
    }, option.label)))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
      title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Impostazioni Globali', 'easy-restaurant-menu'),
      initialOpen: false
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      style: {
        marginBottom: '15px'
      }
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Applica le impostazioni globali del plugin a questo blocco.', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Button, {
      isPrimary: true,
      onClick: () => {
        if (globalOptions) {
          applyGlobalDefaults(globalOptions);
        }
      },
      disabled: isLoadingOptions,
      style: {
        width: '100%',
        justifyContent: 'center',
        marginTop: '10px'
      }
    }, isLoadingOptions ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Caricamento...', 'easy-restaurant-menu') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Applica Impostazioni Globali', 'easy-restaurant-menu'))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Notice, {
      status: "info",
      isDismissible: false
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Questo sovrascriverà le impostazioni di colore, allineamento e layout attuali del blocco.', 'easy-restaurant-menu')))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      ...blockProps
    }, !attributes.menu_id ? (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "erm-editor-placeholder"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "erm-editor-placeholder-icon dashicons dashicons-food"
    }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "erm-editor-placeholder-title"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Menu Ristorante', 'easy-restaurant-menu')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "erm-editor-placeholder-description"
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Seleziona un menu dal pannello laterale per iniziare.', 'easy-restaurant-menu'))) : (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "erm-editor-preview"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("style", null, `
                                .erm-item-title {
                                    font-size: ${attributes.titleFontSize}px !important;
                                }
                                .erm-item-price {
                                    font-size: ${attributes.priceFontSize}px !important;
                                }
                                .erm-item-description {
                                    font-size: ${attributes.descriptionFontSize}px !important;
                                }
                                `), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_7___default()), {
      block: "easy-restaurant-menu/restaurant-menu",
      attributes: attributes
    }))));
  },
  save: function () {
    // Il rendering viene gestito lato server
    return null;
  }
});

/***/ }),

/***/ "./src/restaurant-menu/editor.scss":
/*!*****************************************!*\
  !*** ./src/restaurant-menu/editor.scss ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/restaurant-menu/style.scss":
/*!****************************************!*\
  !*** ./src/restaurant-menu/style.scss ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ ((module) => {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "./src/restaurant-menu/block.json":
/*!****************************************!*\
  !*** ./src/restaurant-menu/block.json ***!
  \****************************************/
/***/ ((module) => {

module.exports = JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"easy-restaurant-menu/restaurant-menu","version":"1.0.0","title":"Menu Ristorante","category":"widgets","icon":"food","description":"Blocco per visualizzare il menu del ristorante con varie opzioni di personalizzazione","supports":{"html":false,"align":["wide","full"],"color":{"background":true,"text":true,"link":true,"gradients":true},"spacing":{"margin":true,"padding":true},"typography":{"fontSize":true,"lineHeight":true,"fontWeight":true,"fontStyle":true,"fontFamily":true}},"attributes":{"menu_id":{"type":"string","default":""},"section_id":{"type":"string","default":""},"showAllSections":{"type":"boolean","default":true},"showMenuDescription":{"type":"boolean","default":true},"showSectionDescriptions":{"type":"boolean","default":true},"showMenuTitle":{"type":"boolean","default":true},"menuTitleAlignment":{"type":"string","default":"center"},"sectionTitleAlignment":{"type":"string","default":"center"},"menuDescriptionAlignment":{"type":"string","default":"center"},"sectionDescriptionAlignment":{"type":"string","default":"center"},"displayType":{"type":"string","default":"grid"},"columns":{"type":"number","default":2},"showImages":{"type":"boolean","default":true},"showPrices":{"type":"boolean","default":true},"showDescriptions":{"type":"boolean","default":true},"imageSizeGrid":{"type":"number","default":200},"imageSizeList":{"type":"number","default":90},"imageSquare":{"type":"boolean","default":true},"listImageAlignment":{"type":"string","default":"center"},"imageSize":{"type":"number","default":100},"imageMargin":{"type":"object","default":{"top":0,"right":0,"bottom":0,"left":0}},"imagePadding":{"type":"object","default":{"top":0,"right":0,"bottom":0,"left":0}},"titleMargin":{"type":"object","default":{"top":0,"right":0,"bottom":0,"left":0}},"priceMargin":{"type":"object","default":{"top":0,"right":0,"bottom":0,"left":0}},"descriptionMargin":{"type":"object","default":{"top":0,"right":0,"bottom":0,"left":0}},"contentPadding":{"type":"object","default":{"top":15,"right":15,"bottom":15,"left":15}},"priceColor":{"type":"string","default":""},"titleColor":{"type":"string","default":""},"sectionTitleColor":{"type":"string","default":""},"sectionTitleUnderlineColor":{"type":"string","default":""},"descriptionColor":{"type":"string","default":""},"backgroundColor":{"type":"string","default":""},"itemSpacing":{"type":"number","default":20},"borderRadius":{"type":"number","default":0},"borderWidth":{"type":"number","default":0},"borderColor":{"type":"string","default":""},"boxShadow":{"type":"boolean","default":false},"hoverEffect":{"type":"string","default":"none"},"titleFontSize":{"type":"number","default":16},"descriptionFontSize":{"type":"number","default":14},"priceFontSize":{"type":"number","default":16}},"textdomain":"easy-restaurant-menu","editorScript":"file:./index.js","editorStyle":"file:./editor.scss","style":"file:./style.scss","render":"file:./render.php"}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"restaurant-menu/index": 0,
/******/ 			"restaurant-menu/style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = globalThis["webpackChunkeasy_restaurant_menu"] = globalThis["webpackChunkeasy_restaurant_menu"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["restaurant-menu/style-index"], () => (__webpack_require__("./src/restaurant-menu/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map