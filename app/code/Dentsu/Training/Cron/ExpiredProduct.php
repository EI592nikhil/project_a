<?php
Namespace Dentsu\Training\Cron;

use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Type;
use Psr\Log\LoggerInterface;

class ExpiredProduct {
    
    protected $logger;

    protected $messageManager;

    private $productCollection;
    
    private $productAction;
    
    private $storeManager;

    public function __construct(
        CollectionFactory $collection,
        ProductAction $action,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    )
    {
        $this->productCollection = $collection;
        $this->productAction = $action;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }    

   /**
    * Write to system.log
    *
    * @return void
    */
    
    public function execute() {
        $this->setAttributeData();
    }

    public function setAttributeData()
    {
        try {

            $now = new \DateTime();
            $currentDate=$now->format('Y-m-d H:i:s');
            $collection = $this->productCollection->create()
                            ->addAttributeToFilter('type_id', Type::TYPE_VIRTUAL)
                            ->addAttributeToFilter('status', 1)
                            ->addAttributeToFilter('course_start_date',['lteq' => $now->format('Y-m-d H:i:s')])
                            ->addFieldToFilter('batch_start_time',['neq' => 'NULL']); //added for adding column in collection 
            $storeId = $this->storeManager->getStore()->getId();
            $ids = [];
            $i = 0;
            foreach ($collection as $item) {               
                //if course start date greater than current date 
                //start date is today and time is less than current time.
                if($item->getCourseStartDate() < $currentDate || 
                   ($item->getCourseStartDate() == $currentDate 
                   && (strtotime($item->getBatchStartTime()) > strtotime(date("H:i")))
                   )){
                    $ids[$i] = $item->getEntityId();
                    $i++;
                }
            }
           
            $this->productAction->updateAttributes($ids, array('status' => 0), $storeId);
            $this->logger->info('disable products'.print_r($ids));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}