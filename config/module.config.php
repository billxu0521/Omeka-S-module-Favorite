<?php
namespace Favorite;

return  [
     'api_adapters' => [
        'invokables' => [
            'favorite_item' => Api\Adapter\FavoriteAdapter::class,
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'linkFavorite' => View\Helper\LinkFavorite::class,
        ],
       ],
    
    'controllers' => [
        'invokables' => [
            'Favorite\Controller\Site\FavoriteList' => Controller\Site\FavoriteListController::class,
            'Favorite\Controller\Site\GuestBoard' => Controller\Site\GuestBoardController::class,
        ],
    ],
    
    'router' => [
        'routes' => [
            'site' => [
                'child_routes' => [
                    'favorite' => [
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/favorite[/:action]',
                            'constraints' => [
                                'action' => 'add|browse',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Favorite\Controller\Site',
                                'controller' => 'FavoriteList',
                                'action' => 'browse',
                            ],
                        ],
                    ],
                    'favorite-id' => [
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/favorite/:id[/:action]',
                            'constraints' => [
                                'action' => 'delete',
                                'id' => '\d+',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Favorite\Controller\Site',
                                'controller' => 'FavoriteList',
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'guest' => [
                        'type' => \Zend\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/guest',
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'favorite' => [
                                'type' => \Zend\Router\Http\Literal::class,
                                'options' => [
                                    'route' => '/favorite',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Favorite\Controller\Site',
                                        'controller' => 'GuestBoard',
                                        'action' => 'show',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'js_translate_strings' => [
        'No query is set.', // @translate
        'Your search is saved.', // @translate
        'You can find it in your account.', // @translate
    ],
    'blocksdisposition' => [
        'item_set_browse' => [
            'Favorite',
        ],
        'item_browse' => [
            'Favorite',
        ],
        'media_browse' => [
            'Favorite',
        ],
    ],
    
    ];
