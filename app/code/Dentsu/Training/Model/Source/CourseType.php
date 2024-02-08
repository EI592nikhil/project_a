<?php
namespace Dentsu\Training\Model\Source;

Class CourseType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
{    
    Public function getAllOptions() 
        {
        if ($this->_options === null) {
                $this->_options = [
                ['value' => '', 'label' => __('Please Select')],
                ['value' => '1', 'label' => __('Live Training')],
                ['value' => '2', 'label' => __('Offline')],
                ];
        }
            return $this->_options;
        }

    Public function getOptionText($value) 
        {
        foreach ($this->getAllOptions() as $option)
            {
                if ($option['value'] == $value)
                {
                return $option['label'];
                }
            }
        return false;
        }
}