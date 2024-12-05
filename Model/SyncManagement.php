<?php

namespace Convertcart\Analytics\Model;

use Convertcart\Analytics\Api\Data\SyncInterface;

class SyncManagement implements SyncInterface, \Magento\Framework\DataObject\IdentityInterface
{

    public const CACHE_TAG = 'Convertcart_Analytics';

    protected function _construct()
    {
        /* _init($resourceModel)  */
        $this->_init(\Convertcart\Analytics\Model\ResourceModel\Sync::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return SyncInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get TYPE.
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return SyncInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get itemId.
     *
     * @return string|null
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * Set itemId.
     *
     * @param string $itemId
     *
     * @return SyncInterface
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * Get createdAt.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set createdAt.
     *
     * @param string $createdAt
     *
     * @return SyncInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
