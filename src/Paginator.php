<?php

namespace Database;

use ArrayIterator;
use IteratorAggregate;

class Paginator implements IteratorAggregate
{
    /**
     * The items in the current page.
     *
     * @var array
     */
    protected $items = [];

    /**
     * The total number of items.
     *
     * @var int
     */
    protected $total;

    /**
     * The number of items per page.
     *
     * @var int
     */
    protected $perPage;

    /**
     * The current page.
     *
     * @var int
     */
    protected $currentPage;

    /**
     * The url parameter for the page name.
     *
     * @var string
     */
    protected $pageName = 'page';

    /**
     * Create a new paginator instance.
     *
     * @param array $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param string $pageName
     * @return void
     */
    public function __construct($items, $total, $perPage, $currentPage, $pageName = 'page')
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->pageName = $pageName;
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function currentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get the number of items per page.
     *
     * @return int
     */
    public function perPage()
    {
        return $this->perPage;
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function lastPage()
    {
        return (int) ceil($this->total / $this->perPage);
    }

    /**
     * Get the items in the paginator.
     *
     * @return array
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * Get an array iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Determine if the paginator has pages.
     *
     * @return bool
     */
    public function hasPages()
    {
        return $this->lastPage() > 1;
    }

    /**
     * Determine if the paginator is in the first page.
     *
     * @return bool
     */
    public function onFirstPage()
    {
        return $this->currentPage() === 1;
    }

    /**
     * Determine if the paginator has more pages from the current one.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->currentPage() < $this->lastPage();
    }
}
