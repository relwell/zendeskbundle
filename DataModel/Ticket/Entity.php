<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Ticket;
use Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
use Malwarebytes\ZendeskBundle\Service\ApiService;

class Entity extends AbstractEntity
{
    /**
     * These fields can't be changed.
     * @var array
     */
    protected $_readOnlyFields = array(
            'id', 
            'url',
            'description',
            'recipient',
            'submitter_id',
            'organization_id',
            'has_incidents',
            'satisfaction_rating',
            'sharing_agreement_ids',
            'created_at',
            'updated_at'
            );
    
    /**
     * These fields are required.
     * @var array
     */
    protected $_mandatoryFields = array(
            'requester_id',
            );
    
    /**
     * Primary key is id.
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
        return 'ticket';
    }
    
    /**
     * Returns comments
     * @return array
     */
    public function getComments()
    {
        return isset( $this['comments'] ) ? $this['comments'] : array();
    }

    /**
     * Adds a comment to the ticket. Automatically saves as well. Use ArrayAccess approach at your own peril.
     * @param string $comment
     * @param bool $public
     * @return Entity 
     */
    public function addComment( $comment, $public = true )
    {
        return $this->_repository->addCommentToTicket( $this, $comment, $public );
    }

    /**
     * Adds a user as collaborator on this ticket
     * @param Malwarebytes\ZendeskBundle\DataModel\User\Entity|int
     * @return Entity
     */
    public function addCollaborator( $userOrId )
    {
        $id = is_int( $userOrId ) ? $userOrId : $userOrId['id'];
        $this['collaborator_ids'] = empty( $this['collaborator_ids'] ) ? array( $id ) : array_merge( $this['collaborator_ids'], array( $id ) );
        return $this->_repository->save( $this );
    }

}