<?php
namespace Dentsu\Trainera\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class InstallData implements InstallDataInterface
{
	

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    
	/**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */

	public function __construct(
        EavSetupFactory $eavSetupFactory
	){
        $this->eavSetupFactory = $eavSetupFactory;
	}
	
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{   
		//create Extra Product Attribute for download Product (for trainer course)
		$this->createDownloadProductAttribute($setup);
	}

	private function createDownloadProductAttribute($setup){
       
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'course_agenda_attribute',
			[
				'type' => 'text',
				'attribute_set_id' =>'Downloadable',
				//'attribute_group_name'=>"Product Details",
				'backend' => '',
				'frontend' => '',
				'label' => 'Agenda of the course',
				'input' => 'text',
				'class' => '',
				'source' => '',
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
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
				'apply_to' => ''
			]
		);
        
        //attribute used for storing trainer id in trainer course
        $eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'customer_id',
			[
				'type' => 'int',
				'attribute_set_id' =>'Downloadable',
				//'attribute_group_name'=>"Product Details",
				'backend' => '',
				'frontend' => '',
				'label' => 'Trainer Id',
				'input' => 'text',
				'class' => '',
				'source' => '',
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
				'visible' => true,
				'required' => true,
				'user_defined' => true,
				'default' => '',
				'searchable' => false,
				'filterable' =>false,
				'comparable' => false,
				'visible_on_front' => false,
				'used_in_product_listing' => true,
				'unique' => false,
				'apply_to' => ''
			]
		);

        $eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'availability_attribute',
			[
				'type' => 'varchar',
				'attribute_set_id' =>'Downloadable',
				//'attribute_group_name'=>"Product Details",
				'backend' => '',
				'frontend' => '',
				'label' => 'Availability',
				'input' => 'text',
				'class' => '',
				'source' => '',
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
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
				'apply_to' => ''
			]
		);
        $eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'course_duration_attribute',
			[
				'type' => 'varchar',
				'attribute_set_id' =>'Downloadable',
				//'attribute_group_name'=>"Product Details",
				'attribute_group_name'=>"Product Details",
				'backend' => '',
				'frontend' => '',
				'label' => ' Course Duration',
				'input' => 'text',
				'class' => '',
				'source' => '',
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
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
				'apply_to' => ''
			]
		);
        $eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
			'type_of_course_attribute',
			[
				'type' => 'varchar',
				'attribute_set_id' =>'Downloadable',
				//'attribute_group_name'=>"Product Details",
				'attribute_group_name'=>"Product Details",
				'backend' => '',
				'frontend' => '',
				'label' => 'Type Of Course',
				'input' => 'select',
				'class' => '',
				'source' => "",
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
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
				'apply_to' => ''
			]
		);
		
	}
}