<?php

namespace CzoneTech\ImprovedCatalogSearch\Plugin;

class FilterRenderer
{

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Path to RenderLayered Block
     *
     * @var string
     */
    protected $block;

    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        $block
    ) {
        $this->layout = $layout;
        $this->block = $block;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRender(
        \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
    ) {
        if ($filter->getRequestVar() == 'price') {

                $block = $this->layout->createBlock($this->block);
                return $block->setFilter($filter)->toHtml();

        }
        return $proceed($filter);
    }

}