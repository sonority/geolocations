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

use Sonority\Geolocations\Domain\Model\DemandInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Install\FolderStructure\Exception\InvalidArgumentException;
use TYPO3\CMS\Install\ViewHelpers\Exception;
use UnexpectedValueException;

/**
 * Location repository with all the callable functionality
 *
 */
class LocationRepository extends AbstractDemandedRepository
{

    /**
     * Returns the objects of this repository matching the demand.
     *
     * @param \array $radialSearchParameters The search parameters as array
     * @return QueryResultInterface The Locations
     */
    public function findLocationsInRadius($radialSearchParameters)
    {
        $latitude = floatval($radialSearchParameters['latitude']);
        $longitude = floatval($radialSearchParameters['longitude']);
        $radius = intval($radialSearchParameters['radius']);
        $categories = GeneralUtility::intExplode(',', $radialSearchParameters['categories'], true);
        $pidList = GeneralUtility::intExplode(',', $radialSearchParameters['pidList'], true);
        $limit = !empty($radialSearchParameters['limit']) ? intval($radialSearchParameters['limit']) : null;
        $orderBy = !empty($radialSearchParameters['orderBy']) ? $radialSearchParameters['orderBy'] : 'distance';
        $orderBy .= ' ' . ((strtolower($this->settings['sortOrder']) === 'desc') ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING);
        $calulcateDistance = intval($radialSearchParameters['calulcateDistance']);
        $kilometer = intval($radialSearchParameters['kilometer']);

        $categoryCount = count($categories);
        $pidCount = count($pidList);

        if ($pidCount && $radius) {
            $query = $this->createQuery();
            $tableName = 'tx_geolocations_domain_model_location';
            $statement = 'SELECT *,
                (
                    6371 * acos(
                        cos(
                            radians(' . $latitude . ')
                        ) * cos(
                            radians(' . $tableName . '.latitude)
                        ) * cos(
                            radians(' . $tableName . '.longitude) - radians(' . $longitude . ')
                        ) + sin(
                            radians(' . $latitude . ')
                        ) * sin(
                            radians(' . $tableName . '.latitude)
                        )
                    )
                ) AS distance FROM ' . $tableName . ' ';

            if ($categoryCount) {
                $statement .= 'LEFT JOIN tx_geolocations_location_category_mm AS mm ON ' . $tableName . '.uid = mm.uid_local ';
            }

            $statement .= 'WHERE ';
            if ($pidCount > 1) {
                $statement .= '' . $tableName . '.pid IN (' . implode(',', $pidList) . ') ';
            } else {
                $statement .= '' . $tableName . '.pid=' . implode(',', $pidList) . ' ';
            }
            if ($categoryCount > 1) {
                $statement .= 'AND mm.uid_foreign IN (' . implode(',', $categories) . ') ';
            } elseif ($categoryCount === 1) {
                $statement .= 'AND mm.uid_foreign=' . implode(',', $categories) . ' ';
            }
            $statement .= $GLOBALS['TSFE']->sys_page->enableFields($tableName) . ' ';
            $statement .= 'HAVING distance < ' . $radius . ' ORDER BY ' . $orderBy . ' ';
            if ($limit) {
                $statement .= 'LIMIT ' . $limit;
            }
            // Set query-statement
            $query->statement($statement);
            // Execute query
            $result = $query->execute();
            // Calulate distance to current coordinates
            if ($calulcateDistance) {
                foreach ($result as $location) {
                    $location->setDistance($latitude, $longitude, $kilometer);
                }
            }
            return $result;
        }
        return false;
    }

    /**
     * Find all records with missing coordinates.
     * This method is used in the backend-module.
     *
     * @return type
     */
    public function findAllMissingCoordinates()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $coordinateConstraints = [
            $query->equals('latitude', ''),
            $query->equals('longitude', '')
        ];
        $constraints = [
            $query->logicalOr($coordinateConstraints),
            $query->logicalNot($query->equals('address', ''))
        ];
        return $query->matching(
                $query->logicalAnd(
                    $constraints
                )
            )->execute();
    }

    /**
     * Returns an array of constraints created from a given demand object.
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     * @throws Exception
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     */
    protected function createConstraintsFromDemand(QueryInterface $query, DemandInterface $demand)
    {
        $constraints = [];
        if ($demand->getCategories() && !empty($demand->getCategories())) {
            $constraints['categories'] = $this->createCategoryConstraint(
                $query, $demand->getCategories()
            );
        }

        // Set storage page
        if (!empty($demand->getStoragePage())) {
            $pidList = GeneralUtility::intExplode(',', $demand->getStoragePage(), true);
            $constraints['pid'] = $query->in('pid', $pidList);
        }

        // Set search
        $searchConstraints = $this->getSearchConstraints($query, $demand);
        if (!empty($searchConstraints)) {
            $constraints['search'] = $query->logicalAnd($searchConstraints);
        }

        // Clean unused constraints
        foreach ($constraints as $key => $value) {
            if (is_null($value)) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }

    /**
     * Returns a category constraint created by a given list of categories
     *
     * @param QueryInterface $query
     * @param  array $categories
     * @return ConstraintInterface|null
     */
    protected function createCategoryConstraint(QueryInterface $query, $categories)
    {
        $categoryConstraints = [];
        if (!is_array($categories)) {
            $categories = GeneralUtility::intExplode(',', $categories, true);
        }
        foreach ($categories as $category) {
            if (!empty($category)) {
                $categoryConstraints[] = $query->contains('categories', $category);
            }
        }
        return $query->logicalOr($categoryConstraints);
    }

    /**
     * Returns an array of orderings created from a given demand object
     *
     * @param DemandInterface $demand
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     */
    protected function createOrderingsFromDemand(DemandInterface $demand)
    {
        $orderings = [];
        $orderList = GeneralUtility::trimExplode(',', $demand->getOrderBy(), true);
        if (!empty($orderList)) {
            // Iterate over each order statement
            foreach ($orderList as $orderItem) {
                list($orderField, $ascDesc) = GeneralUtility::trimExplode(' ', $orderItem, true);
                // count == 1 means that no direction is given
                if ($ascDesc) {
                    $orderings[$orderField] = ((strtolower($ascDesc) === 'desc') ?
                            QueryInterface::ORDER_DESCENDING :
                            QueryInterface::ORDER_ASCENDING);
                } else {
                    $orderings[$orderField] = QueryInterface::ORDER_ASCENDING;
                }
            }
        }
        return $orderings;
    }

    /**
     * Get the search constraints
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @return array
     * @throws UnexpectedValueException
     */
    protected function getSearchConstraints(QueryInterface $query, DemandInterface $demand)
    {
        $constraints = [];
        $searchString = \RemoveXSS::process($demand->getSearchString());
        if (!empty($searchString)) {
            $searchFields = GeneralUtility::trimExplode(',', $demand->getSearchFields(), true);
            $searchConstraints = [];
            if (count($searchFields) === 0) {
                throw new UnexpectedValueException('No search fields defined', 1318497755);
            }
            foreach ($searchFields as $field) {
                $searchConstraints[] = $query->like($field, '%' . $searchString . '%');
            }
            if (count($searchConstraints)) {
                $constraints[] = $query->logicalOr($searchConstraints);
            }
        }
        return $constraints;
    }

}
