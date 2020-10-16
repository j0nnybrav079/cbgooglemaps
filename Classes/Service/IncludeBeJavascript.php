<?php

namespace Brinkert\Cbgooglemaps\Service;

/**
 * Class with methods to extend flexforms with user fields
 *
 * @package				includeBeJavascript
 * @path 				includeBeJavascript.php
 * @version				4.0: includeBeJavascript.php,  02.07.2018
 * @copyright 			(c)2011-2018 Christian Brinkert
 * @author 				Christian Brinkert <christian.brinkert@googlemail.com>
 */

class IncludeBeJavascript{

    protected $configurationManager = null;
    protected $settings = null;
    /**
     * @var \TYPO3\CMS\Backend\Template\DocumentTemplate $doc
     */
    protected $doc = null;

    /**
     * @var \TYPO3\CMS\Backend\Template\DocumentTemplate $doc
     */
    protected $pagerenderer = null;

    /**
     * @var string $filePath
     */
    protected $filePath = null;


    /**
     * Include javascripts and styles
     * @param $config
     * @return string
     */
    public function includeCbGoogleMapsJavascript($config){

        // fetch current extension typoscript configuration
        $sysPageObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
        $TSObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\TemplateService');
        $TSObj->tt_track = 0;
        $TSObj->init();
        $TSObj->runThroughTemplates($sysPageObj->getRootLine($config['row']['pid']));
        $TSObj->generateConfig();

        // set plugin ts
        $this->settings = $TSObj->setup['plugin.']['tx_cbgooglemaps.'];

        // get typo3 version
        (list($major,$minor,$patch) = explode('.',TYPO3_version));

        // set pagerenderer
        if (7 <= (int) $major)
            // TYPO3 Version 7 and higher
            $this->pagerenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);

        else {
            // TYPO3 Version: 6.2 or less ...
            $this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Backend\Template\DocumentTemplate');
            $this->pagerenderer = $this->doc->getPageRenderer();
        }

        // set relative extension path
        $this->filePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('cbgooglemaps');


        // add own scripts for gmaps object and mapping functions
        $this->pagerenderer->addJsFile('/'.$this->filePath.'Resources/Public/JavaScript/class.gmaps.js',
            'text/javascript', FALSE, FALSE, '', TRUE);
        $this->pagerenderer->addJsFile('/'.$this->filePath.'Resources/Public/JavaScript/class.geocoding.js',
            'text/javascript', FALSE, FALSE, '', TRUE);


        // add map provider specific libraries
        if ('Google' === $this->settings['settings.']['mapProvider']) {
            // build googlemaps library url with optional api key - if given
            $googleMapsUri = $this->settings['settings.']['googleapi.']['uri'];
            if ($this->settings['settings.']['googleapi.']['key'])
                $googleMapsUri .= '?key=' . $this->settings['settings.']['googleapi.']['key'];

            // add google libraries
            $this->pagerenderer->addJsFile($googleMapsUri,'text/javascript', FALSE, FALSE, '', TRUE);


        } else if ('MapBox' === $this->settings['settings.']['mapProvider']) {
            // add mapbox libraries
            $this->pagerenderer->addJsFile(
                '/'.$this->filePath.'Resources/Public/JavaScript/mapbox/mapbox-gl-patched.js',
                'text/javascript', FALSE, FALSE, '', TRUE);
            $this->pagerenderer->addCssFile(
                '/'.$this->filePath.'Resources/Public/JavaScript/mapbox/mapbox-gl.css',
                'stylesheet', 'all', '', FALSE, FALSE, '', TRUE);


        } else {
            // add openstreetmap libraries
            $this->pagerenderer->addJsFile(
                '/'.$this->filePath.'Resources/Public/JavaScript/leaflet/leaflet-src-patched.js',
                'text/javascript', FALSE, FALSE, '', TRUE);
            $this->pagerenderer->addCssFile(
                '/'.$this->filePath.'Resources/Public/JavaScript/leaflet/leaflet.css',
                'stylesheet', 'all', '', FALSE, FALSE, '', TRUE);

        }
    }

}

