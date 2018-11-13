<?php
namespace Brinkert\Cbgooglemaps\Utility\Hook;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Christian Brinkert <christian.brinkert@gmail.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class ContentElementWizard allowes a new icon/link for powermail
 * on adding new content elements
 *
 */
class ContentElementWizard
{
    /**
     * Path to locallang file (with : as postfix)
     *
     * @var string
     */
    protected $locallangPath = 'LLL:EXT:cbgooglemaps/Resources/Private/Language/locallang.xlf:';

    /**
     * Adding a new content element wizard item for powermail
     *
     * @param array $wizardItems
     * @return array
     */
    public function proc($wizardItems = [])
    {

        $wizardItems['plugins_tx_cbgooglemaps'] = [
            'iconIdentifier' => 'ce-default-icon',
            'title'          => $GLOBALS['LANG']->sL($this->locallangPath . 'pluginWizardTitle', true),
            'description'    => $GLOBALS['LANG']->sL($this->locallangPath . 'pluginWizardDescription', true),
            'params'         => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=cbgooglemaps_quickgooglemap'
        ];

        return $wizardItems;
    }
}
