<?php

namespace Bydn\Giftcard\Block\Adminhtml\Giftcard\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    private RequestInterface $request;

    /**
     * DeleteButton constructor.
     * @param Context $context
     * @param Registry $registry
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RequestInterface $request
    ) {
        parent::__construct($context, $registry);
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $giftcardId = (int)$this->request->getParam('id');
        $data = [];

        if ($giftcardId !== 0) {
            $data = [
                'label' => __('Delete Giftcard'),
                'class' => 'delete',
                'on_click' => "deleteConfirm('" .__('Are you sure you want to delete this giftcard?') ."', '"
                    . $this->getDeleteUrl($giftcardId) . "', {data: {}})",
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @param int $giftcardId
     * @return string
     */
    public function getDeleteUrl(int $giftcardId): string
    {
        return $this->getUrl('*/*/delete', ['id' => $giftcardId]);
    }
}
