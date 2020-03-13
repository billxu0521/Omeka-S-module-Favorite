<?php
namespace Favorite;

if (!class_exists(\Generic\AbstractModule::class)) {
    require file_exists(dirname(__DIR__) . '/Generic/AbstractModule.php')
        ? dirname(__DIR__) . '/Generic/AbstractModule.php'
        : __DIR__ . '/src/Generic/AbstractModule.php';
}

use Generic\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Search History.
 *
 * @copyright Daniel Berthereau 2019-2020
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 */
class Module extends AbstractModule
{
    const NAMESPACE = __NAMESPACE__;

    protected $dependency = 'Guest';

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        $acl = $this->getServiceLocator()->get('Omeka\Acl');

        $roles = $acl->getRoles();
        $acl
            ->allow(
                $roles,
                [
                    //Entity\Favorite::class,
                    Api\Adapter\FavoriteAdapter::class,
                    'Favorite\Controller\Site\FavoriteList',
                    'Favorite\Controller\Site\GuestBoard',
                ]
        );
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Controller\Site\Item',
            'view.show.after',
            [$this, 'handleViewShowAfterItem'],
                +100
        );
        
    }

    public function handleViewShowAfterItem(Event $event)
    {
        
        $view = $event->getTarget();
        $resource = $view->resource;
        echo $view->partial('common/favorite', ['resource' => $resource]);
        //echo $event->getTarget()->linkFavorite();
    }

    public function handleGuestWidgets(Event $event)
    {
        $helpers = $this->getServiceLocator()->get('ViewHelperManager');
        $translate = $helpers->get('translate');
        $partial = $helpers->get('partial');

        $widget = [];
        $widget['label'] = $translate('Favorite'); // @translate
        $widget['content'] = $partial('guest/site/guest/widget/favorite-list');

        $widgets = $event->getParam('widgets');
        $widgets['favorite-list'] = $widget;
        $event->setParam('widgets', $widgets);
    }
}
