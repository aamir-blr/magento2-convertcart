<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Convertcart\Analytics\Api\Data;

/**
 * Interface SyncInterface
 * @api
 * @since 100.0.2
 */
interface SyncInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ID = 'id';
    public const ITEM_ID = 'item_id';
    public const TYPE   = 'type';
    public const CREATED_AT = 'created_at';
    /**
     * Get ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID.
     *
     * @param integer $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get type.
     *
     * @return string
     */
    public function getType();
 
    /**
     * Set type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * Get itemid.
     *
     * @return string
     */
    public function getItemId();
 
    /**
     * Set itemId.
     *
     * @param string $itemId
     *
     * @return $this
     */
    public function setItemId($itemId);
  
    /**
     * Get createdAt.
     *
     * @return string
     */
    public function getCreatedAt();
 
    /**
     * Set createdAt.
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
