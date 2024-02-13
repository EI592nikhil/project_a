<?php
namespace Dentsu\Training\Block\Trainer;

class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
     ) {
         $this->_customerSession = $customerSession;
         parent::__construct($context, $defaultPath, $data);
     }

    protected function _toHtml()
    {    
        $responseHtml = null;
        if($this->_customerSession->isLoggedIn()) {
            $Trainer = $this->_customerSession->getCustomer()->getIsTrainer();
            $TrainerStatus = $this->_customerSession->getCustomer()->getTrainerStatus();
            if($Trainer == "1" && $TrainerStatus == '1') {
                $responseHtml = parent::_toHtml();
            } 
        }
         return $responseHtml;
    }
}