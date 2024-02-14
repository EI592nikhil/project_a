<?php 
namespace Dentsu\Training\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver; 
use Magento\Framework\Data\Helper\PostHelper; 
use Magento\Framework\App\ObjectManager; 
use Magento\Framework\Url\Helper\Data;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Output as OutputHelper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     */

     /*
    * var $scopeConfig
    */
    public $scopeConfig;

    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        TimezoneInterface $timeZone,
        DateTime $dateTime,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface  $scopeConfig,
        array $data = [],
        OutputHelper $outputHelper = null          
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->timeZone = $timeZone;
        $this->dateTime = $dateTime;
        $this->urlHelper = $urlHelper;
        $data['outputHelper'] = $outputHelper ?? ObjectManager::getInstance()->get(OutputHelper::class);
        parent::__construct($context, $this->_postDataHelper, $layerResolver, $this->categoryRepository, $this->urlHelper);
    }

    public function getFormatedDate($courseDate, $formateRequired)
    {
        return $this->dateTime->date($formateRequired, $courseDate);
    }


    public function getCustomerDetails($customerId)
    {
        if($customerId == NULL) {
            return NULL;
        }
        return $this->customerRepository->getById($customerId);  
    }

    public function getTimeZone()
    {
        return $this->timeZone->getDefaultTimezone();
    }

    public function getCourseDuration($courseStartDate, $courseEndDate)
    {
        $courseStartDate = $this->dateTime->date('Y-m-d',$courseStartDate);
        $courseEndDate = $this->dateTime->date('Y-m-d',$courseEndDate);
        
        $datetime1 = date_create($courseStartDate); 
        $datetime2 = date_create($courseEndDate);

        // Calculates the difference between DateTime objects 
        $interval = date_diff($datetime1, $datetime2);
        
        return $interval->format('%a days');
    }
    
    public function getCourseSchedule($courseStartDate, $courseEndDate) 
    {
        $scheduleDate = date_create($courseStartDate); 
        $courseEndDate = date_create($courseEndDate); 

        $i = 1;
        $scheduleDetail = [];

        do {    
           
            $scheduleDate = $scheduleDate->format('D M d');
            $scheduleDetail[] = $scheduleDate; 
            $scheduleDate = date_add(date_create($scheduleDate), date_interval_create_from_date_string("$i days"));
            
        } while ($scheduleDate <= $courseEndDate);

        return $scheduleDetail;
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