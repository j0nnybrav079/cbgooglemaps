<?php

namespace Brinkert\Cbgooglemaps\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class JsLibrariesElement extends AbstractFormElement
{
    public function render()
    {
        /** @var ConfigurationManager $cm */
        $cm = GeneralUtility::makeInstance(ConfigurationManager::class);
        $ts = $cm->getConfiguration($cm::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
//        $filePath = explode('typo3conf', ExtensionManagementUtility::extPath('cbgooglemaps'))[1];
        $filePath = 'EXT:cbgooglemaps/';

//        die($filePath);



        $settings = $ts['plugin.']['tx_cbgooglemaps.']['settings.'];

        $pageRenderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        // add own scripts for gmaps object and mapping functions
        $pageRenderer->addJsFile($filePath . 'Resources/Public/JavaScript/class.gmaps.js',
            'text/javascript', FALSE, FALSE, '', FALSE);
        $pageRenderer->addJsFile($filePath . 'Resources/Public/JavaScript/class.geocoding.js',
            'text/javascript', FALSE, FALSE, '', FALSE);

        // add map provider specific libraries
        if ('Google' === $settings['mapProvider']) {
            // build googlemaps library url with optional api key - if given
            $googleMapsUri = $settings['googleapi.']['uri'];
            if ($settings['googleapi.']['key'])
                $googleMapsUri .= '?key=' . $settings['googleapi.']['key'];
            // add google libraries
            $pageRenderer->addJsFile($googleMapsUri, 'text/javascript', FALSE, FALSE, '', TRUE);
        } else if ('MapBox' === $settings['mapProvider']) {
            // add mapbox libraries
            $pageRenderer->addJsFile(
                $filePath . 'Resources/Public/JavaScript/mapbox/mapbox-gl-patched.js',
                'text/javascript', FALSE, FALSE, '', TRUE);
            $pageRenderer->addCssFile(
                $filePath . 'Resources/Public/JavaScript/mapbox/mapbox-gl.css',
                'stylesheet', 'all', '', FALSE, FALSE, '', TRUE);
        } else {
            // add openstreetmap libraries
            $pageRenderer->addJsFile(
                $filePath . 'Resources/Public/JavaScript/leaflet/leaflet-src-patched.js',
                'text/javascript', FALSE, FALSE, '', TRUE);
            $pageRenderer->addCssFile(
                $filePath . 'Resources/Public/JavaScript/leaflet/leaflet.css',
                'stylesheet', 'all', '', FALSE, FALSE, '', TRUE);
        }
    }
}