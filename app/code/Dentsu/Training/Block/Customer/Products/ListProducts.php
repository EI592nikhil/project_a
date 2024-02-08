<?php

namespace Dentsu\Training\Block\Customer\Products;

use Magento\Downloadable\Model\Link\Purchased\Item;

/**
 * Block to display downloadable links bought by customer
 *
 * @api
 * @since 100.0.2
 */
class ListProducts extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Downloadable\Model\ResourceModel\Link\Purchased\CollectionFactory
     */
    protected $_linksFactory;

    protected $date;

    /**
     * @var \Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory
     */
    protected $_itemsFactory;

        /**
    * @var \Magento\Sales\Model\OrderRepository
    */
    protected $_orderRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Downloadable\Model\ResourceModel\Link\Purchased\CollectionFactory $linksFactory
     * @param \Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory $itemsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Downloadable\Model\ResourceModel\Link\Purchased\CollectionFactory $linksFactory,
        \Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory $itemsFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        //\Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->_linksFactory = $linksFactory;
        $this->_itemsFactory = $itemsFactory;
        $this->_orderRepository = $orderRepository;
        $this->date =  $date;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $purchased = $this->_linksFactory->create()
            ->addFieldToFilter('customer_id', $this->currentCustomer->getCustomerId())
            ->addOrder('created_at', 'desc');
        $this->setPurchased($purchased);
        $purchasedIds = [];
        foreach ($purchased as $_item) {
            $purchasedIds[] = $_item->getId();
        }
        if (empty($purchasedIds)) {
            $purchasedIds = [null];
        }
        $purchasedItems = $this->_itemsFactory->create()->addFieldToFilter(
            'purchased_id',
            ['in' => $purchasedIds]
        )->addFieldToFilter(
            'status',
            ['nin' => [Item::LINK_STATUS_PENDING_PAYMENT, Item::LINK_STATUS_PAYMENT_REVIEW]]
        )->setOrder(
            'item_id',
            'desc'
        );
        $this->setItems($purchasedItems);
    }

    /**
     * Enter description here...
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock(
            \Magento\Theme\Block\Html\Pager::class,
            'downloadable.customer.products.pager'
        )->setCollection(
            $this->getItems()
        )->setPath('downloadable/customer/products');
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        foreach ($this->getItems() as $item) {
            $item->setPurchased($this->getPurchased()->getItemById($item->getPurchasedId()));
        }
        return $this;
    }

    /**
     * Return order view url
     *
     * @param integer $orderId
     * @return string
     */
    public function getOrderViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /**
     * Return number of left downloads or unlimited
     *
     * @param Item $item
     * @return \Magento\Framework\Phrase|int
     */
    public function getRemainingDownloads($item)
    {
        if ($item->getNumberOfDownloadsBought()) {
            $downloads = $item->getNumberOfDownloadsBought() - $item->getNumberOfDownloadsUsed();
            return $downloads;
        }
        return __('Unlimited');
    }

    /**
     * Return url to download link
     *
     * @param Item $item
     * @return string
     */
    public function getDownloadUrl($item)
    {
        return $this->getUrl('downloadable/download/link', ['id' => $item->getLinkHash(), '_secure' => true]);
    }

    /**
     * Return url to download link
     *
     * @param orderId $orderId
     * @return $invoiceCollection
     */

    public function getInvoice($orderId){
        $order = $this->_orderRepository->get($orderId);
        $invoiceCollection = $order->getInvoiceCollection();
        $invoiceData = [];
        $expireDate = "";
        $inovideDateCreatedFormat="";
        $nextDate = "";
        $diff = "";
        foreach ($invoiceCollection as $item_inv){
            $invoiceCreatedDate = $item_inv->getCreatedAt();
            if($invoiceCreatedDate){
                $inovideDateCreatedFormat = $this->getDateFormat($invoiceCreatedDate);

            } else {
                $inovideDateCreatedFormat="";
            }
            $nextDate = $this->getdate($invoiceCreatedDate); // get expire date after invoice created 
            $expireDate = strtotime($nextDate); // convert expire date
            $invoicedate = strtotime($inovideDateCreatedFormat);
            $currentDate = strtotime($this->getCurrentDate());
            $dateDiff = $currentDate - $invoicedate; 
            $diff = round($dateDiff/(60*60*24));
            break;
        }
        $invoiceData = [$inovideDateCreatedFormat,$nextDate,$diff];
        return $invoiceData;
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsOpenInNewWindow()
    {
        return $this->_scopeConfig->isSetFlag(
            \Magento\Downloadable\Model\Link::XML_PATH_TARGET_NEW_WINDOW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getDate($createdate){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('expiredaterange/daterange/expire');
        $nextdate = $this->date->date('Y-m-d',$createdate.$conf."days");
        return $nextdate;
    }

    public function getCurrentDate(){
        $currentdate= $this->date->date('Y-m-d');
        return $currentdate;
    }

    public function getDateFormat($createdate){
        $nextdate = $this->date->date('Y-m-d',$createdate);
        return $nextdate;
    }

}
