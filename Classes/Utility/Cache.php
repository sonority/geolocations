<?php

namespace Sonority\Geolocations\Utility;

/**
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

use Sonority\Geolocations\Domain\Model\Dto\LocationDemand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Cache Utility class
 *
 * @package geolocations
 */
class Cache
{

    /**
     * Adds page cache tags by used storagePages.
     * This adds tags with the scheme tx_geolocations_pid_[location:pid]
     *
     * @param LocationDemand $demand
     * @return void
     */
    public static function addPageCacheTagsByDemandObject(LocationDemand $demand)
    {
        $cacheTags = [];
        if ($demand->getStoragePage()) {
            // Add cache tags for each storage page
            foreach (GeneralUtility::trimExplode(',', $demand->getStoragePage()) as $pageId) {
                $cacheTags[] = 'tx_geolocations_pid_' . $pageId;
            }
        }
        if (count($cacheTags) > 0) {
            $GLOBALS['TSFE']->addCacheTags($cacheTags);
        }
    }

}
