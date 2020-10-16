<?php

namespace Brinkert\Cbgooglemaps\Service;

/**
 * Class with methods to extend flexforms with user fields
 * @package				addFlexformFields
 * @path 				addFlexformFields.php
 * @version				4.0: addFlexformFields.php,  02.07.2018
 * @copyright 			(c)2011-2018 Christian Brinkert
 * @author 				Christian Brinkert <christian.brinkert@googlemail.com>
 */

class AddFlexformFields
{
    protected $conf;


    /**
     * Create buttons to geocode given address data and a button to display the location
     * @param $config
     * @return string
     */
    public function addGeocodingButton($config)
    {
        if (null === $this->conf)
            $this->getExtensionConfiguration($config['row']['pid']);

        // try to fetch current be_user language and localizations
        if ($GLOBALS && $GLOBALS['BE_USER'] && $GLOBALS['BE_USER']->uc && $GLOBALS['BE_USER']->uc['lang'])
            $btnLabels = $this->conf['_LOCAL_LANG.'][$GLOBALS['BE_USER']->uc['lang'] .'.'];
        else
            $btnLabels = $this->conf['_LOCAL_LANG.']['default.'];


        $fiedset =  '<div class="cbgm_geocoding">';
        $fiedset .= '<input type="button" onclick="cbGooglemaps.doGeocoding(\''.$config['row']['uid'].'\',\''
                 .  $this->conf['settings.']['mapProvider'] .'\',\''
                 .  $this->conf['settings.']['mapboxapi.']['accessToken'] .'\')" value="'
                 .  $btnLabels['btnGeocoding'] .'">';
        $fiedset .= '<input type="button" onclick="cbGooglemaps.displayLocation(\''.$config['row']['uid'].'\',\''
                 .  $this->conf['settings.']['mapProvider'] .'\',\''
                 .  $this->conf['settings.']['mapboxapi.']['accessToken'] .'\')" value="'
                 .  $btnLabels['btnDisplayMap'] .'">';
        $fiedset .= '<div id="cbgm_previewLocation"></div>';
        $fiedset .= '</div>';

        return $fiedset;
    }


    /**
     * Create button for map preview in the backend
     * @param $config
     * @return string
     */
    public function addPreviewButton($config)
    {
        if (null === $this->conf)
            $this->getExtensionConfiguration($config['row']['pid']);

        // try to fetch current be_user language and localizations
        if ($GLOBALS && $GLOBALS['BE_USER'] && $GLOBALS['BE_USER']->uc && $GLOBALS['BE_USER']->uc['lang'])
            $btnLabels = $this->conf['_LOCAL_LANG.'][$GLOBALS['BE_USER']->uc['lang'] .'.'];
        else
            $btnLabels = $this->conf['_LOCAL_LANG.']['default.'];


        $fiedset =  '<div class="cbgm_preview">';
        $fiedset .= '<input type="button" onclick="cbGooglemaps.displayPreview(\''
                  . $config['row']['uid'].'\',\''
                  . $this->conf['settings.']['display.']['zoom'].'\',\''
                  . $this->conf['settings.']['display.']['mapType'].'\',\''
                  . $this->conf['settings.']['display.']['navigationControl'].'\',\''
                  . $this->conf['settings.']['mapProvider'] .'\',\''
                  . $this->conf['settings.']['mapboxapi.']['accessToken'] .'\')" value="'
                  . $btnLabels['btnPreviewMap'] .'">';
        $fiedset .= '<div id="cbgm_previewMap"></div>';
        $fiedset .= '</div>';

        return $fiedset;
    }



    /**
     * Get current typoscript configuration from extension
     * @param integer $pid
     */
    private function getExtensionConfiguration($pid)
    {
        /** instantiate TYPO3\CMS\Frontend\Page\PageRepository object
         * @var \TYPO3\CMS\Frontend\Page\PageRepository $sysPageObj
         */
        $sysPageObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');
        // get rootline from current page ($config['row']['pid'])
        $rootLine = $sysPageObj->getRootLine($pid);
        // instantiate TYPO3\CMS\Core\TypoScript\ExtendedTemplateService object
        /**
         * @var \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $TSObj
         */
        $TSObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\TypoScript\ExtendedTemplateService');
        // set top top level of rootline, deactivate tt-timeobject logging,
        // initialize object, run through templates und create typoscript configuration tree
        $TSObj->tt_track = 0;
        $TSObj->init();
        $TSObj->runThroughTemplates($rootLine);
        $TSObj->generateConfig();

        // select requested typoscript from own extension
        if ($TSObj->setup['plugin.']['tx_cbgooglemaps.'])
            $this->conf = $TSObj->setup['plugin.']['tx_cbgooglemaps.'];
    }
}

?>