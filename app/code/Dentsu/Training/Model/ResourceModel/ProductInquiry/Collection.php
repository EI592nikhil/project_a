<?php

namespace Dentsu\Training\Model\ResourceModel\ProductInquiry;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * @var string
     */
    protected $_eventPrefix = 'dentsu_product_inquiry_post_collection';
    /**
     * @var string
     */
    protected $_eventObject = 'product_inquiry_collection';

    protected function _construct()
    {
        $this->_init(
            \Dentsu\Training\Model\ProductInquiry::class,
            \Dentsu\Training\Model\ResourceModel\ProductInquiry::class
        );
    }
}
