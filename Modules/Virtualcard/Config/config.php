<?php

return [
    'name' => 'Virtualcard',
    'item_id' => 't7hufn2qru',
    'options' => [
        ['label' => __('Preference'), 'url' => url(env('ADMIN_PREFIX') . '/virtualcard/preferences/create ')]
    ],
    'payment_methods' => [
        'virtualcard' => ['Stripe']
    ],
    'transaction_types' => defined('Virtualcard_Withdrawal') && defined('Virtualcard_Topup') ? [Virtualcard_Withdrawal, Virtualcard_Topup] : [],
    'transaction_type_settings' => [
        'web' => [
            'sent' =>  defined('Virtualcard_Topup') ? [Virtualcard_Topup] : [],
            'received' => defined('Virtualcard_Withdrawal') ? [Virtualcard_Withdrawal] : [],
        ],
        'mobile' => [
            'sent' => [
                'Virtualcard_Topup' => defined('Virtualcard_Topup') ? Virtualcard_Topup : ''
            ],
            'received' => [
                'Virtualcard_Withdrawal' => defined('Virtualcard_Withdrawal') ? Virtualcard_Withdrawal : '',
            ]
        ],
    ],
    'permission_group' => ['Virtual Card Holder', 'Virtual Card', 'Virtual Card Withdrawal', 'Virtual Card Topup', 'Virtual Card Category', 'Virtual Card Fees Limit', 'Virtual Card Preference', 'Virtual Card Provider'],
    'menu_config' => [
        'name' => __('Virtual Card'),
        'label' => 'Virtual Card',
        'icon' => megaMenuSvg('virtualcard'),
        'route' => 'login',
        'condition' => (moduleExists('ManualVirtualcard') && isActive('ManualVirtualcard')) || (moduleExists('StripeVirtualcard') && isActive('StripeVirtualcard'))
    ],
];
