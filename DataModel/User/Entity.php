<?php
/**
 * Class definition for \Malwarebytes\ZendeskBundle\DataModel\User\Entity
 */
namespace Malwarebytes\ZendeskBundle\DataModel\User;
use \Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
/**
 * An entity for users.
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
            'created_at',
            'updated_at',
            'active',
            'yes',
            'last_login_at',
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
        return 'user';
    }
}