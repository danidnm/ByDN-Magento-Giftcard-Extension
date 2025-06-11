<?php
/**
 * @package     Bydn_Giftcard
 * @author      Daniel Navarro <https://github.com/danidnm>
 * @license     GPL-3.0-or-later
 * @copyright   Copyright (c) 2025 Daniel Navarro
 *
 * This file is part of a free software package licensed under the
 * GNU General Public License v3.0.
 * You may redistribute and/or modify it under the same license.
 */

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
