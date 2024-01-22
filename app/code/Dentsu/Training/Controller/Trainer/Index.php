<?php
namespace Dentsu\Training\Controller\Trainer;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\RedirectFactory;

class Index extends Action
{
    protected $resultPageFactory;
    protected $customerRepository;
    protected $resultRedirectFactory;

    public function __construct(Context $context, 
    PageFactory $resultPageFactory,
    RedirectFactory $resultRedirectFactory,
    CustomerRepositoryInterface $customerRepository
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        if($this->getRequest()->getPost('id')=="") {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('/');
            
            return $resultRedirect;

        } else {
            $customerId = $this->getRequest()->getPost('id'); //get Customer id
            $customer = $this->getCustomerById($customerId);
            
            $cust_att_value = $customer->getCustomAttribute("is_trainer")->getValue(); // get Custome Attribute value
            $is_trainer_value = $this->getRequest()->getPost('tid');  //get trainer Attribute value sent 
            
            $customer->setCustomAttribute("is_trainer", $is_trainer_value);
            $Trainerupdated = $this->customerRepository->save($customer);
        }
    }

    public function getCustomerById($customerId) 
    {
        return $this->customerRepository->getById($customerId);
    } 
}
