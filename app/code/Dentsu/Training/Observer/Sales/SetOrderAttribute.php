<?php

namespace Dentsu\Training\Observer\Sales;

class SetOrderAttribute implements \Magento\Framework\Event\ObserverInterface
{

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        // $order = $observer->getData('order');
        // $order->setLiveCourseStatus("pending");
        // $order->save();
    }
}
