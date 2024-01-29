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

        CheckoutSession $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productFactory

    ) {

        $this->checkoutSession = $checkoutSession;
        $this->productFactory = $productFactory;
    }




    public function afterGetConfig(

        \Magento\Checkout\Model\DefaultConfigProvider $subject,

        array $result

    ) {

        $items = $result['totalsData']['items'];

        foreach ($items as $index => $item) {

            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $result['quoteItemData'][$index]['type_of_course_attribute'] = $quoteItem->getProduct()->getData('type_of_course_attribute');


            $poductReource = $this->productFactory->create();
            $attribute = $poductReource->getAttribute('type_of_course_attribute');

            if ($attribute && $attribute->usesSource()) {
                $option_Text = $attribute->getSource()->getOptionText($result['quoteItemData'][$index]['type_of_course_attribute']);
            }
        }
        $result['quoteItemData'][$index]['type_of_course_attribute'] = $option_Text;

        return $result;
    }
}
