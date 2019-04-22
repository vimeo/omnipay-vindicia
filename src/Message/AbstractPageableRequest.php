<?php

namespace Omnipay\Vindicia\Message;

/**
 * Abstract request for requests that are pageable. These requests have a page (number) and
 * pageSize parameter.
 *
 * - pageSize: The number of results to return.
 * - page: The page number to return. Starts at 0. For example, if pageSize is 10 and page is
 * 0, returns the first 10 results. If page is 1, returns the second 10 results.
 */
abstract class AbstractPageableRequest extends AbstractRequest
{
    const DEFAULT_PAGE_SIZE = 10000;

    /**
     * Get the number of records to return per call
     *
     * @return null|int
     */
    public function getPageSize()
    {
        return $this->getParameter('pageSize');
    }

    /**
     * Set the number of records to return per call
     *
     * @param int $value
     * @return static
     */
    public function setPageSize($value)
    {
        return $this->setParameter('pageSize', $value);
    }

    /**
     * Get the page to return. Starts at 0.
     * For example, if pageSize is 10 and page is 0, returns the first 10
     * results. If page is 1, returns the second 10 results.
     *
     * @return null|int
     */
    public function getPage()
    {
        return $this->getParameter('page');
    }

    /**
     * Set the page to return. Starts at 0.
     * For example, if pageSize is 10 and page is 0, returns the first 10
     * results. If page is 1, returns the second 10 results.
     *
     * @param int $value
     * @return static
     */
    public function setPage($value)
    {
        return $this->setParameter('page', $value);
    }
}
