<?php
/**
 * Created by PhpStorm.
 * User: ashish
 * Date: 2/5/16
 * Time: 3:10 PM
 */

namespace CzoneTech\ImprovedCatalogSearch\Model\Layer\Filter;


class Attribute extends \Magento\Catalog\Model\Layer\Filter\Attribute
{

    /**
     * @param ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory $filterAttributeFactory
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory $filterAttributeFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Filter\StripTags $tagFilter,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $filterAttributeFactory,
            $string, $tagFilter, $data);
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $filters = $request->getParam($this->_requestVar);
        if($filters === null){
            return $this;
        }
        if (!is_array($filters)) {
            $filters = [$filters];
        }
        //Apply filters to the collection
        $this->_getResource()->applyFilterToCollection($this, $filters);
        //Add the filter values to 'state'. So for multiple values, this gets added multiple times
        foreach($filters as $filter){
            $text = $this->getOptionText($filter);
            if ($filter && strlen($text)) {
                $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
                //It is very important to comment the line below, otherwise any attribute that has a value in
                // RequestVar will have its items set to '[]', and the associated filters section won't show in the
                // layered navigation block
                //$this->_items = [];
            }
        }

        return $this;
    }

}