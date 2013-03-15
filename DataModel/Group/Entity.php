<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\Group\Entity
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Group;
use Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
/**
 * Encapsulates group data
 * @author relwell
 */
class Entity extends AbstractEntity
{
    /**
     * These can't be changed.
     * @var array
     */
    protected $_readOnlyFields = array(
            'id',
            'url',
            'deleted',
            'created_at',
            'updated_at',
            );
    
    /**
     * Primary key is ID
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::getPrimaryKey()
     * @return string
     */
    public function getPrimaryKey()
    {
        return 'id';
    }
    
    /**
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::getType()
     * @return string
     */
    public function getType()
    {
        return 'group';
    }
}