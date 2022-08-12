<?php

// Prevent script from being called directly
defined('TYPO3') or die();

// encapsulate all locally defined variables
(static function() {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:cbgooglemaps/Configuration/TsConfig/ContentElementWizard.tsconfig">'
    );
    
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    
    $iconRegistry->registerIcon(
        'ce-default-icon',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:cbgooglemaps/Resources/Public/Icons/ce_wiz.svg']
    );
    
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'cbgooglemaps',
        'Quickgooglemap',
        [
            \Brinkert\Cbgooglemaps\Controller\MapController::class => 'index',
        ],
    
        // non-cacheable actions
        []
    );
    
    $tStamp = (new Datetime("now"))->getTimestamp();
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][$tStamp] = [
        'nodeName' => 'previewButtonElement',
        'priority' => 40,
        'class' => \Brinkert\Cbgooglemaps\Form\Element\PreviewButtonElement::class,
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][($tStamp + 1)] = [
        'nodeName' => 'jsLibrariesElement',
        'priority' => 40,
        'class' => \Brinkert\Cbgooglemaps\Form\Element\JsLibrariesElement::class,
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][($tStamp + 2)] = [
        'nodeName' => 'geoCodingButtonElement',
        'priority' => 40,
        'class' => \Brinkert\Cbgooglemaps\Form\Element\GeoCodingButtonElement::class,
    ];
})();
