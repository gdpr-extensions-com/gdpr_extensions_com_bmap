<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'GdprExtensionsComBmap',
        'gdprbingmap',
        [
            \GdprExtensionsCom\GdprExtensionsComBmap\Controller\GdprBingMapController::class => 'index'
        ],
        // non-cacheable actions
        [
            \GdprExtensionsCom\GdprExtensionsComBmap\Controller\GdprBingMapController::class => '',
            \GdprExtensionsCom\GdprExtensionsComBmap\Controller\GdprManagerController::class => 'create, update, delete'
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

     // wizards
     \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod.wizards.newContentElement.wizardItems {
               gdpr.header = LLL:EXT:gdpr_extensions_com_bmap/Resources/Private/Language/locallang_db.xlf:tx_gdpr_extensions_com_bmap_gdprgoogle_bingmap.name.tab
        }'
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.gdpr {
                elements {
                    gdprbingmap {
                        iconIdentifier = gdpr_extensions_com_bing_map_logo
                        title = LLL:EXT:gdpr_extensions_com_bmap/Resources/Private/Language/locallang_db.xlf:tx_gdpr_extensions_com_bmap_gdprbingmap.name
                        description = LLL:EXT:gdpr_extensions_com_bmap/Resources/Private/Language/locallang_db.xlf:tx_gdpr_extensions_com_bmap_gdprbingmap.description
                        tt_content_defValues {
                            CType = gdprextensionscombmap_gdprbingmap
                        }
                    }
                }
                show = *
            }
       }'
    );
})();
