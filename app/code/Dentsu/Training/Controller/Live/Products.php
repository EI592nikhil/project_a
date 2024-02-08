<?php

namespace Dentsu\Training\Controller\Live;

class Products extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('My Live Courses'));
        $this->_view->renderLayout();
    }
}
