<?php
namespace CzoneTech\ImprovedCatalogSearch\Model\Plugin;

class FilterRenderer extends \Magento\Swatches\Model\Plugin\FilterRenderer
{

    /**
     * Path to RenderLayered Block
     *
     * @var string
     */
    protected $block = 'CzoneTech\ImprovedCatalogSearch\Block\LayeredNavigation\RenderLayered';

}