<?php
namespace Dentsu\Training\Block\Customer;
class Products extends \Magento\Framework\View\Element\Template
{
public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
\Magento\Catalog\Model\ResourceModel\Product\Collection $productCollectionFactory,
\Magento\Catalog\Model\ProductRepository $productRepository,
\Magento\Customer\Model\Session $customerSession
)
{
    parent::__construct($context);
    $this->productCollectionFactory = $productCollectionFactory;
    $this->customerSession = $customerSession;
    $this->_productRepository = $productRepository;

}

public function myCourses()
{
    $cutomerId=$this->customerSession->getCustomer()->getId();
    $collection = $this->productCollectionFactory->addAttributeToSelect('*')->setFlag('has_stock_status_filter', false);
    $myCourses=$collection->addAttributeToFilter('customer_id',$cutomerId)->toArray() ;
    return $myCourses;
}

protected function _prepareLayout()
{
    parent::_prepareLayout();
    $this->pageConfig->getTitle()->set(__('My Pagination'));
    if ($this->getProductCollection()) {
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'custom.history.pager'
        )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
        ->setShowPerPage(true)->setCollection(
            $this->getProductCollection()
        );
        $this->setChild('pager', $pager);
        $this->getProductCollection()->load();
    }
    return $this;
}

public function getPagerHtml()
{
    return $this->getChildHtml('pager');
}

public function getProductCollection()
{
    $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
    $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
    $cutomerId=$this->customerSession->getCustomer()->getId();
    $collection = $this->productCollectionFactory->addAttributeToSelect('*')->setFlag('has_stock_status_filter', false);
    $collection->addAttributeToFilter('customer_id',$cutomerId);
    $collection->setPageSize($pageSize);
    $collection->setCurPage($page);
    return $collection;    
}
}