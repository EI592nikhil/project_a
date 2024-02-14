<?php

namespace Dentsu\Training\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;


class Defaultconfigprovider

{

    /**
 
     * @var CheckoutSession
 
     */

    protected $checkoutSession;




    /**
 
     * Constructor
 
     *
 
     * @param CheckoutSession $checkoutSession
 
     */

    public function __construct(

        CheckoutSession $checkoutSession

    ) {

        $this->checkoutSession = $checkoutSession;
    }

    public function afterGetConfig(

        \Magento\Checkout\Model\DefaultConfigProvider $subject,

        array $result

    ) {

        $items = $result['totalsData']['items'];

        foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $result['quoteItemData'][$index]['type_of_course_attribute'] = $quoteItem->getProduct()->getAttributeText('type_of_course_attribute');
        }
        return $result;
    }
}
