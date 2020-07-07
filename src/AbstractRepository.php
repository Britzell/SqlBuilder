<?php


namespace Britzel\SqlBuilder;


abstract class AbstractRepository
{

    public $className;
    public $builder;

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @param Builder $builder
     */
    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    public function __construct()
    {
        $this->setBuilder(new Builder());
    }

    /**
     * @param int $id
     * @return object
     */
    public function find(int $id)
    {
        return $this->getBuilder()->find($id, $this->getClassName());
    }

    /**
     * @return array|object
     */
    public function findAll()
    {
        return $this->getBuilder()->findAll($this->getClassName());
    }

    /**
     * @param string $criteria
     * @param int|string $value
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $order
     * @return array|object
     */
    public function findBy(string $criteria, $value, int $limit = null, int $offset = null, array $order = null)
    {
        return $this->getBuilder()->findBy($criteria, $value, $limit, $offset, $order, $this->getClassName());
    }

    /**
     * @param string $criteria
     * @param $value
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $order
     * @return object
     */
    public function findOneBy(string $criteria, $value, int $limit = null, int $offset = null, array $order = null)
    {
        return $this->getBuilder()->findOneBy($criteria, $value, $limit, $offset, $order, $this->getClassName());
    }

}
