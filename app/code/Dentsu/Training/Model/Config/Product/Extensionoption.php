<?php

namespace Dentsu\Training\Model\Config\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;


class Extensionoption extends AbstractSource
{
    protected $optionFactory;
    public function getAllOptions()
    {
        $this->_options = [];
        $this->_options[] = ['label' => 'Java', 'value' => 'java'];
        $this->_options[] = ['label' => 'Magento', 'value' => 'magento'];
        $this->_options[] = ['label' => 'Salesforce', 'value' => 'salesforce'];
        $this->_options[] = ['label' => 'AWS', 'value' => 'aws'];
        return $this->_options;
    }
}