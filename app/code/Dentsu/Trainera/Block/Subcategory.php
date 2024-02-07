<?php
namespace Dentsu\Trainera\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\CategoryFactory;

class Subcategory extends \Magento\Framework\View\Element\Template
{
    protected $_categoryFactory;

    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }

    public function getSubcategories($parentId)
    {
        $parentCategory = $this->_categoryFactory->create()->load($parentId);
        return $parentCategory->getChildrenCategories();
    }
}