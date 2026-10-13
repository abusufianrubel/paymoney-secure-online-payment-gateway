<?php

$adminPrefix = env('ADMIN_PREFIX');

return [
    'name' => 'Addons',

    'options' => [
        ['label' => 'Settings', 'url' => '#']
    ],

    'route_group' => [
        'authenticated' => [
            'admin' => [
                'prefix' => $adminPrefix,
                'middleware' => ['guest:admin', 'locale', 'ip_middleware']
            ],
            'user' => [
                'middleware' => ['guest:users', 'locale', 'twoFa', 'check-user-inactive']
            ]
        ],
        'unauthenticated' => [
            'admin' => [
                'prefix' => $adminPrefix,
                'middleware' => ['no_auth:admin', 'locale', 'ip_middleware']
            ],
            'user' => [
                'middleware' => ['no_auth:users', 'locale']
            ],
        ]
    ],

    'items' => [
        'fv8wtkr1jfc' => 'CryptoExchange',
        'v33hrp9x5en' => 'MobileMoney',
        'azv5h23qhwe' => 'Referral',
        'xnucugaeu6q' => 'Remittance',
        'zlwds4xzwjk' => 'Shop',
        'y6udqa9sf3v' => 'EventTicket',
        'wr6h7efkefa' => 'Agent',
        'f9t4qeh9dmq' => 'PaymentLink',
        'hxrku28ngdp' => 'Woocommerce',
        'mn7hifa2ruq' => 'Investment',
        '8s1i1pwlx3l' => 'PrestaShop',
        'o0pn10eacrt' => 'OpenCart',
        'mn4haf2ir7a' => 'Donation',
        'a2amrhf1obs' => 'PerfexCrm',
        'fv5x6zel8mh' => 'Escrow',
        'prsxrpeaxzi' => 'P2PXchange',
        'yapel5iforp' => 'PayMe',
        'k5cisv7rhf1' => 'AmlbotVerification',
        't7hufn2qru' => 'ManualVirtualcard',
        'fk4bz72yat' => 'StripeVirtualcard',
        'zi9ltc5bn1y' => 'Invoice',
        'n33j1d2qru' => 'AirtimeRecharge',
    ],

    'items_version' => [
        'CryptoExchange' => '2.2.8',
        'MobileMoney'    => '1.0.0',
        'Referral'       => '1.0.0',
        'Remittance'     => '1.0.0',
        'Shop'           => '1.0.0',
        'EventTicket'    => '1.0.0',
        'Agent'          => '1.1.5',
        'PaymentLink'    => '1.1.2',
        'Woocommerce'    => '2.1.0',
        'Investment'     => '2.1.7',
        'PrestaShop'     => '1.0.0',
        'OpenCart'       => '1.1.0',
        'Donation'       => '1.1.0',
        'PerfexCrm'      => '1.1.0',
        'Escrow'         => '1.0.0',
        'P2PXchange'     => '1.0.0',
        'PayMe'          => '1.0.0',
        'ManualVirtualcard'  => '1.0.0',
        'AmlbotVerification' => '1.0.0',
        'StripeVirtualcard' => '1.0.0',
        'Invoice' => '1.0.0',
        'AirtimeRecharge' => '1.0.0',
    ],

    'cache_keys' => [
        'paymoney_cache-preferences',
        'paymoney_cache-settings'
    ],

    'file_permission' => 755
];
