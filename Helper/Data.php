<?php
/**
 * Created by PhpStorm.
 * User: ashish
 * Date: 2/5/16
 * Time: 6:00 PM
 */

namespace CzoneTech\ImprovedCatalogSearch\Helper;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class Data extends \Magento\Swatches\Helper\Data
{

    /**
     * @param ProductCollection $productCollection
     * @param array $attributes
     * @return void
     */
    protected function addFilterByAttributes(ProductCollection $productCollection, array $attributes)
    {
        foreach ($attributes as $code => $option) {
            if(is_array($option)){
                $productCollection->addAttributeToFilter($code, ['in' => $option]);
            }else{
                $productCollection->addAttributeToFilter($code, ['eq' => $option]);
            }

        }
    }

}