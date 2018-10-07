<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Brinkert.' . $_EXTKEY,
	'Quickgooglemap',

	array(
		'Map' => 'index',
	),

	// non-cacheable actions
	array()
);

?>
