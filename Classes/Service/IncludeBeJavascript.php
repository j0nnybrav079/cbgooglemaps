<?php

namespace Brinkert\Cbgooglemaps\Service;

use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class with methods to extend flexforms with user fields
 *
 * @path 				includeBeJavascript.php
 * @version				4.0: includeBeJavascript.php,  02.07.2018
 * @copyright 			(c)2011-2018 Christian Brinkert
 * @author 				Christian Brinkert <christian.brinkert@googlemail.com>
 */
class IncludeBeJavascript
{
    protected $configurationManager = null;
    protected $settings = null;
    /**
     * @var DocumentTemplate $doc
     */
    protected $doc = null;

    /**
     * @var DocumentTemplate $doc
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
    public function includeCbGoogleMapsJavascript($config)
    {

        // fetch current extension typoscript configuration
        $sysPageObj = GeneralUtility::makeInstance(PageRepository::class);
        $TSObj = GeneralUtility::makeInstance(TemplateService::class);
        $TSObj->tt_track = 0;
        $TSObj->init();
        $TSObj->runThroughTemplates($sysPageObj->getRootLine($config['row']['pid']));
        $TSObj->generateConfig();

        // set plugin ts
        $this->settings = $TSObj->setup['plugin.']['tx_cbgooglemaps.'];

        // get typo3 version
        (list($major, $minor, $patch) = explode('.', TYPO3_version));

        // set pagerenderer
        if (7 <= (int) $major) {
            // TYPO3 Version 7 and higher
            $this->pagerenderer = GeneralUtility::makeInstance(PageRenderer::class);
        } else {
            // TYPO3 Version: 6.2 or less ...
            $this->doc = GeneralUtility::makeInstance(DocumentTemplate::class);
            $this->pagerenderer = $this->doc->getPageRenderer();
        }

        // set relative extension path
        $this->filePath = ExtensionManagementUtility::siteRelPath('cbgooglemaps');

        // add own scripts for gmaps object and mapping functions
        $this->pagerenderer->addJsFile(
            '/' . $this->filePath . 'Resources/Public/JavaScript/class.gmaps.js',
            'text/javascript',
            false,
            false,
            '',
            true
        );
        $this->pagerenderer->addJsFile(
            '/' . $this->filePath . 'Resources/Public/JavaScript/class.geocoding.js',
            'text/javascript',
            false,
            false,
            '',
            true
        );

        // add map provider specific libraries
        if ('Google' === $this->settings['settings.']['mapProvider']) {
            // build googlemaps library url with optional api key - if given
            $googleMapsUri = $this->settings['settings.']['googleapi.']['uri'];
            if ($this->settings['settings.']['googleapi.']['key']) {
                $googleMapsUri .= '?key=' . $this->settings['settings.']['googleapi.']['key'];
            }

            // add google libraries
            $this->pagerenderer->addJsFile($googleMapsUri, 'text/javascript', false, false, '', true);
        } elseif ('MapBox' === $this->settings['settings.']['mapProvider']) {
            // add mapbox libraries
            $this->pagerenderer->addJsFile(
                '/' . $this->filePath . 'Resources/Public/JavaScript/mapbox/mapbox-gl-patched.js',
                'text/javascript',
                false,
                false,
                '',
                true
            );
            $this->pagerenderer->addCssFile(
                '/' . $this->filePath . 'Resources/Public/JavaScript/mapbox/mapbox-gl.css',
                'stylesheet',
                'all',
                '',
                false,
                false,
                '',
                true
            );
        } else {
            // add openstreetmap libraries
            $this->pagerenderer->addJsFile(
                '/' . $this->filePath . 'Resources/Public/JavaScript/leaflet/leaflet-src-patched.js',
                'text/javascript',
                false,
                false,
                '',
                true
            );
            $this->pagerenderer->addCssFile(
                '/' . $this->filePath . 'Resources/Public/JavaScript/leaflet/leaflet.css',
                'stylesheet',
                'all',
                '',
                false,
                false,
                '',
                true
            );
        }
    }
}
