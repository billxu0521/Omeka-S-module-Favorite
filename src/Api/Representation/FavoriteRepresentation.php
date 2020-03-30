<?php

namespace Favorite\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class FavoriteRepresentation extends AbstractEntityRepresentation
{
    public function getControllerName()
    {
        return \Favorite\Controller\Admin\FavoriteController::class;
    }

    public function getJsonLdType()
    {
        return 'o-module-favorite:Favorite';
    }

    public function getJsonLd()
    {
        $user = $this->user();
        $site = $this->site();

        $created = [
            '@value' => $this->getDateTime($this->created()),
            '@type' => 'http://www.w3.org/2001/XMLSchema#dateTime',
        ];

        $modified = $this->modified();
        if ($modified) {
            $modified = [
                '@value' => $this->getDateTime($modified),
                '@type' => 'http://www.w3.org/2001/XMLSchema#dateTime',
            ];
        }

        return [
            'o:id' => $this->id(),
            'o:user' => $user ? $user->getReference() : null,
            'o:site' => $site ? $site->getReference() : null,
            'o:item' => $this->item(),
            'o:iteset' => $this->itemset(),
            'o:created' => $created,
            'o:modified' => $modified,
        ];
    }

    /**
     * @return \Omeka\Api\Representation\UserRepresentation
     */
    public function user()
    {
        $user = $this->resource->getUser();
        return $this->getAdapter('users')->getRepresentation($user);
    }

    /**
     * @return \Omeka\Api\Representation\SiteRepresentation|null
     */
    public function site()
    {
        $site = $this->resource->getSite();
        return $site
            ? $this->getAdapter('sites')->getRepresentation($site)
            : null;
    }
    
    /**
     * @return \Omeka\Api\Representation\SiteRepresentation|null
     */
    public function item()
    {
        $item = $this->resource->getItem();
        return $item;
    }
    
    /**
     * @return \Omeka\Api\Representation\SiteRepresentation|null
     */
    public function itemset()
    {
        $itemset = $this->resource->getItemset();
        return $itemset
            ? $this->getAdapter('itemset')->getRepresentation($itemset)
            : null;
    }
  
    /**
     * @return \DateTime
     */
    public function created()
    {
        return $this->resource->getCreated();
    }

    /**
     * @return \DateTime
     */
    public function modified()
    {
        return $this->resource->getModified();
    }



public function siteUrl($siteSlug = null, $canonical = false)
    {
        if (!$siteSlug) {
            $siteSlug = $this->getServiceLocator()->get('Application')
                ->getMvcEvent()->getRouteMatch()->getParam('site-slug');
        }
        $url = $this->getViewHelper('Url');
        return $url(
            'site/guest/favorite',
            [
                'site-slug' => $siteSlug,
                'id' => $this->id(),
            ],
            ['force_canonical' => $canonical]
        );
    }


    /**
     * Check if a module is active.
     *
     * @param string $moduleClass
     * @return bool
     */
protected function isModuleActive($moduleClass)
{
        $services = $this->getServiceLocator();
        /** @var \Omeka\Module\Manager $moduleManager */
        $moduleManager = $services->get('Omeka\ModuleManager');
        $module = $moduleManager->getModule($moduleClass);
        return $module
            && $module->getState() === \Omeka\Module\Manager::STATE_ACTIVE;
    }
}
