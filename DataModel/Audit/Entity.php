<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\Audit\Entity
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Audit;
use Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
/**
 * Encapsulates ticket comment behavior
 * @author relwell
 */
class Entity extends AbstractEntity
{
    /**
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::getPrimaryKey()
     */
    public function getPrimaryKey() 
    {
        return 'id';
    }

    /**
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::getType()
     */
    public function getType()
    {
        return 'audit';
    }
}