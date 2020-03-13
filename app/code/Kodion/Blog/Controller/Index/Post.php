<?php
namespace Kodion\Blog\Controller\Index;

use \Magento\Framework\Controller\ResultFactory;

class Post extends \Magento\Framework\App\Action\Action
{
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;
    protected $_messageManager;
    protected $_postFactory;
    protected $_fileUploaderFactory;
    protected $_mediaDirectory;

    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Kodion\Blog\Model\PostFactory $postFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->resultPageFactory    = $resultPageFactory;
        $this->_messageManager      = $messageManager;
        $this->_postFactory         = $postFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    public function execute()
    { 
        $roleName = "Administrators";
        $userEmail = array();
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $roleModel = $objectManager->create('Magento\Authorization\Model\Role');
        $userModel = $objectManager->create('Magento\User\Model\User');
        $roleModel = $roleModel->load($roleName, 'role_name');
        if($roleModel->getId()) {
            $userIds = $roleModel->getRoleUsers();
            foreach($userIds as $userId) {
                $user = $userModel->load($userId);      
                $userEmail[]= $user->getEmail();                                 
            }
        }
        //print_r($userEmail);
        $post = $this->getRequest()->getPost();      
        $post = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        // echo "<pre>";
        // print_r($post);
        if(empty($post)){
            $this->_messageManager->addErrorMessage('Please dont try to submit this url directly');
            $resultRedirect->setUrl('blog/index/index');
            return $resultRedirect;

        }else{    
        if(($post['posttitle']=="")||($post['postcontent']=="")||($post['email']=="")){
            $this->_messageManager->addErrorMessage('Please Fill Required Fields');
            return $resultRedirect;
        }
        if($post['email']!=""){
            if(!in_array($post['email'], $userEmail))
                {
                  $this->_messageManager->addErrorMessage('Not admin user');
                  return $resultRedirect;
                }else{
                    $posttitle = $post['posttitle'];
                    $postcontent = $post['postcontent'];
                    $email = $post['email'];
                    $fileToUpload = $_FILES["fileToUpload"]["name"];

                    $postModel = $this->_postFactory->create();
                    $postModel->setName($posttitle);
                    $postModel->setPostContent($postcontent);
                    $postModel->setEmail($email);
                    $postModel->setFeaturedImage($fileToUpload);
                    $postModel->save();

             
                    //Upload image to a path
                    $target = $this->_mediaDirectory->getAbsolutePath('blog/');  
                    $uploader = $this->_fileUploaderFactory->create(['fileId' => 'fileToUpload']);
         
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);         
                    $uploader->setAllowRenameFiles(true);         
                    //$uploader->setFilesDispersion(false); 
                    $uploader->save($target);         
                    //$uploader->save($path);

                     $this->_messageManager->addSuccessMessage('Your data is submitted');
                     return $resultRedirect;
                }
            }
        }
        return $resultRedirect;

    }
}