<?php
/**
 * Created by PhpStorm.
 * User: ashish
 * Date: 1/4/16
 * Time: 6:29 PM
 */
namespace CzoneTech\ImprovedCatalogSearch\Plugin;



class Attribute{

    /*public function aroundApply(\Magento\Catalog\Model\Layer\Filter\Attribute $subject, \Closure
    $method, \Magento\Framework\App\RequestInterface $request){
        $filters = $request->getParam($subject->getRequestVar());
        if ($filters === null) {
            return $subject;
        }
        if(!is_array($filters)){
            $filters = [$filters];
        }
        foreach($filters as $filter){
            $text = $subject->getAttributeModel()->getFrontend()->getOption($filter);
            if ($filter && strlen($text)) {
                $subject->_getResource()->applyFilterToCollection($subject, $filter);
                $subject->getLayer()->getState()->addFilter($subject->_createItem($text, $filter));
                $subject->setItems([]);
            }
        }

        return $subject;
    }*/
}