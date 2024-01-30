<?php
/**
* Copyright © Magento, Inc. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Dentsu\Trainera\Setup\Patch\Data;

use Magento\Catalog\Ui\DataProvider\Product\ProductCollectionFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Psr\Log\LoggerInterface;

/**
* Class AddPhoneAttribute
* @package Dentsu\Trainera\Setup\Patch\Data
*/
class AddPhoneAttribute implements DataPatchInterface, PatchRevertableInterface
{
   /**
    * @var ModuleDataSetupInterface
    */
   private $moduleDataSetup;
   /**
    * @var EavSetupFactory
    */
   private $eavSetupFactory;
   /**
    * @var ProductCollectionFactory
    */
   private $productCollectionFactory;
   /**
    * @var LoggerInterface
    */
   private $logger;
   /**
    * @var Config
    */
   private $eavConfig;
   /**
    * @var \Magento\Customer\Model\ResourceModel\Attribute
    */
   private $attributeResource;

   /**  
    * @param EavSetupFactory $eavSetupFactory
    * @param Config $eavConfig
    * @param LoggerInterface $logger
    * @param \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    */
   public function __construct(
       EavSetupFactory $eavSetupFactory,
       Config $eavConfig,
       LoggerInterface $logger,
       \Magento\Customer\Model\ResourceModel\Attribute $attributeResource,
       \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
   ) {
       $this->eavSetupFactory = $eavSetupFactory;
       $this->eavConfig = $eavConfig;
       $this->logger = $logger;
       $this->attributeResource = $attributeResource;
       $this->moduleDataSetup = $moduleDataSetup;
   }

  /**
     * Apply patch
     *
     * @return void
     */
   public function apply()
   {
       $this->moduleDataSetup->getConnection()->startSetup();
       $this->addPhoneAttribute();
       $this->moduleDataSetup->getConnection()->endSetup();       
   }

   /**
    * @throws \Magento\Framework\Exception\AlreadyExistsException
    * @throws \Magento\Framework\Exception\LocalizedException
    * @throws \Zend_Validate_Exception
    */
   public function addPhoneAttribute()
   {
       $eavSetup = $this->eavSetupFactory->create();


       $eavSetup->addAttribute(
           \Magento\Customer\Model\Customer::ENTITY,
           'mobile_number',
           [
               'type' => 'varchar',
               'label' => 'Mobile Number',
               'input' => 'text',
               'required' => 1,
               'visible' => 1,
               'user_defined' => 1,
               'sort_order' => 30,
               'position' => 30,
               'system' => 0
           ]
       );

       $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
       $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

       $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'mobile_number');
       $attribute->setData('attribute_set_id', $attributeSetId);
       $attribute->setData('attribute_group_id', $attributeGroupId);

       $attribute->setData('used_in_forms', [
           'adminhtml_customer',
           'customer_account_create',
           'customer_account_edit',
       ]);
       $attribute->setData("is_used_in_grid", 1);
       $attribute->setData("is_visible_in_grid", 1);
       
       $this->attributeResource->save($attribute);
   }

   /**
    * {@inheritdoc}
    */
   public static function getDependencies()
   {
       return [];
   }

   /**
    *
    */
   public function revert()
   {
   }

   /**
    * {@inheritdoc}
    */
   public function getAliases()
   {
       return [];
   }
}