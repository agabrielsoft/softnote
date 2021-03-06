<?php
namespace Matiere;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controllers' => [
        'factories' => [
            Controller\MatiereController::class => InvokableFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'matiere' => [
                'type'    => Literal::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/matiere',
                    'defaults' => [
                        'controller'    => Controller\MatiereController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
            'matiere' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/matiere[/:action]',
                    'defaults' => [
                        'controller'    => Controller\MatiereController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            
            'matiere' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/matiere[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\MatiereController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
            'matiere' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/matiere',
                    'defaults' => [
                        'controller' => Controller\MatiereController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
             'matiere' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/matiere[/:action][/:id][/page/:page][/order_by/:order_by][/:order][/search_by/:search_by]',
                    'constraints' =>[
                        'action'    => '(?!\bpage\b)(?!\border_by\b)(?!\bsearch_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ],
                    'defaults' => [
                        'controller' => Controller\MatiereController::class,
                        'action'     => 'search',
                    ],
                ],
            ],
             'matiere' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/matiere[/:action][/:id][/page/:page][/order_by/:order_by][/:order][/search_by/:search_by]',
                    'constraints' =>[
                        'action'    => '(?!\bpage\b)(?!\border_by\b)(?!\bsearch_by\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ],
                    'defaults' => [
                        'controller' => Controller\MatiereController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    
    
    
     'access_filter' => [
        'controllers' => [
            Controller\MatiereController::class => [
                // Give access to "resetPassword", "message" and "setPassword" actions
                // to anyone
                // Give access to "index", "add", "edit", "view", "changePassword" actions to authorized users only.
                ['actions' => ['index', 'afficherMatiereClasse','affecterMatiereClasse','matiere', 'afficherMatiereNotInClasse','desaffecter','affecter','add','addLibeleDiscipline','edit','editd','view','delete','deleted','confirm','viewd'], 'allow' => '+user.manage']
            ],
            //Controller\RegistrationController::class => [
                // Give access to "resetPassword", "message" and "setPassword" actions
                // to anyone
                // Give access to "index", "add", "edit", "view", "changePassword" actions to authorized users only.
               // ['actions' => ['index', 'review'], 'allow' => '+user.manage']
            //],
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\MatiereController::class => Controller\Factory\MatiereControllerFactory::class
           
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\MatiereManager::class => Service\Factory\MatiereManagerFactory::class,
              
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],  
     'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => ['ViewJsonStrategy',],
    ],
];
