<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Brinkert.' . $_EXTKEY,
    'Quickgooglemap',

    [
        'Map' => 'index',
    ],

    // non-cacheable actions
    []
);
