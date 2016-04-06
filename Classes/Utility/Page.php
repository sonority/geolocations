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

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Page Utility class
 *
 * @package geolocations
 */
class Page
{

    /**
     * Find recursive PIDs from a given list
     *
     * @param string $pidList Comma separated list of page-IDs
     * @param int $depth Recursive levels
     * @return string Comma separated list of page-IDs
     */
    public static function getRecursivePidList($pidList = '', $depth = 0)
    {
        if ((int) $depth > 0) {
            $recursiveStoragePids = [];
            $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
            $storagePids = GeneralUtility::intExplode(',', $pidList);
            foreach ($storagePids as $pid) {
                $treeList = $queryGenerator->getTreeList($pid, $depth, 0, 1);
                if (strlen($treeList) > 0) {
                    $recursiveStoragePids = array_merge($recursiveStoragePids, explode(',', $treeList));
                }
            }
            $pidList = implode(',', array_unique($recursiveStoragePids));
        }
        return $pidList;
    }

}
