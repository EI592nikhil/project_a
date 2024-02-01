<?php
/**
* Copyright © Magento, Inc. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Dentsu\Trainera\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Psr\Log\LoggerInterface;
class LiveCourseAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->logger = $logger;
    }
    /**
     * @return void
     */
    public function apply()
    {
        try {
            $productTypes = implode(',', [Type::TYPE_SIMPLE, Type::TYPE_VIRTUAL]);
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
            $eavSetup->addAttribute(Product::ENTITY, 'course_start_date', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Course Start Date',
                'input' => 'date',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes
            ]);
            $eavSetup->addAttribute(Product::ENTITY, 'course_end_date', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Course End Date',
                'input' => 'date',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes
            ]);
            $eavSetup->addAttribute(Product::ENTITY, 'batch_start_time', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Batch Start Time',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes
            ]);
            $eavSetup->addAttribute(Product::ENTITY, 'batch_end_time', [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Batch End Time',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes
            ]);
            $eavSetup->addAttribute(Product::ENTITY, 'course_agenda_attribute', [
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Agenda of the course',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes
                ]);
            
            //attribute used for storing trainer id in trainer course
            $eavSetup->addAttribute(Product::ENTITY, 'customer_id', [        
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Trainer Id',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' =>false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes
            ]);
            $eavSetup->addAttribute(Product::ENTITY, 'availability_attribute', [ 
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Availability',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes
            ]);
            $eavSetup->addAttribute(Product::ENTITY, 'course_duration_attribute', [         
                'type' => 'varchar',
                'attribute_group_name'=>"Product Details",
                'backend' => '',
                'frontend' => '',
                'label' => ' Course Duration',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => $productTypes
            ]);
            $eavSetup->addAttribute(Product::ENTITY, 'type_of_course_attribute', [        
                'type' => 'varchar',
                'attribute_group_name'=>"Product Details",
                'backend' => '',
                'frontend' => '',
                'label' => 'Type Of Course',
                'input' => 'select',
                'class' => '',
                'source' => "",
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'option'  => [
                    'value' => [
                        'option_1' => ['Live Training'],
                        'option_2' => ['Study Material'],
                        'option_3' => ['Both - Live Training and Study Material'],
                    ],
                    'order' => [
                        'option_1' => 1,
                        'option_2' => 2,
                        'option_3' => 3,
                    ],
                ],
                'apply_to' => $productTypes
            ]);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }
    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}