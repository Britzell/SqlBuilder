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
     */
    public function find(int $id)
    {
        return $this->getBuilder()->find($id, $this->getClassName());
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->getBuilder()->findAll($this->getClassName());
    }

    /**
     * @param string $criteria
     * @param $value
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function findBy(string $criteria, $value, $limit = null, $offset = null): array
    {
        return $this->getBuilder()->findBy($criteria, $value, $limit, $offset, $this->getClassName());
    }

    /**
     * @param string $criteria
     * @param $value
     */
    public function findOneBy(string $criteria, $value)
    {
        return $this->getBuilder()->findOneBy($criteria, $value, $this->getClassName());
    }

}
