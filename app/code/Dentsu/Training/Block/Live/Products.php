<?php

namespace Dentsu\Training\Block\Live;

class Products extends \Magento\Framework\View\Element\Template
{

    public $productRepository;
    public $productCollectionFactory;
    public $orderCollectionFactory;
    public $customerSession;
    public $scopeConfig;
    public $orderRepository;
    public $orderResourceModel;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollectionFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
    ) {
        parent::__construct($context);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        $this->_productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->orderResourceModel = $orderResourceModel;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Pagination'));
        if ($this->getProductIdFromOrderCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'custom.history.pager'
            )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)->setCollection(
                    $this->getProductIdFromOrderCollection()
                );
            $this->setChild('pager', $pager);
            $this->getProductIdFromOrderCollection()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getProductIdFromOrderCollection()
    {
        $productIds = [];
        $customerId = $this->customerSession->getCustomer()->getId();
        $collection =  $this->orderCollectionFactory->create()->addFieldToSelect('*')
            ->addAttributeToFilter('customer_id', $customerId)
            // ->addAttributeToFilter('status', 'pending')
            ->addAttributeToFilter('live_course_status', "pending")
            ->getItems();
        if ($collection) {
            foreach ($collection as $order) {

                $items = $order->getAllItems();
                foreach ($items as $item) {
                    if ($item->getProductType() == 'virtual') {
                        $productIds[$order->getOrderId()] = $item->getProductId();
                    }
                }
            }
            if (isset($productIds)) {
                $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
                $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
                $collection = $this->productCollectionFactory->addAttributeToSelect('*');
                $collection->addAttributeToFilter('entity_id', array($productIds));
                $collection->setPageSize($pageSize);
                $collection->setCurPage($page);
                return $collection;
            } else {
                return false;
            }
        }
    }

    public function getOrderId($productId)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $collection =  $this->orderCollectionFactory->create()->addFieldToSelect('*')
            ->addAttributeToFilter('customer_id', $customerId)
            ->addAttributeToFilter('live_course_status', "pending")
            ->getItems();
        foreach ($collection as $order) {
            $items = $order->getAllItems();
            foreach ($items as $item) {
                if ($item->getProductId() == $productId) {
                    $ordermodel = $this->orderRepository->get($order->getEntityId());
                    $ordermodel = $ordermodel->setLiveCourseStatus('complete');
                    $this->orderResourceModel->save($ordermodel);
                    return true;
                }
            }
        }
    }
    public function getLinkActiveBeforeTimeConfiguration()
    {
        $value = $this->scopeConfig->getValue('mylivecourses/userlivecourse/expire');
        if (!isset($value)) {
            $value = 10;
        }

        return $value;
    }
}
