<?php

namespace Sonority\Geolocations\Domain\Repository;

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

use Sonority\Geolocations\Domain\Model\DemandInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\BackendInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Abstract demanded repository
 *
 */
abstract class AbstractDemandedRepository extends Repository implements DemandedRepositoryInterface
{

    /**
     * @var BackendInterface
     */
    protected $storageBackend;

    /**
     * @param BackendInterface $storageBackend
     * @return void
     */
    public function injectStorageBackend(BackendInterface $storageBackend)
    {
        $this->storageBackend = $storageBackend;
    }

    /**
     * Returns an array of constraints created from a given demand object.
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     * @abstract
     */
    abstract protected function createConstraintsFromDemand(QueryInterface $query, DemandInterface $demand);

    /**
     * Returns an array of orderings created from a given demand object.
     *
     * @param DemandInterface $demand
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     * @abstract
     */
    abstract protected function createOrderingsFromDemand(DemandInterface $demand);

    /**
     * Returns the objects of this repository matching the demand.
     *
     * @param DemandInterface $demand
     * @param bool $respectEnableFields
     * @return QueryResultInterface
     */
    public function findDemanded(DemandInterface $demand, $respectEnableFields = true)
    {
        $query = $this->generateQuery($demand, $respectEnableFields);
        return $query->execute();
    }

    protected function generateQuery(DemandInterface $demand, $respectEnableFields = true)
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = $this->createConstraintsFromDemand($query, $demand);

        // Call hook functions for additional constraints
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['geolocations']['Domain/Repository/AbstractDemandedRepository.php']['findDemanded'])) {
            $params = [
                'demand' => $demand,
                'respectEnableFields' => &$respectEnableFields,
                'query' => $query,
                'constraints' => &$constraints,
            ];
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['geolocations']['Domain/Repository/AbstractDemandedRepository.php']['findDemanded'] as $reference) {
                GeneralUtility::callUserFunction($reference, $params, $this);
            }
        }

        if ($respectEnableFields === false) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
            $constraints[] = $query->equals('deleted', 0);
        }

        if (!empty($constraints)) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        $orderings = $this->createOrderingsFromDemand($demand);
        if ($orderings) {
            $query->setOrderings($orderings);
        }

        if ($demand->getLimit() != null) {
            $query->setLimit((int) $demand->getLimit());
        }

        return $query;
    }

}
