<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Ticket;
use Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
use Malwarebytes\ZendeskBundle\Service\ApiService;

class Entity extends AbstractEntity
{
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
    
    public function getPrimaryKey() {
        return 'id';
    }
}