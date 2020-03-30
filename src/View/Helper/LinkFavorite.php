<?php

namespace Favorite\View\Helper;

use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Zend\View\Helper\AbstractHelper;

class LinkFavorite extends AbstractHelper
{
    /**
     * Get the link to the search history button (to save or to delete).
     *
     * @return string
     */
    public function __invoke(AbstractResourceEntityRepresentation $resource)
    {
        $view = $this->getView();
        $resource_id = $resource->id();
        
        $site = $this->currentSite();
        $user = $view->identity();
        $searchRequest = null;
        if ($user) {
            $query = ['user_id' => $user->getId(),'site_id' => $site ? $site->id() : 0,'item_id' => $resource_id];
            if ($user) {
                $searchRequest = $view->api()->searchOne('favorite_item',$query)->getContent(); 
            }        
        }
        $showDelBtn = false;
        $favoriteId = $searchRequest->item()->getId();
        if($favoriteId == $resource_id){
            $showDelBtn = 'true';
        }else{
            $showDelBtn = 'false';
        }
        
        return $view->partial('common/favorite-button', [
            'resource' => $resource,
            'resource_id' => $resource_id,
            'searchRequest' => $searchRequest,
            'showdelbtn' => $showDelBtn,
            'user' => $user,
        ]);
    }

    /**
     * Clean a request query.
     *
     * @see \Favorite\Controller\Site\SearchRequestController::cleanQuery()
     *
     * @return string
     */
    protected function cleanQuery(AbstractResourceEntityRepresentation $resource)
    {
        $view = $this->getView();

        // Clean query for better search.
        $request = $view->params()->fromQuery();
        unset($request['csrf']);
        unset($request['page']);
        unset($request['per_page']);
        unset($request['offset']);
        unset($request['limit']);
        $request = array_filter($request, function ($v) {
            // TODO Improve cleaning of empty sub-arrays in the query.
            return (bool) is_array($v) ? !empty($v) : strlen($v);
        });

        return http_build_query($request);
    }

    /**
     * Get the current site from the view.
     *
     * @return \Omeka\Api\Representation\SiteRepresentation|null
     */
    protected function currentSite()
    {
        $view = $this->getView();
        return isset($view->site)
            ? $view->site
            : $view->getHelperPluginManager()->get('Zend\View\Helper\ViewModel')->getRoot()->getVariable('site');
    }
}
