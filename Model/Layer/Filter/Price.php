<?php
/**
 * Created by PhpStorm.
 * User: ashish
 * Date: 16/8/16
 * Time: 6:57 PM
 */

namespace CzoneTech\ImprovedCatalogSearch\Model\Layer\Filter;


class Price extends \Magento\Catalog\Model\Layer\Filter\Price
{

    protected $dataProvider;

    protected $algorithmFactory;

    protected $intervals = [];

    /**
     * @param ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param Dynamic\AlgorithmFactory $algorithmFactory
     * @param DataProvider\PriceFactory $dataProviderFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \CzoneTech\ImprovedCatalogSearch\Model\ResourceModel\Layer\Filter\PriceFactory
        $improvedFilterAttributeFactory,
        \CzoneTech\ImprovedCatalogSearch\Model\Layer\Filter\ItemFactory $improvedFilterItemFactory,
        array $data = []
    ) {

        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $resource, $customerSession,
            $priceAlgorithm, $priceCurrency, $algorithmFactory, $dataProviderFactory, $data);
        $this->_resource = $improvedFilterAttributeFactory->create();
        $this->_filterItemFactory = $improvedFilterItemFactory;
        $this->dataProvider = $dataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->algorithmFactory = $algorithmFactory;
    }

    /**
     * Apply price range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filters = $request->getParam($this->getRequestVar());
        if(is_array($filters)){
            foreach($filters as $filter){
                //validate filter

                $filter = $this->dataProvider->validateFilter($filter);
                if (!$filter) {
                    return $this;
                }

                list($from, $to) = $filter;

                $this->dataProvider->setInterval([$from, $to]);

                $this->intervals[] = $this->dataProvider->getInterval();

                $priorFilters = $this->dataProvider->getPriorFilters($filters);
                if ($priorFilters) {
                    $this->dataProvider->setPriorIntervals($priorFilters);
                }


                $this->getLayer()
                    ->getState()
                    ->addFilter(
                        $this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
                    );

            }
        }

        $this->_applyPriceRange();

        return $this;
    }

    /**
     * Prepare text of range label
     *
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return float|\Magento\Framework\Phrase
     */
    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $formattedFromPrice = $this->priceCurrency->format($fromPrice);
        if ($toPrice === '') {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()
        ) {
            return $formattedFromPrice;
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }

            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
    }



    /**
     * Get data for build price filter items
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getItemsData()
    {
        //Since we do not want 'price filter' to be applied when ProductCollection finds out the max price, min price,
        //and standard deviation, we are actually removing price filters and reapplying them later
        //@todo a better place to put this may be in a plugin (pre/post process)
        $collection = $this->getLayer()->getProductCollection();
        $select = $collection->getSelect();

        // processing WHERE part
        $wherePart = $select->getPart(\Magento\Framework\DB\Select::WHERE);
        $newWherePart = [];

        foreach ($wherePart as $key => $wherePartItem) {
            if(!stristr($wherePartItem, '.min_price')){
                $newWherePart[$key] = $wherePartItem;
            }
        }
        $select->setPart(\Magento\Framework\DB\Select::WHERE, $newWherePart);

        $algorithm = $this->algorithmFactory->create();

        $items = $algorithm->getItemsData();
        $select->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
        return $items;
    }
}