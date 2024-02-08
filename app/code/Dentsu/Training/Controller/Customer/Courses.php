<?php 
namespace Dentsu\Training\Controller\Customer;

class Courses extends \Magento\Framework\App\Action\Action { 

public function execute() { 

$this->_view->loadLayout(); 
$this->_view->getPage()->getConfig()->getTitle()->set(__('My Courses Grid'));
$this->_view->renderLayout(); 

} 
} 
?>