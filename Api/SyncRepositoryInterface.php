<?php

namespace Convertcart\Analytics\Api;

use Convertcart\Analytics\Api\Data\SyncInterface;

interface SyncRepositoryInterface
{
    /**
     * Post Api data.
     *
     * @api
     *
     * @param int $limit
     * @param int $id
     * @param string $type
     *
     * @return array
     */
    public function getDeletedProduct($limit, $id, $type);
}
