<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Brinkert.' . $_EXTKEY,
    'Quickgooglemap',

    [
        'Map' => 'index',
    ],

    // non-cacheable actions
    []
);
