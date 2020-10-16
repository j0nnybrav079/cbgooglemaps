<?php 
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}


// register plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Brinkert.cbgooglemaps',
	'Quickgooglemap',
	'Quick map integration'
);


// register icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Imaging\IconRegistry::class
);

$iconRegistry->registerIcon(
    'ce-default-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:cbgooglemaps/Resources/Public/Icons/ce_wiz.svg']
);

// add plugin to the new content element list
$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['Brinkert\Cbgooglemaps\Utility\Hook\ContentElementWizard'] =
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cbgooglemaps') .
    'Classes/Utility/Hook/ContentElementWizard.php';


// add static typoscripts
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
		'cbgooglemaps', 'Configuration/TypoScript', 'Quick Maps');


// set plugin signature
$pluginSignature = str_replace('_','','cbgooglemaps') . '_quickgooglemap';
// add flexform fields to the list
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
// define flexform file
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
		$pluginSignature, 'FILE:EXT:' . 'cbgooglemaps' . '/Configuration/FlexForms/flexform_quickgooglemap.xml');


// exclude some default fields, like: layout, select_key, pages and recursive
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';


