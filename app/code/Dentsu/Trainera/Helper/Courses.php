<?php

namespace Dentsu\Trainera\Helper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Downloadable\Api\Data\LinkInterface;
use Magento\Downloadable\Api\LinkRepositoryInterface;
use Magento\Downloadable\Api\SampleRepositoryInterface;
use Magento\Downloadable\Api\Data\SampleInterface;
use Magento\Customer\Model\Session;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Downloadable\Api\Data\File\ContentInterfaceFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Model\Product;


class Courses extends \Magento\Framework\App\Helper\AbstractHelper
{   
      /**
      * @var $productFactory
      */
      private $productFactory;

      /**
       * @var $productRepository
       */
     private $productRepository;
 
     /**
      * @var $storeManagerInterface
      */
 
      private $storeManagerInterface;
 
      /**
      * @var $linkInterface
      */
 
      private $linkInterface;
      
      /**
      * @var $linkRepositoryInterface
      */
 
      private $linkRepositoryInterface;
      
      /**
      * @var $sampleRepositoryInterface
      */
 
      private $sampleRepositoryInterface;
 
      /**
      * @var $sampleInterface
      */
 
      private $sampleInterface;
 
      /**
      * @var $customerSession
      */
 
      private $customerSession;
 
      /**
      * @var UploaderFactory
      */
     private UploaderFactory $uploaderFactory;
 
     /**
      * @var Filesystem
      */
     private Filesystem $filesystem;
 
      
     /**
      * @var \Magento\Downloadable\Api\Data\File\ContentInterface $content
      */
     private ContentInterfaceFactory $contentInterfaceFactory;

     /**
      *  @coursetypemapping array 
      */

    public $courseTypes= ["livetraining" =>"Live Training","Offline"=>"Study Material"];

    
    static $archiveCourseTypesValue = [];

    /**
      * @var  Magento\Eav\Api\AttributeRepositoryInterface  $attribute_repository;
      */
    public $attribute_repository ;

    /**
     * Constructor
     *
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem ,
     */

     public function __construct(
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManagerInterface,
        LinkInterface $linkInterface,
        LinkRepositoryInterface $linkRepositoryInterface,
        SampleRepositoryInterface $sampleRepositoryInterface,
        SampleInterface $sampleInterface,
        Session $customerSession,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        ContentInterfaceFactory $contentInterfaceFactory,
        AttributeRepositoryInterface $attribute_repository

    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->linkInterface = $linkInterface;
        $this->linkRepositoryInterface = $linkRepositoryInterface;
        $this->sampleRepositoryInterface = $sampleRepositoryInterface;
        $this->sampleInterface = $sampleInterface;
        $this->customerSession = $customerSession;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->contentInterfaceFactory = $contentInterfaceFactory;
        $this->attribute_repository = $attribute_repository;
        
    }
    
    public function createOfflineCourse($dataObject)
    {  
        
        $data = (array) $dataObject->getPost();
        $newCours=$this->productFactory->create();
        $sku="course-offline".substr(str_shuffle(md5(time())), 0, 6);
        $newCours->setName($data['name']); // Set Product Name       
        $newCours->setAttributeSetId(4); // Set Attribute Set ID defulat 14
        $newCours->setSku($sku); // Set Random SKU 6 
        $newCours->setUrlKey($sku.$data['name']);// Set url key
        $newCours->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $newCours->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED); //  Set Status by defulat it is disable 
        $newCours->setTypeId(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE);
        
        /* @ToDo */
        //$newCours->setTaxClassId(2); // Set Tax Class Id
        //$newCours->setWebsiteIds([1]); // Set Website Ids
        $newCours->setCategoryIds($data["subcategory"]); // Assign Category Ids
        $newCours->setPrice(100); // Product Price
        $newCours->setPriceType(0);
        $newCours->setPriceView(0);
        
         /* @ToDo */
        // $newCours->setImage('/downloadable/test.jpg'); // Image Path
        // $newCours->setSmallImage('/downloadable/test.jpg'); // Small Image Path
        // $newCours->setThumbnail('/downloadable/test.jpg'); // Thumbnail Image Path
        $newCours->addImageToMediaGallery("downloadable/test_image.jpg", array('image', 'small_image', 'thumbnail'), false, false);

        //set custom attribute 
        $newCours->setCustomerId($this->customerSession->getCustomerId()); // set  trainer id 
        $newCours->setCourseAgendaAttribute($data["courseagenda"]); // set cource agenda
        $newCours->setAvailabilityAttribute($data["availability"]); //set trainer availablity
        
        $optionValue = $this->getAttributeValueForStringValue("type_of_course_attribute",$this->courseTypes[$data["coursetype"]]);
                
        $newCours->setTypeOfCourseAttribute($optionValue); //course type
        
