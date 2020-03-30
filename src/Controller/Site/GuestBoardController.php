<?php

namespace Favorite\Controller\Site;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class GuestBoardController extends AbstractActionController
{
    public function indexAction()
    {
        $params = $this->params()->fromRoute();
        $params['action'] = 'show';
        return $this->forward()->dispatch(__CLASS__, $params);
    }

    public function showAction()
    {
       
        $site = $this->currentSite();

        $user = $this->identity();
        $query = $this->params()->fromQuery();
        $query['user_id'] = $user->getId();

        //$this->setBrowseDefaults('created');
        $response = $this->api()->search('favorite_item', $query);
        $this->paginator($response->getTotalResults());
        $favoritelist = $response->getContent();
        
        $favoriteitem = [];
        foreach ($favoritelist as $favorite) {
            $id = $favorite->id();
            $itemid =$favorite->item()->getId();
            
            $query = ['id' => $itemid, 'site_id' => $site];
            $res = $this->api()->read('items',$itemid)->getContent();
            
            array_push($favoriteitem,$res);
        }
        
        
        $view = new ViewModel;
        return $view
            ->setTemplate('guest/site/guest/favorite')
            ->setVariable('site', $site)
            ->setVariable('resources', $favoriteitem);
    }
}
