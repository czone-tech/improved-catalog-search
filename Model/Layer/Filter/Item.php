<?php
/**
 * Copyright © 2016 Czone Technologies. All rights reserved.
 */

namespace CzoneTech\ImprovedCatalogSearch\Model\Layer\Filter;


use Magento\Framework\App\RequestInterface;

class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{

    protected $_request;
    /**
     * Construct
     *
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        RequestInterface $request,
        array $data = []
    ) {
        $this->_request = $request;
        parent::__construct($url, $htmlPagerBlock, $data);
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $requestValue = $this->_request->getParam($this->getFilter()->getRequestVar());
        if($requestValue){
            $requestValue = explode(',', $requestValue);
            $value = $this->getValue();
            if(!in_array($value, $requestValue)){
                $newValue = array_merge($requestValue, [$value]);
            }else{
                $newValue = array_diff($requestValue, [$value]);
            }
        }else{
            $newValue = [$this->getValue()];
        }

        $query = [
            $this->getFilter()->getRequestVar() => implode(',', $newValue),
            // exclude current page from urls
            $this->_htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query,
            '_escape' => false]);
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        /**
         * @var $filter \Magento\Catalog\Model\Layer\Filter\Item
         */
        $filterValues = [];
        $filters = $this->getFilter()->getLayer()->getState()->getFilters();
        foreach($filters as $filter){
            if($filter->getFilter()->getRequestVar() == $this->getFilter()->getRequestVar()){
                if($filter->getValueString() != $this->getValueString()){
                    $filterValues[] = $filter->getFilter()->getRequestVar() == 'price'? implode('-',
                        $filter->getValue()): $filter->getValueString();
                }
            }
        }
        $filterValue = implode(',', $filterValues);
        $query = [$this->getFilter()->getRequestVar() => $filterValue];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return $this->_url->getUrl('*/*/*', $params);
    }
}