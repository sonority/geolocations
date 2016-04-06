<?php

namespace Sonority\Geolocations\Hooks;

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

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook into TCEMAIN
 *
 * @package geolocations
 */
class DataHandler
{

    /**
     * Clear cache for 'cache_geolocations_category' if categories were modified
     *
     * @param string $status Status
     * @param string $table Table name
     * @param int $recordUid Id of the record
     * @param array $fields Field array
     * @param DataHandler $parentObject Parent object
     * @return void
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $recordUid, array $fields, DataHandler $parentObject)
    {
        // Clear category cache
        if ($table === 'tx_geolocations_domain_model_category') {
            /** @var FrontendInterface $cache */
            $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('cache_geolocations_category');
            $cache->flush();
        }
    }

}
