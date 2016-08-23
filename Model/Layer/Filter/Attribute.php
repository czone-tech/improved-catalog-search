<?php
/**
 * Created by PhpStorm.
 * User: ashish
 * Date: 2/5/16
 * Time: 3:10 PM
 */

namespace CzoneTech\ImprovedCatalogSearch\Model\Layer\Filter;


class Attribute extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute
{



    /**
     * Apply attribute option filter to product collection
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValues = $request->getParam($this->_requestVar);

        if (empty($attributeValues)) {
            return $this;
        }
        $attribute = $this->getAttributeModel();


        $newAttributeValues = [];
        foreach($attributeValues as $key => $attributeValue){
            //Modify index of filter array to give it 'text keys', so that it gets added to the product collection
            // filters properly
            $newAttributeValues[$attribute->getAttributeCode().'_'.$key] = intval($attributeValue);
            $label = $this->getOptionText($attributeValue);
            $this->getLayer()
                ->getState()
                ->addFilter($this->_createItem($label, $attributeValue));
        }

        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()
            ->getProductCollection();
        $productCollection->addFieldToFilter($attribute->getAttributeCode(), $newAttributeValues);
        //$this->setItems([]); // set items to disable show filtering
        return $this;
    }

}