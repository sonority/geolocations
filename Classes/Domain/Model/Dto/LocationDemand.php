<?php

namespace Sonority\Geolocations\Domain\Model\Dto;

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

/**
 * Location Demand object which holds all information to get the correct location records.
 *
 */
class LocationDemand extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements DemandInterface
{

    /**
     * @var array
     */
    protected $categories;

    /** @var string */
    protected $searchFields;

    /** @var string */
    protected $searchString;

    /** @var string */
    protected $orderBy;

    /** @var int */
    protected $storagePage;

    /** @var int */
    protected $limit;

    /** @var string */
    protected $action;

    /** @var string */
    protected $class;

    /**
     * List of allowed categories
     *
     * @param array $categories categories
     * @return void
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get allowed categories
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set search fields
     *
     * @param string $searchFields Search fields
     * @return void
     */
    public function setSearchFields($searchFields)
    {
        $this->searchFields = $searchFields;
    }

    /**
     * Get search fields
     *
     * @return string
     */
    public function getSearchFields()
    {
        return $this->searchFields;
    }

    /**
     * Set search string
     *
     * @param string $searchString Search string
     * @return void
     */
    public function setSearchString($searchString)
    {
        $this->searchString = $searchString;
    }

    /**
     * Get search string
     *
     * @return string
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    /**
     * Set order by
     *
     * @param string $orderBy Order by
     * @return void
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /**
     * Get order by
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set list of storage pages
     *
     * @param string $storagePage storage page list
     * @return void
     */
    public function setStoragePage($storagePage)
    {
        $this->storagePage = $storagePage;
    }

    /**
     * Get list of storage pages
     *
     * @return string
     */
    public function getStoragePage()
    {
        return $this->storagePage;
    }

    /**
     * Set limit
     *
     * @param array $limit limit
     * @return void
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @param string $action
     * @param string $controller
     */
    public function setActionAndClass($action, $controller)
    {
        $this->action = $action;
        $this->class = $controller;
    }

}
