<?php

$arr = [
    'dashboard' => [
        'label' => 'Dashboard',
        'access' => [
            'view' => ['admin.dashboard'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],
  'language' => [
        'label' => 'Language Change',
        'access' => [
            'view' => ['lang.switch'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],
    'manage_staff' => [
        'label' => 'Manage Staff',
        'access' => [
            'view' => ['admin.users'],
            'add' => ['admin.storeStaff'],
            'edit' => ['admin.updateStaff'],
            'delete' => [],
        ],
    ],

    'manage_location' => [
        'label' => 'Manage Location',
        'access' => [
            'view' => ['admin.location'],
            'add' => ['admin.users.location.add'],
            'edit' => ['admin.location.update'],
            'delete' => ['admin.users.location.delete'],
        ],
    ],

  'roles_category' => [
        'label' => 'Roles Category',
        'access' => [
            'view' => ['admin.rolescategory'],
            'add' => ['admin.roles.add' , 'admin.roles.copy'],
            'edit' => ['admin.roles.update'],
            'delete' => ['admin.roles.delete'],
        ],
    ],

 'roles_and_permission' => [
        'label' => 'Assign Permission To Role',
        'access' => [
            'view' => [],
            'add' => ['admin.roles_and_permission'],
            'edit' => [],
            'delete' => [],
        ],
    ],


    // 'parent_group' => [
    //     'label' => 'Parent Group',
    //     'access' => [
    //         'view' => ['admin.parant'],
    //         'add' => [],
    //         'edit' => [],
    //         'delete' => [],
    //     ],
    // ],


    'work_board' => [
        'label' => 'Work Board',
        'access' => [
            'view' => ['admin.workboard'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'telegram_group' => [
        'label' => 'Telegram Group',
        'access' => [
            'view' => ['admin.groups'],
            'add' => ['admin.add'],
            'edit' => ['admin.update'],
            'delete' => ['admin.delete'],
        ],
    ],

    'identify_form' => [
        'label' => 'Identity Form',
        'access' => [
            'view' => ['admin.identify-form'],
            'add' => [],
            'edit' => [
                'admin.identify-form.store',
                'admin.identify-form.action',
            ],
            'delete' => [],
        ],
    ],

    'manage_game' => [
        'label' => 'Manage Game Module',
        'access' => [
            'view' => [
                'admin.listCategory',
                'admin.listTournament',
                'admin.listTeam',
                'admin.listMatch',
                'admin.infoMatch',
                'admin.addQuestion',
                'admin.optionList',
            ],
            'add' => [
                'admin.storeCategory',
                'admin.updateCategory',
                'admin.deleteCategory',
                'admin.storeTournament',
                'admin.storeTeam',
                'admin.storeMatch',
                'admin.storeQuestion',
                'admin.optionAdd',
            ],
            'edit' => [
                'admin.updateTournament',
                'admin.updateTeam',
                'admin.updateMatch',
                'admin.locker',
                'admin.updateQuestion',
                'admin.optionUpdate',

            ],
            'delete' => [
                'admin.deleteTournament',
                'admin.deleteTeam',
                'admin.deleteMatch',
                'admin.deleteQuestion',
                'admin.optionDelete',
            ],
        ],
    ],

    'manage_result' => [
        'label' => 'Manage Result',
        'access' => [
            'view' => [
                'admin.resultList.pending',
                'admin.resultList.complete',
                'admin.searchResult',
                'admin.resultWinner',
                'admin.betUser',
            ],
            'add' => [],
            'edit' => [
                'admin.makeWinner',
                'admin.refundQuestion',
            ],
            'delete' => [],
        ],
    ],

    'commission_setting' => [
        'label' => 'Commission Setting',
        'access' => [
            'view' => [
                'admin.referral-commission',
            ],
            'add' => [],
            'edit' => [
                'admin.referral-commission.store',
                'admin.referral-commission.action',
            ],
            'delete' => [],
        ],
    ],

    'all_transaction' => [
        'label' => 'All Transaction',
        'access' => [
            'view' => [
                'admin.transaction',
                'admin.transaction.search',
                'admin.commissions',
                'admin.commissions.search',
                'admin.bet-history',
                'admin.searchBet',
            ],
            'add' => [],
            'edit' => [
                'admin.refundBet',
            ],
            'delete' => [],
        ],
    ],

    // 'user_management' => [
    //     'label' => 'User Management',
    //     'access' => [
    //         'view' => [
    //             'admin.users',
    //             'admin.users.search',
    //             'admin.email-send',
    //             'admin.user.transaction',
    //             'admin.user.fundLog',
    //             'admin.user.withdrawal',
    //             'admin.user.userKycHistory',
    //             'admin.kyc.users.pending',
    //             'admin.kyc.users',
    //             'admin.user-edit',
    //         ],
    //         'add' => ['admin.user.add'],
    //         'edit' => [
    //             'admin.user-multiple-active',
    //             'admin.user-multiple-inactive',
    //             'admin.send-email',
    //             'admin.user.userKycHistory',
    //             'admin.user-balance-update',
    //         ],
    //         'delete' => ['admin.users'],
    //     ],
    // ],

    'payment_gateway' => [
        'label' => 'Payment Gateway',
        'access' => [
            'view' => [
                'admin.payment.methods',
                'admin.deposit.manual.index',
            ],
            'add' => [
                'admin.deposit.manual.create',
            ],
            'edit' => [
                'admin.edit.payment.methods',
                'admin.deposit.manual.edit',
            ],
            'delete' => [],
        ],
    ],

    'payment_log' => [
        'label' => 'Payment Request & Log',
        'access' => [
            'view' => [
                'admin.payment.log',
                'admin.payment.search',
            ],
            'add' => [],
            'edit' => [
                'admin.payment.action',
            ],
            'delete' => [],
        ],
    ],

    'api_payment_log' => [
        'label' => 'API Deposit Log',
        'access' => [
            'view' => [
                'admin.payment.apiLog',
                'admin.payment.apisearch',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'deposit_report' => [
        'label' => 'Deposit Reports',
        'access' => [
            'view' => [
                'admin.payment.report',
                'admin.payment.report.search',
                'admin.payment.report.search',
                'admin.payment.report.daily',
                'admin.payment.report.daily.search',
                'admin.payment.report.detail',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

   'deposit_last_hour_report' => [
        'label' => 'Deposit Last Hour Reports',
        'access' => [
            'view' => [
                'admin.payment.log2',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'merchant_reports' => [
        'label' => 'Merchants Reports',
        'access' => [
            'view' => [
                'admin.merchant_reports.by_date',
                'admin.merchant_reports.export_by_date',
                'admin.merchant_reports.by_name',
                'admin.merchant_reports.export_by_name',
                'admin.merchant_reports.by_month',
                'merchant_reports.export_by_month'
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'live_e_wallet_balance_report' => [
        'label' => 'Live E-Wallet Balance Report',
        'access' => [
            'view' => ['admin.reports.live_ewallet_balance'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'daily_e_wallet_summary' => [
        'label' => 'Daily E-Wallet Summary Report',
        'access' => [
            'view' => ['admin.reports.daily_ewallet_summary'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'daily_transaction_summary' => [
        'label' => 'Daily Transaction Summary Report',
        'access' => [
            'view' => ['admin.reports.daily_transection_summary'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'merchant_charges_summary_report' => [
        'label' => 'Merchant Charges Summary Report',
        'access' => [
            'view' => ['admin.reports.merchant_charges_summary'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partner_account_summary' => [
        'label' => 'Partner Account Summary Report',
        'access' => [
            'view' => ['admin.reports.partner_account_summary'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partner_account_summary' => [
        'label' => 'Partner Account Summary Report',
        'access' => [
            'view' => ['admin.reports.partner_account_summary'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partner_account_balance_summary_creation' => [
        'label' => 'Partner Account Balance Summary Creation Report',
        'access' => [
            'view' => ['admin.reports.partner_account_balance_summary'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partner_account_balance_summary_completions' => [
        'label' => 'Partner Account Balance Summary Completions Report',
        'access' => [
            'view' => ['admin.reports.partner_account_balance_summary_completions'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'revenue_center_report' => [
        'label' => 'Revenue Center Report',
        'access' => [
            'view' => ['admin.reports.revenue_center'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partner_balance_log' => [
        'label' => 'Partner Balance Log Report',
        'access' => [
            'view' => ['admin.reports.logs'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partner_balance_reports' => [
        'label' => 'Partner Balance Report',
        'access' => [
            'view' => ['admin.reports.cal', 'admin.reports.cal2'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'master_report' => [
        'label' => 'Master Report',
        'access' => [
            'view' => ['admin.reports.master_report'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'gateway_performance_report' => [
        'label' => 'Gateway Performance Report',
        'access' => [
            'view' => ['admin.payment.payment_gateway_report'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

   'payment_type' => [
        'label' => 'Payment Type',
        'access' => [
            'view' => ['admin.typ'],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],





    'all_reports' => [
        'label' => 'All Reports',
        'access' => [
            'view' => [
                'admin.payment.report.all',
                'admin.payment.report.all.search',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'payout_manage' => [
        'label' => 'Payout method & Log',
        'access' => [
            'view' => [
                'admin.payout-method',
                'admin.payout-log',
                'admin.payout-request',
                'admin.payout-log.search',
            ],
            'add' => [
                'admin.payout-method.create',
            ],
            'edit' => [
                'admin.payout-method.edit',
                'admin.payout-action',
            ],
            'delete' => [],
        ],
    ],

    'withdrawal_reports' => [
        'label' => 'Withdrawal Reports',
        'access' => [
            'view' => [
                'admin.payout-report',
                'admin.payout-report.search',
                'admin.payout.report.daily',
                'admin.payout.report.daily.search',
                'admin.payout.report.detail',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'e_wallet_accounts' => [
        'label' => 'E-Wallet Accounts',
        'access' => [
            'view' => [
                'admin.accounts',
                'admin.payout-report.search',
                'admin.payout.report.daily',
                'admin.payout.report.daily.search',
                'admin.payout.report.detail',
            ],
            'add' => [
                'admin.accounts.add',
            ],
            'edit' => [
                'admin.accounts.edit',
            ],
            'delete' => [
                'admin.merchant.delete',
            ],
        ],
    ],

    'e_wallet_accounts_test' => [
        'label' => 'E-Wallet Accounts Test',
        'access' => [
            'view' => [
                'admin.ewallet.accounts',

            ],
            'add' => [
                'admin.ewallet.accounts.add',
            ],
            'edit' => [
                'admin.deposit.test',
                'admin.deposit.testp',
                'admin.withdrawal.test',
                'admin.withdrawal.testp',
                'admin.e_wallet_accounts.toggle_status',
            ],
            'delete' => [
                'admin.ewallet.accounts.delete',
            ],
        ],
    ],

    // 'account_balance_logs' => [
    //     'label' => 'E-Wallet Account Balance Logs',
    //     'access' => [
    //         'view' => [
    //             'admin.balance.logs',
    //             'admin.balance.logs.search',
    //         ],
    //         'add' => [],
    //         'edit' => [],
    //         'delete' => [],
    //     ],
    // ],

    'account_management' => [
        'label' => 'Account Management',
        'access' => [
            'view' => [
                'admin.ewallet.accounts.details',
            ],
            'add' => ['admin.account_management.add_account'],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'account_group' => [
        'label' => 'Account Group',
        'access' => [
            'view' => [
                'admin.account_management.account_group',
            ],
            'add' => ['admin.accounts.addpairs'],
            'edit' => ['admin.accounts.updateGroup'],
            'delete' => [],
        ],
    ],


    'gateways' => [
        'label' => 'Account Management Gateways',
        'access' => [
            'view' => [
                'admin.account_management.gateway',
            ],
            'add' => ['admin.deposit.accounts.store'],
            'edit' => ['admin.deposit.accounts.update'],
            'delete' => [],
        ],
    ],

    'categories' => [
        'label' => 'Account Management Categories',
        'access' => [
            'view' => [
                'admin.account_management.add_category',
            ],
            'add' => ['admin.category.store'],
            'edit' => ['admin.category.update'],
            'delete' => ['admin.category.delete'],
        ],
    ],

    'commission_category' => [
        'label' => 'Commission Category',
        'access' => [
            'view' => ['admin.commission.categories.index',],
            'add' => ['admin.commission.categories.store'],
            'edit' => ['admin.commission.categories.update'],
            'delete' => ['admin.commission.categories.destroy'],
        ],
    ],

    'settings' => [
        'label' => 'Personal & Merchant Timming Setting',
        'access' => [
            'view' => [
                'admin.settings.edit',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partners' => [
        'label' => 'Partners (API Keys)',
        'access' => [
            'view' => [
                'admin.apis',
            ],
            'add' => [
                'admin.apis.add',
            ],
            'edit' => [
                'admin.apis.addByParent',
                'admin.apis.reset',
                'admin.apis.commission',
                'admin.apis.commission.add',
                'admin.apis.commission2',
                'admin.apis.commission2.add',
                'admin.apis.balance.add',
                'admin.apis.update',
            ],
            'delete' => [
                'admin.apis.delete',
            ],
        ],
    ],


    'merchant_accounts' => [
        'label' => "Merchant Accounts",
        'access' => [
            'view' => [
                'admin.merchant_accounts',
            ],
            'add' => [
                'admin.merchant_accounts.add',
            ],
            'edit' => [
                'admin.merchant_accounts.update',
            ],
            'delete' => [
                'admin.merchant_accounts.delete',
            ],
        ],
    ],

    'agents' => [
        'label' => 'Agent Managements',
        'access' => [
            'view' => [
                'admin.agent.list',
            ],
            'add' => [

            ],
            'edit' => [

            ],
            'delete' => [

            ],
        ],
    ],


    'partner_login' => [
        'label' => 'Admin Login Partners Access',
        'access' => [
            'view' => [
                'admin.apis.login',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partnersbalance' => [
        'label' => "Add Partner's Balance",
        'access' => [
            'view' => [],
            'add' => [
                'admin.apis.balance.add.get',
                'admin.apis.balance.add',
            ],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'settlements' => [
        'label' => 'Partner Settlements',
        'access' => [
            'view' => [
                'admin.settlements',
                'admin.settlements.search',
            ],
            'add' => [],
            'edit' => [
                'admin.settlements.approve',
                'admin.settlements.reject',
            ],
            'delete' => [],
        ],
    ],

    'commissions' => [
        'label' => 'Partner Commissions',
        'access' => [
            'view' => [
                'admin.api.commissions',
                'admin.api.commissions.search',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'adjustments' => [
        'label' => 'Partner Adjustments',
        'access' => [
            'view' => [
                'admin.adjustments',
                'admin.adjustments.search',
            ],
            'add' => [],
            'edit' => [
                'admin.adjustments.approve',
            ],
            'delete' => [],
        ],
    ],

    'partner_balance' => [
        'label' => 'Partner Balance Logs',
        'access' => [
            'view' => [
                'admin.partner.balance',
                'admin.partner.balance.search',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partner_activity_logs' => [
        'label' => 'Partner Activity Logs',
        'access' => [
            'view' => [
                'admin.partner.logs',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'partner_transfer_logs' => [
        'label' => 'Partner Transfer Logs',
        'access' => [
            'view' => [
                'admin.transfer-log',
            ],
            'add' => [
                'admin.transfer-log.add',
            ],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'api_logs' => [
        'label' => 'API Logs',
        'access' => [
            'view' => [
                'admin.transections.apilogs',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

    'support_ticket' => [
        'label' => 'Support Ticket',
        'access' => [
            'view' => [
                'admin.ticket',
                'admin.ticket.view',
            ],
            'add' => [
                'admin.ticket.reply',
            ],
            'edit' => [],
            'delete' => [
                'admin.ticket.delete',
            ],
        ],
    ],
    'subscriber' => [
        'label' => 'Subscriber',
        'access' => [
            'view' => [
                'admin.subscriber.index',
                'admin.subscriber.sendEmail',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [
                'admin.subscriber.remove',
            ],
        ],
    ],

    'website_controls' => [
        'label' => 'Website Controls',
        'access' => [
            'view' => [
                'admin.basic-controls',
                'admin.email-controls',
                'admin.email-template.show',
                'admin.sms.config',
                'admin.sms-template',
                'admin.notify-config',
                'admin.notify-template.show',
                'admin.notify-template.edit',
                'admin.plugin.config',
                'admin.tawk.control',
                'admin.fb.messenger.control',
                'admin.google.recaptcha.control',
                'admin.google.analytics.control',
            ],
            'add' => [],
            'edit' => [
                'admin.basic-controls.update',
                'admin.email-controls.update',
                'admin.email-template.edit',
                'admin.sms-template.edit',
                'admin.notify-config.update',
                'admin.notify-template.update',
            ],
            'delete' => [],
        ],
    ],
    'language_settings' => [
        'label' => 'Language Settings',
        'access' => [
            'view' => [
                'admin.language.index',
            ],
            'add' => [
                'admin.language.create',
            ],
            'edit' => [
                'admin.language.edit',
                'admin.language.keywordEdit',
            ],
            'delete' => [
                'admin.language.delete',
            ],
        ],
    ],
    'theme_settings' => [
        'label' => 'Theme Settings',
        'access' => [
            'view' => [
                'admin.manage.theme',
                'admin.logo-seo',
                'admin.breadcrumb',
                'admin.template.show',
                'admin.content.index',
            ],
            'add' => [
                'admin.content.create',
            ],
            'edit' => [
                'admin.logoUpdate',
                'admin.seoUpdate',
                'admin.breadcrumbUpdate',
                'admin.content.show',
            ],
            'delete' => [
                'admin.content.delete',
            ],
        ],
    ],

    'ewallet_transfer_balance' => [
        'label' => 'E-Wallet Transfer Balance',
        'access' => [
            'view' => [
                'admin.transfer.balance',
            ],
            'add' => [
                'admin.transfer.balance.add',
            ],
            'edit' => [],
            'delete' => [],
        ],
    ],
    'view_partner_account' => [
        'label' => 'View Partner Account',
        'access' => [
            'view' => [
                'can_view_partner_account',
            ],
            'add' => [],
            'edit' => [],
            'delete' => [],
        ],
    ],

];

return $arr;
