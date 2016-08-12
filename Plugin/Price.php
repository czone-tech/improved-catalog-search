<?php
/**
 * Created by PhpStorm.
 * User: ashish
 * Date: 12/8/16
 * Time: 5:37 PM
 */

namespace CzoneTech\ImprovedCatalogSearch\Plugin;


class Price
{

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\ItemFactory
     */
    protected $_filterItemFactory;

    protected $_dataProviderFactory;

    protected $dataProvider;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory
    ){
        $this->_filterItemFactory = $filterItemFactory;
        $this->_dataProviderFactory = $dataProviderFactory;
        $this->priceCurrency = $priceCurrency;
    }


    public function aroundApply(\Magento\CatalogSearch\Model\Layer\Filter\Price $subject, \Closure
    $method, \Magento\Framework\App\RequestInterface $request){
        $filters = explode(',', $request->getParam($subject->getRequestVar()));
        if ($filters === null) {
            return $subject;
        }
        $this->dataProvider = $this->_dataProviderFactory->create(['layer' => $subject->getLayer()]);

        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $subject->getLayer()
            ->getProductCollection();

        foreach($filters as $filter){
            $filterParams = explode(',', $filter);
            $filter = $this->dataProvider->validateFilter($filterParams[0]);
            if (!$filter) {
                return $this;
            }

            $this->dataProvider->setInterval($filter);
            $priorFilters = $this->dataProvider->getPriorFilters($filterParams);
            if ($priorFilters) {
                $this->dataProvider->setPriorIntervals($priorFilters);
            }

            list($from, $to) = $filter;

            $productCollection->addFieldToFilter(
                'price',
                ['from' => $from, 'to' =>  empty($to) || $from == $to ? $to : $to - $subject::PRICE_DELTA]
            );
            $subject->getLayer()->getState()->addFilter(
                $this->_createItem($subject, $this->_renderRangeLabel($subject, empty($from) ? 0 : $from, $to), $filter)
            );

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
    protected function _createItem(\Magento\CatalogSearch\Model\Layer\Filter\Price $filter, $label, $value, $count = 0)
    {
        return $this->_filterItemFactory->create()
            ->setFilter($filter)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }

    /**
     * Prepare text of range label
     *
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return float|\Magento\Framework\Phrase
     */
    protected function _renderRangeLabel(\Magento\CatalogSearch\Model\Layer\Filter\Price $filter, $fromPrice, $toPrice)
    {

        $formattedFromPrice = $this->priceCurrency->format($fromPrice);
        if ($toPrice === '') {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }

            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
    }
}