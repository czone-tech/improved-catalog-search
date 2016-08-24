<?php
/**
 * Created by PhpStorm.
 * User: ashish
 * Date: 24/8/16
 * Time: 3:32 PM
 */

namespace CzoneTech\ImprovedCatalogSearch\Block\LayeredNavigation;

use \Magento\CatalogSearch\Model\Layer\Filter\Price;
use Magento\Framework\View\Element\Template;

class PriceFilterRenderer extends Template
{

    /**
     * @var \Magento\CatalogSearch\Model\Layer\Filter\Price
     */
    protected $_filter;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'CzoneTech_ImprovedCatalogSearch::layer/price_filter.phtml';

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = [])
    {
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * @param Price $filter
     * @return string
     */
    public function setFilter(Price $filter)
    {
        $this->_filter = $filter;
        return $this;
    }

    public function getCurrencySymbol(){
        return $this->_priceCurrency->getCurrencySymbol($this->_filter->getStoreId());
    }

    public function getPriceRange(){
        $range = $this->_filter->getResource()->loadPrices(100000000);
        return [floor($range[0]), ceil($range[count($range)-1])];
    }

    public function getPriceFilterUrl(){
        $query = [$this->_filter->getRequestVar() => ''];
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

}