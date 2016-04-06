<?php

namespace Sonority\Geolocations\Domain\Repository;

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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The repository for Categories
 */
class CategoryRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    protected function createConstraintsFromDemand(QueryInterface $query, DemandInterface $demand)
    {

    }

    /**
     * Find all categories ordered by name
     *
     * @return QueryInterface The categories
     */
    public function findAll()
    {
        $query = $this->createQuery();
        $query->setOrderings(Array('title' => QueryInterface::ORDER_ASCENDING));
        return $query->execute();
    }

    /**
     * Find categories by a given pid
     *
     * @param array $idList List of ids
     * @param array $ordering ordering
     * @return QueryInterface
     */
    public function findByIdList(array $idList, array $ordering = [], $startingPoint = null)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);

        if (count($ordering) > 0) {
            $query->setOrderings($ordering);
        }
        $this->overlayTranslatedCategoryIds($idList);

        $conditions = [];
        $conditions[] = $query->in('uid', $idList);

        if (is_null($startingPoint) === false) {
            $conditions[] = $query->in('pid', GeneralUtility::trimExplode(',', $startingPoint, true));
        }

        return $query->matching(
                $query->logicalAnd(
                    $conditions
            ))->execute();
    }

    /**
     * Overlay the category ids with the translated record
     *
     * @param array $idList
     * return void
     */
    protected function overlayTranslatedCategoryIds(array &$idList)
    {
        $language = $this->getSysLanguageUid();

        if ($language > 0) {
            if (isset($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
                $whereClause = 'sys_language_uid=' . $language . ' AND l10n_parent IN(' . implode(',', $idList) . ')' . $GLOBALS['TSFE']->sys_page->enableFields('tx_geolocations_domain_model_category');
                $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'l10n_parent, uid,sys_language_uid',
                    'tx_geolocations_domain_model_category',
                    $whereClause
                );
                $idList = $this->replaceCategoryIds($idList, $rows);
            }
        }
    }

    /**
     * Get the current sys language uid
     *
     * @return int
     */
    protected function getSysLanguageUid()
    {
        $sysLanguage = 0;
        if (isset($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            $sysLanguage = $GLOBALS['TSFE']->sys_language_content;
        } elseif (intval(GeneralUtility::_GP('L'))) {
            $sysLanguage = intval(GeneralUtility::_GP('L'));
        }

        return $sysLanguage;
    }

}
