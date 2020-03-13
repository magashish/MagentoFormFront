<?php
namespace Kodion\Blog\Model;
class Post extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'kodion_blog';

	protected $_cacheTag = 'kodion_blog';

	protected $_eventPrefix = 'kodion_blog';

	protected function _construct()
	{
		$this->_init('Kodion\Blog\Model\ResourceModel\Post');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}