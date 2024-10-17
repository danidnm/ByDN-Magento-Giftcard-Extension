<?php

namespace Bydn\Giftcard\Block\Adminhtml\Giftcard\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;

class SendButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * DeleteButton constructor.
     *
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
     * Returns delete button config if needed
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $giftcardId = (int)$this->request->getParam('id');
        $data = [];

        if ($giftcardId !== 0) {
            $data = [
                'label' => __('Send Giftcard Email'),
                'class' => 'send',
                'on_click' => "deleteConfirm('" .__('Are you sure you want to send this giftcard email?') ."', '"
                    . $this->getSendUrl($giftcardId) . "', {data: {}})",
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * Returns URL for giftcard deletion
     *
     * @param int $giftcardId
     * @return string
     */
    public function getSendUrl(int $giftcardId): string
    {
        return $this->getUrl('*/*/send', ['id' => $giftcardId]);
    }
}
