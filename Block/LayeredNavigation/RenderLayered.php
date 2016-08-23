<?php
/**
 * Copyright © 2016 Czone Technologies. All rights reserved.
 */

 namespace CzoneTech\ImprovedCatalogSearch\Block\LayeredNavigation;

use Magento\Eav\Model\Entity\Attribute;

class RenderLayered extends \Magento\Swatches\Block\LayeredNavigation\RenderLayered
{

    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'CzoneTech_ImprovedCatalogSearch::product/layered/renderer.phtml';

    /**
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     */
    public function buildUrl($attributeCode, $optionId)
    {
        $requestValue = $this->getRequest()->getParam($attributeCode);
        if(is_array($requestValue)){
            if(!in_array($optionId, $requestValue)){
                $newValue = array_merge($requestValue, [$optionId]);
            }else{
                $newValue = array_diff($requestValue, [$optionId]);
            }
        }else{
            $newValue = [$optionId];
        }
        $query = [$attributeCode => $newValue];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}