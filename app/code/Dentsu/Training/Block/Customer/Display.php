<?php
namespace Dentsu\Training\Block\Customer;

use Magento\Eav\Model\Config as EavConfig;

class Display extends \Magento\Framework\View\Element\Template
{
    public $_eavConfig;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        EavConfig $_eavConfig
    ) {
        $this->_eavConfig = $_eavConfig;
        parent::__construct($context);
    }
    
    public function getAreaofInterests()
    {
        $attributeCode = "area_of_interests";
        $attribute = $this->_eavConfig->getAttribute('customer', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        $arr = [];
        foreach ($options as $option) {
            if ($option['value'] > 0) {
                $arr[] = $option;
            }
        }
        return $arr;
      }

}