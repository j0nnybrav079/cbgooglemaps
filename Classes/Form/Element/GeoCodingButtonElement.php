<?php

namespace Brinkert\Cbgooglemaps\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class GeoCodingButtonElement extends AbstractFormElement
{

    public function render()
    {
        /** @var ConfigurationManager $cm */
        $cm = GeneralUtility::makeInstance(ConfigurationManager::class);
        $ts = $cm->getConfiguration($cm::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $settings = $ts['plugin.']['tx_cbgooglemaps.']['settings.'];
        $i18n = $ts['plugin.']['tx_cbgooglemaps.']['_LOCAL_LANG.'];
        $iso2 = $GLOBALS['BE_USER']->uc['lang'] . '.';
        $btnLabels = isset($i18n[$iso2]) ? $i18n[$iso2] : $i18n['default.'];

        $fieldset = '<div class="cbgm_geocoding">';
        $fieldset .= '<input type="button" onclick="cbGooglemaps.doGeocoding(\'' . $this->data['databaseRow']['uid'] . '\',\''
            . $settings['mapProvider'] . '\',\''
            . $settings['mapboxapi.']['accessToken'] . '\')" value="'
            . $btnLabels['btnGeocoding'] . '">';
        $fieldset .= '<input type="button" onclick="cbGooglemaps.displayLocation(\'' . $this->data['databaseRow']['uid'] . '\',\''
            . $settings['mapProvider'] . '\',\''
            . $settings['mapboxapi.']['accessToken'] . '\')" value="'
            . $btnLabels['btnDisplayMap'] . '">';
        $fieldset .= '<div id="cbgm_previewLocation"></div>';
        $fieldset .= '</div>';


        $result = $this->initializeResultArray();
        $result['html'] = $fieldset;
        return $result;
    }
}