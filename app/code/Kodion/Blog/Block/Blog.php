<?php
namespace Kodion\Blog\Block;
class Blog extends \Magento\Framework\View\Element\Template
{
    protected $_urlInterface;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\UrlInterface $urlInterface,    
        array $data = []
    )
    {        
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getFormAction()
    {
    	return $this->_urlInterface->getUrl('blog/index/post');
    }
}