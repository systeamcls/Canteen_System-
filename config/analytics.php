<?php

return [
    'dashboard' => [
        'refresh_interval' => 30, // seconds
        'default_date_range' => 7, // days
        'items_per_chart' => 10,
    ],
    
    'reports' => [
        'logo_path' => 'images/logo.png',
        'company_name' => 'Kaja Canteen Management System',
        'address' => 'Your Business Address',
        'phone' => 'Your Contact Number',
    ],
    
    'kpis' => [
        'sales_target_daily' => 10000, // PHP
        'orders_target_daily' => 50,
        'compliance_threshold' => 80, // percentage
    ],
    
    'colors' => [
        'primary' => '#667eea',
        'success' => '#10B981',
        'warning' => '#F59E0B', 
        'danger' => '#EF4444',
        'info' => '#3B82F6',
    ],
];