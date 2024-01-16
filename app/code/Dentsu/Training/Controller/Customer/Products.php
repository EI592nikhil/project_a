<?php 
namespace Dentsu\Training\Controller\Customer;

class Products extends \Magento\Framework\App\Action\Action { 

public function execute() { 

$this->_view->loadLayout(); 
$this->_view->getPage()->getConfig()->getTitle()->set(__('My Courses'));

$this->_view->renderLayout(); 

} 
} 
?>