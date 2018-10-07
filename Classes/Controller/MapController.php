<?php

namespace Brinkert\Cbgooglemaps\Controller;

/**
 * Class to extend the backend with a tca user field 
 * @package				Cbgooglemaps
 * @path 				Cbgooglemaps\Controller\MapController.php
 * @version				4.0: MapController.php,  02.07.2018
 * @copyright 			(c)2011-2018 Christian Brinkert
 * @author 				Christian Brinkert <christian.brinkert@googlemail.com>
 */

class MapController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     */
	protected $configurationManager;
	protected $ceData;
	protected $settings;
	protected $cobj;
	protected $filePath;


    /**
     * Inject configuration manager
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}



    /**
     * Do some global initialization
     */
	public function initializeAction() {
		// store content element data to local property
		$this->ceData = $this->configurationManager->getContentObject()->data;	

		// get extension typoscript
		$this->settings = $this->configurationManager->getConfiguration(
							\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
							'Cbgooglemaps',
							'Quickgooglemap');

		// set sitepath
		$this->filePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('cbgooglemaps');

		// set content object renderer
        $this->cobj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
	}



    /**
     * Create map content element to the frontend
     */
	public function indexAction()
	{
	    // add google or openstreetmap scripts/styles
        $this->addJsCss();

        // set map parameters
        $mapParameter = $this->getMapParameters();

        // assign mapStyling and settings
        $mapParameter['mapStyling'] = $this->getMapStyling();

        // assign map parameters to the view
        $this->view->assignMultiple($mapParameter);

	}



    /**
     * Build and return map parameter array, with defaults or ce specific values
     * @return array
     */
	private function getMapParameters(){

	    return [
            // assign uid of current content element
            'contentId' => ((
                             null != $this->ceData['uid']
                             ? $this->ceData['uid']
                             : rand(1,999999)
                           ) .'_'. $this->configurationManager->getContentObject()->parentRecord['data']['uid']),
            // map provider to build map: googleMaps or OpenStreetMap
            'mapProvider' => $this->settings['mapProvider'],
            // assign width and height of map
            'width'     => null != $this->ceData['width']
                           ? $this->ceData['width']
                           : (
                               0 < (int)$this->settings['cbgmMapWidth']
                               ? $this->settings['cbgmMapWidth']
                               : $this->settings['display']['width']
                           ),
            'height'    => null != $this->ceData['height']
                           ? $this->ceData['height']
                           : (
                            0 < (int)$this->settings['cbgmMapHeight']
                               ? $this->settings['cbgmMapHeight']
                               : $this->settings['display']['height']
                           ),
            // assign pin description text, to placed into info box
            'infoText'  => urlencode(
                             null != $this->ceData['infoText']
                             ? $this->ceData['infoText']
                             : (
                                isset($this->settings['cbgmDescription'])
                                ? $this->settings['cbgmDescription']
                                : $this->settings['infoText']
                             )
                           ),
            // assign auto open flag to the view
            'openInfoBox' =>  isset($this->settings['cbgmAutoOpen'])
                ? $this->settings['cbgmAutoOpen']
                : $this->settings['infoTextOpen'],
            // assign deactivation of zooming by mousewheel
            'useScrollwheel' => $this->settings['options']['useScrollwheel'],
            // assign location (longitude and latitude) to the view
            'latitude'  => null != $this->ceData['latitude']
                           ? (float) $this->ceData['latitude']
                           : (
                                isset($this->settings['cbgmLatitude'])
                                ? (float) $this->settings['cbgmLatitude']
                                : (float) $this->settings['latitude']
                           ),
            'longitude'  => null != $this->ceData['longitude']
                           ? (float) $this->ceData['longitude']
                           : (
                                isset($this->settings['cbgmLongitude'])
                                ? (float) $this->settings['cbgmLongitude']
                                : (float) $this->settings['longitude']
                           ),
            // assign map zoom level to the view ,if given value is valid
            'mapZoom'   => null != $this->ceData['zoom']
                           ? (int) $this->ceData['zoom']
                           : (
                                0 <= (int) $this->settings['cbgmScaleLevel'] && !empty($this->settings['cbgmScaleLevel'])
                                ? (int) $this->settings['cbgmScaleLevel']
                                : (int) $this->settings['display']['zoom']
                           ),
            // assign map type to the view, if given value is valid
            'mapType'   => null != $this->ceData['mapType'] && in_array( (string) $this->ceData['mapType'],
                                preg_split("/[\s]*[,][\s]*/", $this->settings['valid']['mapTypes']))
                           ? $this->ceData['mapType']
                           : (
                                in_array((string)$this->settings['cbgmMapType'],
                                    preg_split("/[\s]*[,][\s]*/", $this->settings['valid']['mapTypes']))
                                ? $this->settings['cbgmMapType']
                                : $this->settings['display']['mapType']
                           ),
            // assign navigation controls to the view
            'mapControl' => null != $this->ceData['navigationControl']
                                 && in_array((string) $this->ceData['navigationControl'],
                                    preg_split("/[\s]*[,][\s]*/", $this->settings['valid']['navigationControl']))
                            ? $this->ceData['navigationControl']
                            : (
                                 in_array((string)$this->settings['cbgmNavigationControl'],
                                     preg_split("/[\s]*[,][\s]*/", $this->settings['valid']['navigationControl']))
                                 ? $this->settings['cbgmNavigationControl']
                                 : $this->settings['display']['navigationControl']
                            ),
            // assign icon if given by constant or typoscript
            'icon'      => null != $this->ceData['icon'] && file_exists(PATH_site . $this->ceData['icon'])
                ? \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') .'/'. $this->ceData['icon']
                : (
                !empty($this->settings['display']['icon']) && file_exists(PATH_site . $this->settings['display']['icon'])
                    ? \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') .'/'. $this->settings['display']['icon']
                    : null
                ),
            // add map styling default
            'mapStyling'  => null,
            // some braces?
            'braceStart'  => '{',
            'braceEnd'    => '}'
        ];
    }



    /**
     * Fetch and return map styling from file if defined
     * @param array $mapParameter
     * @return mixed
     */
    private function getMapStyling(){

        if (null != $this->ceData['mapStyling']
            && file_exists(PATH_site . $this->ceData['mapStyling']) ){
            // assign map styling from content element

            $styling = file_get_contents(PATH_site. $this->ceData['mapStyling']);

            return !is_null(json_decode($styling)) ? $styling : null;

        } else if (!empty($this->settings['display']['mapStyling'])
            && file_exists(PATH_site . $this->settings['display']['mapStyling']) ) {
            // assign map styling from typoscript file definition

            $styling = file_get_contents(PATH_site . $this->settings['display']['mapStyling']);

            return !is_null(json_decode($styling)) ? $styling : null;

        } else if (!empty($this->settings['display']['mapStyling'])
            && !is_null(json_decode($this->settings['display']['mapStyling']))){
            // assign map styling from typoscript style definition

            return $this->settings['display']['mapStyling'];
        }

        return null;
    }



    /**
     * Add some javascripts and css styles to the view
     * @return void
     */
	private function addJsCss(){

        // add google or openstreet map scripts and styles to the view
        if ('Google' == $this->settings['mapProvider']) {

            // build google maps uri
            $googleMapsUri = preg_match('/^http/',$this->settings['googleapi']['uri'])
                           ? $this->settings['googleapi']['uri']
                           : '/'. $this->filePath . $this->settings['googleapi']['uri'];

            // add optional or required given key
            if (!empty($this->settings['googleapi']['key']))
                $googleMapsUri .= '?key='. $this->settings['googleapi']['key'];

            // add google api file
            $GLOBALS['TSFE']->additionalHeaderData['cbgooglemaps'] =
                '<script src="' . $googleMapsUri . '" type="text/javascript"></script>';


        } else if ('MapBox' == $this->settings['mapProvider']) {
            // add mapbox js and css files
            $mapboxJs  = preg_match('/^http/',$this->settings['mapboxapi']['js'])
                       ? $this->settings['mapboxapi']['js']
                       : '/'. $this->filePath . $this->settings['mapboxapi']['js'];

            $mapboxCss = preg_match('/^http/',$this->settings['mapboxapi']['css'])
                       ? $this->settings['mapboxapi']['css']
                       : '/'. $this->filePath . $this->settings['mapboxapi']['css'];

            $GLOBALS['TSFE']->additionalHeaderData['cbgooglemapsJs'] =
                '<script src="'. $mapboxJs .'" type="text/javascript"></script>';
            $GLOBALS['TSFE']->additionalHeaderData['cbgooglemapsCss'] =
                '<link href="'. $mapboxCss .'" type="text/css" rel="stylesheet" />';


        }else {
            // add leaflet js and css files
            $osmJs  = preg_match('/^http/',$this->settings['osmapi']['js'])
                ? $this->settings['osmapi']['js']
                : '/'. $this->filePath . $this->settings['osmapi']['js'];

            $osmCss = preg_match('/^http/',$this->settings['osmapi']['css'])
                ? $this->settings['osmapi']['css']
                : '/'. $this->filePath . $this->settings['osmapi']['css'];

            $GLOBALS['TSFE']->additionalHeaderData['cbgooglemapsJs'] =
                '<script src="'. $osmJs .'" type="text/javascript"></script>';
            $GLOBALS['TSFE']->additionalHeaderData['cbgooglemapsCss'] =
                '<link href="'. $osmCss .'" type="text/css" rel="stylesheet" />';

        }

    }

}