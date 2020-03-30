<?php

namespace Favorite\Api\Adapter;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class FavoriteAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id' => 'id',
        'created' => 'created',
        'item_id' => 'item_id',
        'item' => 'item',
        'item_set_id' => 'item_set_id',
        'modified' => 'modified',
    ];

    public function getResourceName()
    {
        return 'favorite_item';
    }

    public function getRepresentationClass()
    {
        return \Favorite\Api\Representation\FavoriteRepresentation::class;
    }

    public function getEntityClass()
    {
        return \Favorite\Entity\Favorite::class;
    }

    public function hydrate(Request $request, EntityInterface $entity,
        ErrorStore $errorStore
    ) {
        /** @var \AccessResource\Entity\AccessRequest $entity */
        $data = $request->getContent();
        foreach ($data as $key => $value) {
            $key = str_replace(['o:', 'o-module-favorite:'], '', $key);
            $method = 'set' . ucfirst(Inflector::camelize($key));
            if (!method_exists($entity, $method)) {
                continue;
            }
            $entity->$method($value);
        }
        if ($this->shouldHydrate($request, 'o:user_id')) {
            $userId = $request->getValue('o:user_id');
            $entity->setUser($this->getAdapter('users')->findEntity($userId));
        }
        if ($this->shouldHydrate($request, 'o:site_id')) {
            $siteId = $request->getValue('o:site_id');
            $entity->setSite($this->getAdapter('sites')->findEntity($siteId));
        }
        if ($this->shouldHydrate($request, 'o:item_id')) {
            $itemId = $request->getValue('o:item_id');
            $entity->setItem($this->getAdapter('items')->findEntity($itemId));
        }
        if ($this->shouldHydrate($request, 'o:item_set_id')) {
            $itemSetId = $request->getValue('o:item_set_id');
            $entity->setItemSet($this->getAdapter('item_sets')->findEntity($itemSetId));
        }
        
        $this->updateTimestamps($request, $entity);
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
        $isOldOmeka = \Omeka\Module::VERSION < 2;
        $alias = $isOldOmeka ? $this->getEntityClass() : 'omeka_root';
        $expr = $qb->expr();

        if (isset($query['user_id'])) {
            $userAlias = $this->createAlias();
            $qb->innerJoin(
                $alias . '.user',
                $userAlias
            );
            $qb->andWhere($expr->eq(
                $userAlias . '.id',
                $this->createNamedParameter($qb, $query['user_id']))
            );
        }

        if (isset($query['site_id'])) {
            $siteAlias = $this->createAlias();
            $qb->innerJoin(
                $alias . '.site',
                $siteAlias
            );
            if ($query['site_id']) {
                $qb->andWhere($expr->eq(
                    $siteAlias . '.id',
                    $this->createNamedParameter($qb, $query['site_id']))
                );
            }   
        }
        
        if (isset($query['item_id'])) {
            $siteAlias = $this->createAlias();
            $qb->innerJoin(
                $alias . '.item',
                $siteAlias
            );
            if ($query['item_id']) {
                $qb->andWhere($expr->eq(
                    $siteAlias . '.id',
                    $this->createNamedParameter($qb, $query['item_id']))
                );
            }
            // A "0" means a search in admin board.
            else {
                $qb->andWhere($expr->isNull($siteAlias . '.id'));
            }
        }
    }
}