        $newCours->setCourseDurationAttribute($data["courseduration"]); //set course duration
        $newCours->setCourseStartDate($data["from-date"]); //set course start date
        $newCours->setCourseEndDate($data["to-date"]); //set course End date
        $newCours->setBatchStartTime($data["fromtime"]); //batch start time
        $newCours->setBatchEndTime($data["totime"]); //batch end time 

        //$newCours->setRelatedDocumnetAttribute($data["coursedocumnet"]); //set trainer availablity
        
        //set stock
        $newCours->setStockData(
            [
                'qty' => 100,
                'is_in_stock' => 1,
                'manage_stock' => 1,
            ]
            );

            $newCours = $this->productRepository->save($newCours);
            if ($newCours->getId()) {

            //set related documnet /video /file 
            $allfiles = $dataObject->getFiles('coursedocumnet');  
            
            $linkurls=array("sampleurl"=>null,"linkurl"=>null,"name"=>"Course Training Link"); 
            //$filetype=  \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE; 
            $i=0;
            foreach($allfiles as $singleFile){      
                $uploadedfile = $this->uploadFile($singleFile,"coursedocumnet[".$i."]");
                $uploadedfile=array_merge($linkurls,$uploadedfile);
                //add product downloadable link 
                $this->genrateDownloadableLinks(
                    $newCours->getSku(),
                    \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE,
                    $uploadedfile);
                $i++;
            }

            //add training video 
           if(isSet($data["trainingvideo"])){
            $linkurls=array("sampleurl"=>$data["trainingvideo"],"linkurl"=>$data["trainingvideo"],"name"=>"Course Training Link");
            $this->genrateDownloadableLinks(
                $newCours->getSku(),
                \Magento\Downloadable\Helper\Download::LINK_TYPE_URL,
                $linkurls
            );
            }
        }  
            
      
    }

    
     /**
     * genrate downloadable link with sample Files
     * @param string $sku
     * @param string $type
     * @param array $files
     * @throws LocalizedException
     */
    public function genrateDownloadableLinks(string $sku ,string $type,array $linkData) {
       

       $baseUrl = $this->storeManagerInterface->getStore()->getBaseUrl();            
       
       $link_interface = $this->linkInterface;
       $link_interface->setTitle($linkData["name"]);
       $link_interface->setPrice(9);
       $link_interface->setNumberOFDownloads(10);
       $link_interface->setIsShareable(\Magento\Downloadable\Model\Link::LINK_SHAREABLE_CONFIG);
       $link_interface->setLinkType($type);
       $link_interface->setSampleType($type);
       $link_interface->setSampleUrl($linkData["sampleurl"]);
       $link_interface->setLinkUrl($linkData["linkurl"]);
       
       
       if($type==\Magento\Downloadable\Helper\Download::LINK_TYPE_FILE){
            $content = $this->contentInterfaceFactory->create();
            $content->setFileData(
            // @codingStandardsIgnoreLine
            base64_encode(file_get_contents($linkData['path'].$linkData['file']))
            );
            $content->setName($linkData["name"]);
            $link_interface->setLinkFileContent($content);
            
            //@ToDo set sample file content if sample file is diffarent 
            //$sampleContent = $objectManager->create(\Magento\Downloadable\Api\Data\File\ContentInterfaceFactory::class)->create();
            //$sampleContent->setFileData(
            // @codingStandardsIgnoreLine
            //base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . 'test_image.jpg'))
            //);
            //$sampleContent->setName($filedata["name"]);
            //$link->setSampleFileContent($content);
            $link_interface->setSampleFileContent($content);
       }
       
       $link_interface->setIsUnlimited(0);
       $link_interface->setSortOrder(0);
       $this->linkRepositoryInterface->save($sku, $link_interface); // param1 is the sku of your product
       

       //set  sample file            
    //    $sample_interface = $this->sampleInterface;
    //    $sample_interface->setTitle($linkData["name"]);
    //    $sample_interface->setSampleType($type);
    //    $sample_interface->setSampleUrl($linkData["sampleurl"]);
       
    //    if($type==\Magento\Downloadable\Helper\Download::LINK_TYPE_FILE){
    //     $sampleContent = $this->contentInterfaceFactory->create();
    //     $sampleContent->setFileData(
    //     // @codingStandardsIgnoreLine
    //     base64_encode(file_get_contents($linkData['path'].$linkData['file']))
    //     );
    //     $sampleContent->setName($linkData["name"]);
    //     $sample_interface->setSampleFileContent($sampleContent);
    //    }

    //    $sample_interface->setSortOrder(0);
    //    $this->sampleRepositoryInterface->save($sku, $sample_interface);

    }

     /**
     * create live training course 
     *
     * @param array $files
     * @param string|null $fileId
     * @return array|bool
     * @throws LocalizedException
     */
    
    public function createLiveTrainingCourse($dataObject) {
        
        $data = (array) $dataObject->getPost();

        $newLiveCours=$this->productFactory->create();

        $sku="course-live".substr(str_shuffle(md5(time())), 0, 6);

        $newLiveCours->setName($data['name']); // Set Product Name       
        $newLiveCours->setAttributeSetId(4); // Set Attribute Set , defulat 4
        $newLiveCours->setSku("course-live".substr(str_shuffle(md5(time())), 0, 6)); // Set Random SKU 6 
        $newLiveCours->setUrlKey($sku.$data['name']);
        $newLiveCours->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $newLiveCours->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED); //  Set Status by defulat it is disable 
        $newLiveCours->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL);
        $newLiveCours->setCategoryIds($data["subcategory"]); // Assign Category Ids
        /* @ToDo */
        //$newCours->setTaxClassId(2); // Set Tax Class Id
        //$newCours->setWebsiteIds([1]); // Set Website Ids
        
        $newLiveCours->setPrice(100); // Product Price
        $newLiveCours->setPriceType(0);
        $newLiveCours->setPriceView(0);
        
         /* @ToDo */
        // $newCours->setImage('/downloadable/test.jpg'); // Image Path
        // $newCours->setSmallImage('/downloadable/test.jpg'); // Small Image Path
        // $newCours->setThumbnail('/downloadable/test.jpg'); // Thumbnail Image Path
        $newLiveCours->addImageToMediaGallery("downloadable/test_image.jpg", array('image', 'small_image', 'thumbnail'), false, false);

        //set custom attribute 
        $newLiveCours->setCustomerId($this->customerSession->getCustomerId()); // set  trainer id 
        $newLiveCours->setCourseAgendaAttribute($data["courseagenda"]); // set cource agenda
        
        $newLiveCours->setAvailabilityAttribute($data["availability"]); //set trainer availablity
        
        $optionValue = $this->getAttributeValueForStringValue("type_of_course_attribute",$this->courseTypes[$data["coursetype"]]);
                
        $newLiveCours->setTypeOfCourseAttribute($optionValue); //course type

        $newLiveCours->setCourseDurationAttribute($data["courseduration"]); //set trainer availablity
        
        //$newCours->setRelatedDocumnetAttribute($data["coursedocumnet"]); //set trainer availablity
        $now = new \DateTime();
        $newLiveCours->setCourseStartDate($now->format($data["from-date"].' H:i:s')); //set course start date
        $newLiveCours->setCourseEndDate($data["to-date"]); //set course End date
        $newLiveCours->setBatchStartTime($data["fromtime"]); //batch start time
        $newLiveCours->setBatchEndTime($data["totime"]); //batch end time 

        //set stock
        $newLiveCours->setStockData(
        [
            'qty' => 100,
            'is_in_stock' => 1,
            'manage_stock' => 1,
        ]
        );
            
        
        $newLiveCours = $this->productRepository->save($newLiveCours);
        if ($newLiveCours->getId()) {
            //add training video 
            if(isSet($data["trainingvideo"])){
            
            }
        }
            
    } 
   

     /**
     * Upload Files
     *
     * @param array $files
     * @param string|null $fileId
     * @return array|bool
     * @throws LocalizedException
     */
    public function uploadFile(array $files = [], string $fileId = null): bool|array
    {
        
        $result = false;
        // fileId must be a html file field name.
        $uploaderFactory = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploaderFactory->setAllowedExtensions(["jpg", "png", "gif", "pdf"]);
        $uploaderFactory->setAllowRenameFiles(true);
        $uploaderFactory->setFilesDispersion(true);
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $destinationPath = $mediaDirectory->getAbsolutePath('tainer_documnets');
        try {
            $result = $uploaderFactory->save($destinationPath);
            if (!$result) {
                throw new LocalizedException(
                    __('File cannot be saved to path: %1', $destinationPath)
                );
            }

        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage().": %1", $files["name"]));
        }
        return $result;
    }


    public function getAttributeValueForStringValue($attribute_code, $search_string)
    {
        $options = $this->getAttributeOptions($attribute_code);
        if(array_key_exists($search_string, $options)) {
            return $options[$search_string];
        }
    }

    public function getAttributeOptions($attribute_code)
    {
        if(!array_key_exists($attribute_code, self::$archiveCourseTypesValue)) {
            self::$archiveCourseTypesValue[$attribute_code] = array_reduce(
                $this->attribute_repository
                ->get(Product::ENTITY, $attribute_code)
                ->getSource()
                ->getAllOptions(false),
                function ($collector, $attribute) {
                    $collector[$attribute['label']] = $attribute['value'];
                    return $collector;
                },[]);
        }
        return self::$archiveCourseTypesValue[$attribute_code];
    }
}
