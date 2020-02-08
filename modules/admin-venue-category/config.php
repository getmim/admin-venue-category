<?php

return [
    '__name' => 'admin-venue-category',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-venue-category.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-venue-category' => ['install','update','remove'],
        'theme/admin/venue/category' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'venue-category' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminVenueCategory\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-venue-category/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminVenueCategory' => [
                'path' => [
                    'value' => '/venue/category'
                ],
                'method' => 'GET',
                'handler' => 'AdminVenueCategory\\Controller\\Category::index'
            ],
            'adminVenueCategoryEdit' => [
                'path' => [
                    'value' => '/venue/category/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminVenueCategory\\Controller\\Category::edit'
            ],
            'adminVenueCategoryRemove' => [
                'path' => [
                    'value' => '/venue/category/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminVenueCategory\\Controller\\Category::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'venue' => [
                    'label' => 'Venue',
                    'icon' => '<i class="fas fa-map-marker-alt"></i>',
                    'priority' => 0,
                    'children' => [
                        'category' => [
                            'label' => 'Category',
                            'icon'  => '<i></i>',
                            'route' => ['adminVenueCategory'],
                            'perms' => 'manage_venue_category'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.venue-category.edit' => [
                '@extends' => ['std-site-meta'],
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => true
                    ]
                ],
                'slug' => [
                    'label' => 'Slug',
                    'type' => 'text',
                    'slugof' => 'name',
                    'rules' => [
                        'required' => TRUE,
                        'empty' => FALSE,
                        'unique' => [
                            'model' => 'VenueCategory\\Model\\VenueCategory',
                            'field' => 'slug',
                            'self' => [
                                'service' => 'req.param.id',
                                'field' => 'id'
                            ]
                        ]
                    ]
                ],
                'content' => [
                    'label' => 'About',
                    'type' => 'summernote',
                    'rules' => []
                ],
                'meta-schema' => [
                    'options' => ['ItemList' => 'ItemList']
                ]
            ],
            'admin.venue-category.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ]
        ]
    ]
];