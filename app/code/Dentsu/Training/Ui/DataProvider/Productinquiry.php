<?php
namespace Dentsu\Training\Ui\DataProvider;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Dentsu\Training\Model\ResourceModel\ProductInquiry\CollectionFactory;
class Productinquiry extends AbstractDataProvider
{
    protected $collection;
    protected $addFieldStrategies;
    protected $addFilterStrategies;
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
 
    public function getCollection()
    {
        return $this->collection;
 
    }
 
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        return $this->getCollection()->toArray();
    }
}