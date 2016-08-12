<?php
/**
 * Copyright © 2016 Czone Technologies. All rights reserved.
 */
namespace CzoneTech\ImprovedCatalogSearch\Plugin;



class Attribute{

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\ItemFactory
     */
    protected $_filterItemFactory;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
    ){
        $this->_filterItemFactory = $filterItemFactory;
    }


    public function aroundApply(\Magento\CatalogSearch\Model\Layer\Filter\Attribute $subject, \Closure
    $method, \Magento\Framework\App\RequestInterface $request){
        $filters = explode(',', $request->getParam($subject->getRequestVar()));
        if ($filters === null) {
            return $subject;
        }
        if(!is_array($filters)){
            $filters = [$filters];
        }
        $attribute = $subject->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $subject->getLayer()
            ->getProductCollection();
        foreach($filters as $filter){
            $label = $subject->getAttributeModel()->getFrontend()->getOption($filter);
            if ($filter && strlen($label)) {
                $productCollection->addFieldToFilter($attribute->getAttributeCode(), $filter);
                $subject->getLayer()
                    ->getState()
                    ->addFilter($this->_createItem($subject, $label, $filter));
            }
        }

        return $subject;
    }

    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  \Magento\Catalog\Model\Layer\Filter\Item
     */
    protected function _createItem($filter, $label, $value, $count = 0)
    {
        return $this->_filterItemFactory->create()
            ->setFilter($filter)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }
}