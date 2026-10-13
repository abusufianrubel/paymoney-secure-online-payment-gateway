<?php

if (!app()->runningInConsole()) {
    return [
        'name' => 'TatumIo',
        'tatum_api_url' => 'https://api.tatum.io',
        'tatum_api_version' => 'v3',
        'transaction_types' => (defined('Crypto_Sent') && defined('Crypto_Received') && defined('Token_Sent') && defined('Token_Received')) ? [Crypto_Sent, Crypto_Received, Token_Sent, Token_Received] : [],
        'permission_group' => ['Crypto Token', 'Token Transactions', 'Token Send/Receive', 'Crypto Asset', 'Crypto Transactions', 'Crypto Send/Receive'],
        'transaction_type_settings' => [
            'web' => [
                'sent' => (defined('Crypto_Sent') && defined('Token_Sent')) ? [Crypto_Sent, Token_Sent] : [],
                'received' => (defined('Crypto_Received') && defined('Token_Received')) ? [Crypto_Received, Token_Received] : [],
            ],
            'mobile' => [
                'sent' => [
                    'Crypto_Sent' => defined('Crypto_Sent') ? Crypto_Sent : '',
                    'Token_Sent' => defined('Token_Received') ? Token_Sent : '',
                ],
                'received' => [
                    'Crypto_Received' => defined('Crypto_Received') ? Crypto_Received : '',
                    'Token_Received' => defined('Token_Received') ? Token_Received : '',
                ]
            ]
        ],

        'transaction_list' => [
            'sender' => (defined('Crypto_Sent') && defined('Crypto_Received') && defined('Token_Sent') && defined('Token_Received'))
                ? [Crypto_Sent => 'user', Crypto_Received => 'end_user', Token_Sent => 'user', Token_Received => 'end_user']
                : [],
            'receiver' => (defined('Crypto_Sent') && defined('Crypto_Received') && defined('Token_Sent') && defined('Token_Received'))
                ? [Crypto_Sent => 'end_user', Crypto_Received => 'user', Token_Sent => 'end_user', Token_Received => 'user']
                : [],
        ]


    ];
} else {
    return [
        'name' => 'TatumIo',
        'permission_group' => ['Crypto Token', 'Token Transactions', 'Token Send/Receive', 'Crypto Asset', 'Crypto Transactions', 'Crypto Send/Receive'],
    ];
}
