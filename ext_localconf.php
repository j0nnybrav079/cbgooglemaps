<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Brinkert.cbgooglemaps',
    'Quickgooglemap',
    [
        'Map' => 'index',
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

