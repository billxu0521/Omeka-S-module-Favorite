<?php

namespace Favorite\Controller\Site;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Api\Representation\AbstractRepresentation;

use Zend\View\Model\JsonModel;

class FavoriteListController extends AbstractActionController
{
    public function addAction()
    {
        $params = $this->params();
        $resource_id = $params->fromQuery('resource_id');
        $site = $this->currentSite();
        $site_id = $site ? $site->id() : null;
        $api = $this->api();
        $resource = $api->read('items',$resource_id)->getContent(); 
        $itemset = $resource->itemSets();
        $itemset_id = key((array)$itemset);
        
        $user = $this->identity();
        if (!$user) {
            return new JsonModel([
                'status' => 'error',
                'message' => $this->translate('Access forbidden'), // @translate
            ]);
        }
        
        $favoriteRequest = [
            'o:user_id' => $user->getId(),
            'o:site_id' => $site_id,
            'o:item_id' => intval($resource_id),
            'o:item_set_id' => $itemset_id,
        ];
        
        $response = $api->create('favorite_item', $favoriteRequest); 

        if (!$response) {
            return new JsonModel([
                'status' => 'error',
                'message' => $this->translate('Unable to save.'), // @translate
            ]);
        }
        $favotiteItem = $response->getContent();
        return new JsonModel([
            'status' => 'success',
            'data' => [
                'item_id' => intval($resource_id),
                'itemset_id' => $itemset_id,
                'site_id' => $site_id,
                'url_delete' => $this->url()->fromRoute('site/favorite-id', ['action' => 'delete', 'id' => $favotiteItem->id()], true),
                ],
        ]);
    }
  

    public function deleteAction()
    {
        $user = $this->identity();
        if (!$user) {
            return new JsonModel([
                'status' => 'error',
                'message' => $this->translate('Access forbidden'), // @translate
            ]);
        }

        $params = $this->params();
        $id = $params->fromRoute('id') ?: $params->fromQuery('id');
        if (!$id) {
            return $this->jsonErrorNotFound();
        }

        $isMultiple = is_array($id);
        $ids = $isMultiple ? $id : [$id];

        $api = $this->api();

        $userId = $user->getId();

        $results = [];
        foreach ($ids as $id) {
            // Avoid to remove requests of another user.
            // TODO Add acl check for own requests.
            $data = [
                'id' => $id,
                'user_id' => $userId,
            ];
            $favoritelist = $api->searchOne('favorite_item', $data)->getContent();
            if ($favoritelist) {
                $api->delete('favorite_item', ['id' => $id]);
            }
            $results[$id] = null;
        }

        return new JsonModel([
            'status' => 'success',
            'data' => [
                'favorite_item' => $results,
            ],
        ]);
    }

    /**
     * Clean a request query.
     *
     * @see \Favorite\View\Helper\LinkFavorite::cleanQuery()
     *
     * @return string
     */
    protected function cleanQuery()
    {
        $params = $this->params();
        $query = ltrim($params->fromQuery('query'), '?');

        // Clean query for better search.
        $request = [];
        parse_str($query, $request);
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

    protected function jsonErrorNotFound()
    {
        $response = $this->getResponse();
        $response->setStatusCode(Response::STATUS_CODE_404);
        return new JsonModel([
            'status' => 'error',
            'message' => $this->translate('Not found'), // @translate
        ]);
    }
}
