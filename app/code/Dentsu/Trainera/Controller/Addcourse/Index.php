<?php
namespace Dentsu\Trainera\Controller\Addcourse;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

use Dentsu\Trainera\Helper\Courses;
 
class Index extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */

     protected $resultPageFactory;


    /**
    * @var courseHelper
    */

    public $courseHelper ;
    
   /**
     * Constructor
     *
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem ,
     */

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Courses $courseHelper

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->courseHelper = $courseHelper;
        parent::__construct($context);
    }
 
    public function execute()
    {   
        

        $dataObject = $this->getRequest();
     
        //echo "---".$this->getRequest()->getMethod();exit;
        if ($this->getRequest()->getMethod()=="POST") {
            try {
            $coursetype=$this->getRequest()->getPost("coursetype");

            if(isset($coursetype) &&  $coursetype =="livetraining"){
                $this->courseHelper->createLiveTrainingCourse($dataObject);
            }else{
                $this->courseHelper->createOfflineCourse($dataObject);
            }
            
            $this->messageManager->addSuccessMessage(__("New Cource added Successfully."));
            } catch (Exception $ex) {
                // print_r($ex->getMessage());
                $this->messageManager->addErrorMessage($e, __("We can\'t submit your request, Please try again."));
    
            }
        }        
        return $this->resultPageFactory->create();
    }

}