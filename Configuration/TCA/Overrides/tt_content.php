<?php

// Prevent script from being called directly
defined('TYPO3') or die();

// encapsulate all locally defined variables
(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'cbgooglemaps',
        'Quickgooglemap',
        'Quick map integration',
        'EXT:cbgooglemaps/Resources/Public/Icons/ce_wiz.svg'
    );
    
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['cbgooglemaps_quickgooglemap'] = 'pi_flexform';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['cbgooglemaps_quickgooglemap'] = 'layout,select_key,pages,recursive';
    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'cbgooglemaps_quickgooglemap',
        'FILE:EXT:cbgooglemaps/Configuration/FlexForms/flexform_quickgooglemap.xml'
    );
})();
