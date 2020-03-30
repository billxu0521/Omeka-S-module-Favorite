<?php

namespace Favorite\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Site;
use Omeka\Entity\User;
use Omeka\Entity\Item;
use Omeka\Entity\ItemSet;

/**
 * @Entity
 * @Table(
 *     name="favorite_item"
 * )
 * @HasLifecycleCallbacks
 */
class Favorite extends AbstractEntity
{
    /**
     * @var int
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @var User
     *
     * @ManyToOne(
     *      targetEntity="\Omeka\Entity\User"
     * )
     * @JoinColumn(
     *      nullable=false,
     *      onDelete="CASCADE"
     * )
     */
    protected $user;

    /**
     * @var \Omeka\Entity\Site
     *
     * @ManyToOne(
     *      targetEntity="\Omeka\Entity\Site"
     * )
     * @JoinColumn(
     *      nullable=true,
     *      onDelete="CASCADE"
     * )
     */
    protected $site;
    
    /**
     * @var \Omeka\Entity\Item
     *
     * @ManyToOne(
     *      targetEntity="\Omeka\Entity\Item"
     * )
     * @JoinColumn(
     *      nullable=true,
     *      onDelete="CASCADE"
     * )
     */
    protected $item;
    
    /**
     * @var \Omeka\Entity\ItemSet
     *
     * @ManyToOne(
     *      targetEntity="\Omeka\Entity\ItemSet"
     * )
     * @JoinColumn(
     *      nullable=true,
     *      onDelete="CASCADE"
     * )
     */
    protected $itemset;

    
    /**
     * @var DateTime
     *
     * @Column(type="datetime")
     */
    protected $created;
    /**
     * @var DateTime
     *
     * @Column(type="datetime")
     */
    protected $modified;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param User $user
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \Omeka\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Site $site
     * @return self
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @return \Omeka\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }
    
    /**
     * @param Item $item
     * @return self
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return \Omeka\Entity\Site
     */
    public function getItem()
    {
        return $this->item;
    }
    
    /**
     * @param ItemSet $itemset
     * @return self
     */
    public function setItemSet(ItemSet $itemset = null)
    {
        $this->itemset = $itemset;
        return $this;
    }

    /**
     * @return \Omeka\Entity\ItemSet
     */
    public function getItemSet()
    {
        return $this->itemset;
    }

    /**
     * @param DateTime $created
     * @return self
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $modified
     * @return self
     */
    public function setModified(DateTime $modified)
    {
        $this->modified = $modified;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->created = new DateTime('now');
        return $this;
    }
}
