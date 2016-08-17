<?php
/**
 * Created by PhpStorm.
 * User: ashish
 * Date: 16/8/16
 * Time: 7:03 PM
 */

namespace CzoneTech\ImprovedCatalogSearch\Model\ResourceModel\Layer\Filter;


class Price extends \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price
{

    /**
     * Apply price range filter to product collection
     *
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @param mixed $interval
     * @return $this
     */
    public function applyPriceRange(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter, $intervals)
    {
        if (!$intervals) {
            return $this;
        }
        if(!is_array($intervals)){
            $intervals = [$intervals];
        }
        $wheres = [];

        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $priceExpr = $this->_getPriceExpression($select, false);

        foreach($intervals as $interval){
            list($from, $to) = $interval;
            if ($from === '' && $to === '') {
                return $this;
            }

            if ($to !== '') {
                $to = (double)$to;
                if ($from == $to) {
                    $to += self::MIN_POSSIBLE_PRICE;
                }
            }
            $where = [];
            if ($from !== '') {
                $where[] = $priceExpr . ' >= ' . $this->_getComparingValue($from);
            }
            if ($to !== '') {
                $where[] = $priceExpr . ' < ' . $this->_getComparingValue($to);
            }
            $wheres[] = implode(' AND ', $where);
        }

        $condition = '('. implode(') OR (', $wheres) .')';
        $select->where($condition);
        return $this;
    }


    /**
     * Retrieve array with products counts per price range
     *
     * @param int $range
     * @return array
     */
    public function getCount($range)
    {
        $select = $this->_getSelect();
        // processing WHERE part
        $wherePart = $select->getPart(\Magento\Framework\DB\Select::WHERE);
        $newWherePart = [];

        foreach ($wherePart as $key => $wherePartItem) {
            if(!stristr($wherePartItem, '.min_price')){
                $newWherePart[$key] = $wherePartItem;
            }
        }
        $select->setPart(\Magento\Framework\DB\Select::WHERE, $newWherePart);


        $priceExpression = $this->_getFullPriceExpression($select);

        /**
         * Check and set correct variable values to prevent SQL-injections
         */
        $range = floatval($range);
        if ($range == 0) {
            $range = 1;
        }
        $countExpr = new \Zend_Db_Expr('COUNT(*)');
        $rangeExpr = new \Zend_Db_Expr("FLOOR(({$priceExpression}) / {$range}) + 1");

        $select->columns(['range' => $rangeExpr, 'count' => $countExpr]);
        $select->group($rangeExpr)->order("({$rangeExpr}) ASC");

        return $this->getConnection()->fetchPairs($select);
    }
}