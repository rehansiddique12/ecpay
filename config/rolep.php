<?php

$arr = [
    'dashboard' => [
        'label' => "Dashboard",
        'access' => [
            'view' => ['partner.dashboard'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],
    'manage_staff' => [
        'label' => "Manage Staff",
        'access' => [
            'view' => ['partner.staff'],
            'add' => ['partner.storeStaff'],
            'edit' => ['partner.updateStaff'],
            'delete' => ['partner.apis.delete'],
        ],
    ],


    'payment_log' => [
        'label' => "Payment Reports",
        'access' => [
            'view' => [
                'partner.payment.report',
                'partner.payment.report.search',
                'partner.payment.report.daily',
                'partner.payment.report.daily.search',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],
    
    'all_reports' => [
        'label' => "All Reports",
        'access' => [
            'view' => [
                'partner.payment.report.all',
                'partner.payment.report.all.search',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'payout_manage' => [
        'label' => "Withdrawal Requests & Log",
        'access' => [
            'view' => [
                'partner.payout-log',
                'partner.payout-request',
                'partner.payout-log.search',
            ],
            'add' => [],
            'edit' => [
                'partner.payout-action'
            ],
            'delete' => [],
        ],
    ],
    
    'payout_report' => [
        'label' => "Withdrawal Reports",
        'access' => [
            'view' => [
                'partner.payout-report',
                'partner.payout-report.search',
                'partner.payout.report.daily',
                'partner.payout.report.daily.search',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

];

return $arr;



