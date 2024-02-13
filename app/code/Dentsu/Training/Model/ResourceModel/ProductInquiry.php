<?php

namespace Dentsu\Training\Model\ResourceModel;

class ProductInquiry extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('dentsu_product_inquiry', 'entity_id');
    }
}
