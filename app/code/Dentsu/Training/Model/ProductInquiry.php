<?php

namespace Dentsu\Training\Model;


class ProductInquiry extends \Magento\Framework\Model\AbstractModel
{
    public const CACHE_TAG = 'dentsu_product_inquiry_post';

    /**
     * @var string
     */
    protected $_cacheTag = 'dentsu_product_inquiry_post';
    /**
     * @var string
     */
    protected $_eventPrefix = 'dentsu_product_inquiry_post';

  
    protected function _construct()
    {
        $this->_init(\Dentsu\Training\Model\ResourceModel\ProductInquiry::class);
    }
}
