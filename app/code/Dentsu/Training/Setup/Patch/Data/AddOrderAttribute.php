<?php

namespace Dentsu\Training\Setup\Patch\Data;

use Magento\Backend\Block\Dashboard\Sales;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Psr\Log\LoggerInterface;

class AddOrderAttribute implements DataPatchInterface
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
        // try {
        //     $productTypes = implode(',', [Type::TYPE_SIMPLE, Type::TYPE_VIRTUAL]);
        //     /** @var EavSetup $eavSetup */
        //     $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        //     $eavSetup->addAttribute('order', 'live_course_status', [
        //         'type' => 'varchar',
        //         'length' => 5,
        //         'visible' => false,
        //         'required' => false,
        //         'grid' => true,
        //         'label' => 'Live Course Status',
        //         'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        //     ]);
        //     $customAttribute = array('live_course_status');
        //     foreach ($customAttribute as $salesAttr) {
        //         $eavSetup->addAttributeToGroup(
        //             'order',
        //             'Default',
        //             'General', // group
        //             $salesAttr,
        //             50 // sort order
        //         );
        //     }
        // } catch (\Exception $e) {
        //     $this->logger->critical($e);
        // }
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
