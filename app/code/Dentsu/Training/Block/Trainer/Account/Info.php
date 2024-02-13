<?php 
namespace Dentsu\Training\Block\Trainer\Account;

use Magento\Framework\App\Http\Context;
use Magento\Framework\View\Element\Template;

class Info extends Template
{
    protected $_customerSession;
    protected $storeManager;
    
    public function __construct(
        Template\Context $context, 
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = [])
    {
        $this->storeManager = $storeManager;
        $this->_customerSession = $session;
        parent::__construct($context, $data);
    }

    public function isCustomerLoggedIn()
        {
            return $this->_customerSession->isLoggedIn();
        }
    
    public function getCustomerSession(){
        return $this->_customerSession ;
    }
    public function getCurrentUrl()  {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->storeManager->getStore($storeId)->getUrl("trainer/trainer/index/index");
    }

}