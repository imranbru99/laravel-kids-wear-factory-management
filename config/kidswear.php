<?php

return [

    'timezone' => 'Asia/Dhaka',

    'factory' => [
        'lat' => 23.9908,
        'lng' => 90.3854,
        'radius_meters' => 250,
        'bundle_size' => 20,
        'rejection_alert_percent' => 5,
        'currency' => 'USD',
    ],

    'sizes' => [
        '0-3M', '3-6M', '6-12M', '1Y', '2Y', '3Y', '4Y', '5Y',
        '6Y', '7Y', '8Y', '9Y', '10Y', '11Y', '12Y',
    ],

    'locales' => [
        'en' => 'English',
        'bn' => 'বাংলা',
    ],

    'modules' => [
        'dashboard' => ['label' => 'Dashboard', 'enabled' => true],
        'hr' => ['label' => 'HR & Attendance', 'enabled' => true],
        'catalog' => ['label' => 'Styles & Tech Packs', 'enabled' => true],
        'orders' => ['label' => 'Orders', 'enabled' => true],
        'production' => ['label' => 'Production', 'enabled' => true],
        'inventory' => ['label' => 'Inventory', 'enabled' => true],
        'quality' => ['label' => 'Quality', 'enabled' => true],
        'procurement' => ['label' => 'Procurement', 'enabled' => true],
        'finance' => ['label' => 'Costing & Finance', 'enabled' => true],
        'maintenance' => ['label' => 'Machines', 'enabled' => true],
        'reports' => ['label' => 'Reports', 'enabled' => true],
        'website' => ['label' => 'Website', 'enabled' => true],
        'settings' => ['label' => 'Settings', 'enabled' => true],
    ],

    'roles' => [
        'super-admin' => '*',
        'factory-manager' => '*',
        'production-supervisor' => ['dashboard.view', 'orders.view', 'production.view', 'production.manage', 'quality.view', 'hr.view', 'catalog.view', 'maintenance.view', 'reports.view'],
        'quality-controller' => ['dashboard.view', 'quality.view', 'quality.manage', 'production.view', 'catalog.view', 'reports.view'],
        'inventory-manager' => ['dashboard.view', 'inventory.view', 'inventory.manage', 'procurement.view', 'procurement.manage', 'catalog.view', 'reports.view'],
        'sales' => ['dashboard.view', 'orders.view', 'orders.manage', 'catalog.view', 'catalog.manage', 'website.view', 'website.manage', 'finance.view', 'reports.view'],
        'hr' => ['dashboard.view', 'hr.view', 'hr.manage', 'reports.view'],
        'accounts' => ['dashboard.view', 'finance.view', 'finance.manage', 'orders.view', 'reports.view', 'reports.manage'],
        'viewer' => [
            'dashboard.view', 'hr.view', 'catalog.view', 'orders.view', 'production.view',
            'inventory.view', 'quality.view', 'procurement.view', 'finance.view',
            'maintenance.view', 'reports.view', 'website.view', 'settings.view',
        ],
    ],

];
