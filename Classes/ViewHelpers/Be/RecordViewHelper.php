<?php

namespace Sonority\Geolocations\ViewHelpers\Be;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to create a backend link that should edit/create a location-record
 *
 * @package geolocations
 */
class RecordViewHelper extends AbstractViewHelper
{

    /**
     * Renders the link to edit or create a location-record
     *
     * @param int $uid
     * @return string
     */
    public function render($uid = 0)
    {
        $pid = (int) GeneralUtility::_GET('id');
        if (!empty($uid)) {
            $action = 'edit';
            $record = (int) $uid;
        } else {
            $action = 'new';
            $record = $pid;
        }
        $parameters = [
            'edit[tx_geolocations_domain_model_location][' . $record . ']' => $action,
        ];
        $parameters['returnUrl'] = 'index.php?M=web_GeolocationsMod1&id=' . $pid . '&moduleToken=' .
            FormProtectionFactory::get()->generateToken('moduleCall', 'web_GeolocationsMod1');
        $url = BackendUtility::getModuleUrl('record_edit', $parameters);
        return $url;
    }

}
