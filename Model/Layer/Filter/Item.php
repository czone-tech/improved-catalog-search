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
            if(!in_array($this->getValue(), $requestValue)){
                $newValue = array_merge($requestValue, [$this->getValue()]);
            }else{
                $newValue = array_diff($requestValue, [$this->getValue()]);
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
        $requestValue = $this->_request->getParam($this->getFilter()->getRequestVar());
        if($requestValue){
            $requestValue = explode(',', $requestValue);
            $newValue = array_diff($requestValue, [$this->getValue()]);
        }else{
            $newValue = $this->getFilter()->getResetValue();
        }
        $query = [
            $this->getFilter()->getRequestVar() => implode(',', $newValue)
        ];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return $this->_url->getUrl('*/*/*', $params);
    }
}