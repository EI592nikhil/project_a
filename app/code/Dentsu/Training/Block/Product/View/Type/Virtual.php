<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Simple product data view
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Dentsu\Training\Block\Product\View\Type;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;


/**
 * @api
 * @since 100.0.2
 */



class Virtual extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @var \Magento\Framework\Stdlib\ArrayUtils
     */
    protected $arrayUtils;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param array $data
     */

    /*
    * var $scopeConfig
    */
    public $scopeConfig;


    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        ScopeConfigInterface  $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->arrayUtils = $arrayUtils;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    public function getInstockLable()
    {
        return $this->scopeConfig->getValue('label_configuration/labelforquantity/labelforinstock', ScopeInterface::SCOPE_STORE);
    }
    public function getLabelforLowseats()
    {
        return $this->scopeConfig->getValue('label_configuration/labelforquantity/labelforlowseats', ScopeInterface::SCOPE_STORE);
    }
    public function getLabelforLowseatsnumber()
    {
        return $this->scopeConfig->getValue('label_configuration/labelforquantity/labelforlowseatsnumber', ScopeInterface::SCOPE_STORE);
    }
}
