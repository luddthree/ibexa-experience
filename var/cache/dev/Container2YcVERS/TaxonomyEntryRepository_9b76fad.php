<?php

namespace Container2YcVERS;
include_once \dirname(__DIR__, 4).'/vendor/doctrine/persistence/src/Persistence/ObjectRepository.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/collections/src/Selectable.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/src/EntityRepository.php';
include_once \dirname(__DIR__, 4).'/vendor/gedmo/doctrine-extensions/src/Tree/RepositoryUtilsInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/gedmo/doctrine-extensions/src/Tree/RepositoryInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/gedmo/doctrine-extensions/src/Tree/Entity/Repository/AbstractTreeRepository.php';
include_once \dirname(__DIR__, 4).'/vendor/gedmo/doctrine-extensions/src/Tree/Entity/Repository/NestedTreeRepository.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/Repository/ServiceEntityRepositoryInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/ibexa/taxonomy/src/lib/Persistence/Repository/TaxonomyEntryRepository.php';

class TaxonomyEntryRepository_9b76fad extends \Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository|null wrapped object, if the proxy is initialized
     */
    private $valueHolder3c8ba = null;

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializerf0d6f = null;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties2eb7b = [
        
    ];

    public function findById(int $id) : ?\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findById', array('id' => $id), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findById($id);
    }

    public function findByContentId(int $contentId) : ?\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findByContentId', array('contentId' => $contentId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findByContentId($contentId);
    }

    public function findByIdentifier(string $identifier, ?string $taxonomy = null) : ?\Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findByIdentifier', array('identifier' => $identifier, 'taxonomy' => $taxonomy), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findByIdentifier($identifier, $taxonomy);
    }

    public function findByMatchingName(string $name, ?string $languageCode = null, ?int $limit = null, int $offset = 0, ?string $taxonomy = null) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findByMatchingName', array('name' => $name, 'languageCode' => $languageCode, 'limit' => $limit, 'offset' => $offset, 'taxonomy' => $taxonomy), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findByMatchingName($name, $languageCode, $limit, $offset, $taxonomy);
    }

    public function countAllEntries(?string $taxonomyName = null) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countAllEntries', array('taxonomyName' => $taxonomyName), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countAllEntries($taxonomyName);
    }

    public function loadAllEntries(?string $taxonomyName, int $limit, int $offset) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadAllEntries', array('taxonomyName' => $taxonomyName, 'limit' => $limit, 'offset' => $offset), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadAllEntries($taxonomyName, $limit, $offset);
    }

    public function loadEntryChildren(int $parentEntryId, ?int $limit = 30, int $offset = 0) : array
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'loadEntryChildren', array('parentEntryId' => $parentEntryId, 'limit' => $limit, 'offset' => $offset), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->loadEntryChildren($parentEntryId, $limit, $offset);
    }

    public function countEntryChildren(int $parentEntryId) : int
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'countEntryChildren', array('parentEntryId' => $parentEntryId), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->countEntryChildren($parentEntryId);
    }

    public function __call($method, $args)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__call', array('method' => $method, 'args' => $args), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->__call($method, $args);
    }

    public function getRootNodesQueryBuilder($sortByField = null, $direction = 'asc')
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRootNodesQueryBuilder', array('sortByField' => $sortByField, 'direction' => $direction), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRootNodesQueryBuilder($sortByField, $direction);
    }

    public function getRootNodesQuery($sortByField = null, $direction = 'asc')
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRootNodesQuery', array('sortByField' => $sortByField, 'direction' => $direction), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRootNodesQuery($sortByField, $direction);
    }

    public function getRootNodes($sortByField = null, $direction = 'asc')
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRootNodes', array('sortByField' => $sortByField, 'direction' => $direction), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRootNodes($sortByField, $direction);
    }

    public function getPathQueryBuilder($node)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPathQueryBuilder', array('node' => $node), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPathQueryBuilder($node);
    }

    public function getPathQuery($node)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPathQuery', array('node' => $node), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPathQuery($node);
    }

    public function getPath($node)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPath', array('node' => $node), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPath($node);
    }

    public function getPathAsString(object $node, array $options = []) : string
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPathAsString', array('node' => $node, 'options' => $options), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPathAsString($node, $options);
    }

    public function childrenQueryBuilder($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'childrenQueryBuilder', array('node' => $node, 'direct' => $direct, 'sortByField' => $sortByField, 'direction' => $direction, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->childrenQueryBuilder($node, $direct, $sortByField, $direction, $includeNode);
    }

    public function childrenQuery($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'childrenQuery', array('node' => $node, 'direct' => $direct, 'sortByField' => $sortByField, 'direction' => $direction, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->childrenQuery($node, $direct, $sortByField, $direction, $includeNode);
    }

    public function children($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'children', array('node' => $node, 'direct' => $direct, 'sortByField' => $sortByField, 'direction' => $direction, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->children($node, $direct, $sortByField, $direction, $includeNode);
    }

    public function getChildrenQueryBuilder($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getChildrenQueryBuilder', array('node' => $node, 'direct' => $direct, 'sortByField' => $sortByField, 'direction' => $direction, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getChildrenQueryBuilder($node, $direct, $sortByField, $direction, $includeNode);
    }

    public function getChildrenQuery($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getChildrenQuery', array('node' => $node, 'direct' => $direct, 'sortByField' => $sortByField, 'direction' => $direction, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getChildrenQuery($node, $direct, $sortByField, $direction, $includeNode);
    }

    public function getChildren($node = null, $direct = false, $sortByField = null, $direction = 'ASC', $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getChildren', array('node' => $node, 'direct' => $direct, 'sortByField' => $sortByField, 'direction' => $direction, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getChildren($node, $direct, $sortByField, $direction, $includeNode);
    }

    public function getLeafsQueryBuilder($root = null, $sortByField = null, $direction = 'ASC')
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getLeafsQueryBuilder', array('root' => $root, 'sortByField' => $sortByField, 'direction' => $direction), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getLeafsQueryBuilder($root, $sortByField, $direction);
    }

    public function getLeafsQuery($root = null, $sortByField = null, $direction = 'ASC')
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getLeafsQuery', array('root' => $root, 'sortByField' => $sortByField, 'direction' => $direction), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getLeafsQuery($root, $sortByField, $direction);
    }

    public function getLeafs($root = null, $sortByField = null, $direction = 'ASC')
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getLeafs', array('root' => $root, 'sortByField' => $sortByField, 'direction' => $direction), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getLeafs($root, $sortByField, $direction);
    }

    public function getNextSiblingsQueryBuilder($node, $includeSelf = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getNextSiblingsQueryBuilder', array('node' => $node, 'includeSelf' => $includeSelf), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getNextSiblingsQueryBuilder($node, $includeSelf);
    }

    public function getNextSiblingsQuery($node, $includeSelf = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getNextSiblingsQuery', array('node' => $node, 'includeSelf' => $includeSelf), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getNextSiblingsQuery($node, $includeSelf);
    }

    public function getNextSiblings($node, $includeSelf = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getNextSiblings', array('node' => $node, 'includeSelf' => $includeSelf), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getNextSiblings($node, $includeSelf);
    }

    public function getPrevSiblingsQueryBuilder($node, $includeSelf = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPrevSiblingsQueryBuilder', array('node' => $node, 'includeSelf' => $includeSelf), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPrevSiblingsQueryBuilder($node, $includeSelf);
    }

    public function getPrevSiblingsQuery($node, $includeSelf = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPrevSiblingsQuery', array('node' => $node, 'includeSelf' => $includeSelf), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPrevSiblingsQuery($node, $includeSelf);
    }

    public function getPrevSiblings($node, $includeSelf = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getPrevSiblings', array('node' => $node, 'includeSelf' => $includeSelf), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getPrevSiblings($node, $includeSelf);
    }

    public function moveDown($node, $number = 1)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'moveDown', array('node' => $node, 'number' => $number), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->moveDown($node, $number);
    }

    public function moveUp($node, $number = 1)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'moveUp', array('node' => $node, 'number' => $number), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->moveUp($node, $number);
    }

    public function removeFromTree($node)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'removeFromTree', array('node' => $node), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->removeFromTree($node);
    }

    public function reorder($node, $sortByField = null, $direction = 'ASC', $verify = true, $recursive = true)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'reorder', array('node' => $node, 'sortByField' => $sortByField, 'direction' => $direction, 'verify' => $verify, 'recursive' => $recursive), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->reorder($node, $sortByField, $direction, $verify, $recursive);
    }

    public function reorderAll($sortByField = null, $direction = 'ASC', $verify = true)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'reorderAll', array('sortByField' => $sortByField, 'direction' => $direction, 'verify' => $verify), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->reorderAll($sortByField, $direction, $verify);
    }

    public function verify()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'verify', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->verify();
    }

    public function recoverFast(array $options = []) : void
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'recoverFast', array('options' => $options), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba->recoverFast($options);
return;
    }

    public function recover()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'recover', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->recover();
    }

    public function getNodesHierarchyQueryBuilder($node = null, $direct = false, array $options = [], $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getNodesHierarchyQueryBuilder', array('node' => $node, 'direct' => $direct, 'options' => $options, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getNodesHierarchyQueryBuilder($node, $direct, $options, $includeNode);
    }

    public function getNodesHierarchyQuery($node = null, $direct = false, array $options = [], $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getNodesHierarchyQuery', array('node' => $node, 'direct' => $direct, 'options' => $options, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getNodesHierarchyQuery($node, $direct, $options, $includeNode);
    }

    public function getNodesHierarchy($node = null, $direct = false, array $options = [], $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getNodesHierarchy', array('node' => $node, 'direct' => $direct, 'options' => $options, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getNodesHierarchy($node, $direct, $options, $includeNode);
    }

    public function setRepoUtils(\Gedmo\Tree\RepositoryUtilsInterface $repoUtils)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setRepoUtils', array('repoUtils' => $repoUtils), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->setRepoUtils($repoUtils);
    }

    public function getRepoUtils()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getRepoUtils', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getRepoUtils();
    }

    public function childCount($node = null, $direct = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'childCount', array('node' => $node, 'direct' => $direct), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->childCount($node, $direct);
    }

    public function childrenHierarchy($node = null, $direct = false, array $options = [], $includeNode = false)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'childrenHierarchy', array('node' => $node, 'direct' => $direct, 'options' => $options, 'includeNode' => $includeNode), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->childrenHierarchy($node, $direct, $options, $includeNode);
    }

    public function buildTree(array $nodes, array $options = [])
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'buildTree', array('nodes' => $nodes, 'options' => $options), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->buildTree($nodes, $options);
    }

    public function buildTreeArray(array $nodes)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'buildTreeArray', array('nodes' => $nodes), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->buildTreeArray($nodes);
    }

    public function setChildrenIndex($childrenIndex)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'setChildrenIndex', array('childrenIndex' => $childrenIndex), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->setChildrenIndex($childrenIndex);
    }

    public function getChildrenIndex()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getChildrenIndex', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getChildrenIndex();
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createQueryBuilder', array('alias' => $alias, 'indexBy' => $indexBy), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createQueryBuilder($alias, $indexBy);
    }

    public function createResultSetMappingBuilder($alias)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createResultSetMappingBuilder', array('alias' => $alias), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createResultSetMappingBuilder($alias);
    }

    public function createNamedQuery($queryName)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createNamedQuery', array('queryName' => $queryName), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createNamedQuery($queryName);
    }

    public function createNativeNamedQuery($queryName)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'createNativeNamedQuery', array('queryName' => $queryName), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->createNativeNamedQuery($queryName);
    }

    public function clear()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'clear', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->clear();
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'find', array('id' => $id, 'lockMode' => $lockMode, 'lockVersion' => $lockVersion), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->find($id, $lockMode, $lockVersion);
    }

    public function findAll()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findAll', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findBy', array('criteria' => $criteria, 'orderBy' => $orderBy, 'limit' => $limit, 'offset' => $offset), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'findOneBy', array('criteria' => $criteria, 'orderBy' => $orderBy), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->findOneBy($criteria, $orderBy);
    }

    public function count(array $criteria)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'count', array('criteria' => $criteria), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->count($criteria);
    }

    public function getClassName()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'getClassName', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->getClassName();
    }

    public function matching(\Doctrine\Common\Collections\Criteria $criteria)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'matching', array('criteria' => $criteria), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return $this->valueHolder3c8ba->matching($criteria);
    }

    /**
     * Constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public static function staticProxyConstructor($initializer)
    {
        static $reflection;

        $reflection = $reflection ?? new \ReflectionClass(__CLASS__);
        $instance   = $reflection->newInstanceWithoutConstructor();

        unset($instance->listener, $instance->repoUtils, $instance->_entityName, $instance->_em, $instance->_class);

        \Closure::bind(function (\Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository $instance) {
            unset($instance->taxonomyConfiguration);
        }, $instance, 'Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository')->__invoke($instance);

        $instance->initializerf0d6f = $initializer;

        return $instance;
    }

    public function __construct(\Doctrine\ORM\EntityManagerInterface $em, \Ibexa\Taxonomy\Service\TaxonomyConfiguration $taxonomyConfiguration)
    {
        static $reflection;

        if (! $this->valueHolder3c8ba) {
            $reflection = $reflection ?? new \ReflectionClass('Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository');
            $this->valueHolder3c8ba = $reflection->newInstanceWithoutConstructor();
        unset($this->listener, $this->repoUtils, $this->_entityName, $this->_em, $this->_class);

        \Closure::bind(function (\Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository $instance) {
            unset($instance->taxonomyConfiguration);
        }, $this, 'Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository')->__invoke($this);

        }

        $this->valueHolder3c8ba->__construct($em, $taxonomyConfiguration);
    }

    public function & __get($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__get', ['name' => $name], $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        if (isset(self::$publicProperties2eb7b[$name])) {
            return $this->valueHolder3c8ba->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            $backtrace = debug_backtrace(false, 1);
            trigger_error(
                sprintf(
                    'Undefined property: %s::$%s in %s on line %s',
                    $realInstanceReflection->getName(),
                    $name,
                    $backtrace[0]['file'],
                    $backtrace[0]['line']
                ),
                \E_USER_NOTICE
            );
            return $targetObject->$name;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __set($name, $value)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__set', array('name' => $name, 'value' => $value), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            $targetObject->$name = $value;

            return $targetObject->$name;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function & () use ($targetObject, $name, $value) {
            $targetObject->$name = $value;

            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __isset($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__isset', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            return isset($targetObject->$name);
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    public function __unset($name)
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__unset', array('name' => $name), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $realInstanceReflection = new \ReflectionClass('Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder3c8ba;

            unset($targetObject->$name);

            return;
        }

        $targetObject = $this->valueHolder3c8ba;
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);

            return;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $accessor();
    }

    public function __clone()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__clone', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        $this->valueHolder3c8ba = clone $this->valueHolder3c8ba;
    }

    public function __sleep()
    {
        $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, '__sleep', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;

        return array('valueHolder3c8ba');
    }

    public function __wakeup()
    {
        unset($this->listener, $this->repoUtils, $this->_entityName, $this->_em, $this->_class);

        \Closure::bind(function (\Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository $instance) {
            unset($instance->taxonomyConfiguration);
        }, $this, 'Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository')->__invoke($this);
    }

    public function setProxyInitializer(?\Closure $initializer = null) : void
    {
        $this->initializerf0d6f = $initializer;
    }

    public function getProxyInitializer() : ?\Closure
    {
        return $this->initializerf0d6f;
    }

    public function initializeProxy() : bool
    {
        return $this->initializerf0d6f && ($this->initializerf0d6f->__invoke($valueHolder3c8ba, $this, 'initializeProxy', array(), $this->initializerf0d6f) || 1) && $this->valueHolder3c8ba = $valueHolder3c8ba;
    }

    public function isProxyInitialized() : bool
    {
        return null !== $this->valueHolder3c8ba;
    }

    public function getWrappedValueHolderValue()
    {
        return $this->valueHolder3c8ba;
    }
}

if (!\class_exists('TaxonomyEntryRepository_9b76fad', false)) {
    \class_alias(__NAMESPACE__.'\\TaxonomyEntryRepository_9b76fad', 'TaxonomyEntryRepository_9b76fad', false);
}
