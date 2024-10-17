<?php

namespace Bydn\Giftcard\Model\Source\Giftcard;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Returns possible values for giftcard status field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Pending'),
                'value' => \Bydn\Giftcard\Model\Giftcard::GIFTCARD_PENDING,
            ],
            [
                'label' => __('Active'),
                'value' => \Bydn\Giftcard\Model\Giftcard::GIFTCARD_ACTIVE,
            ],
            [
                'label' => __('Used'),
                'value' => \Bydn\Giftcard\Model\Giftcard::GIFTCARD_USED,
            ],
            [
                'label' => __('Expired'),
                'value' => \Bydn\Giftcard\Model\Giftcard::GIFTCARD_EXPIRED,
            ],
            [
                'label' => __('Canceled'),
                'value' => \Bydn\Giftcard\Model\Giftcard::GIFTCARD_CANCELED,
            ],
        ];

        return $options;
    }
}
