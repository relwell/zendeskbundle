<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Service\RepositoryService
 */
namespace Malwarebytes\ZendeskBundle\Service;
use \Exception;

class RepositoryService
{
    /**
     * Constructor
     * @var ApiService
     */
    protected $_api;
    
    /**
     * Stores repos for easy access.
     * @var array
     */
    protected $_repos = array();
    
    public function __construct( ApiService $api )
    {
        $this->_api = $api;
    }

    /**
     * Access a repo by its type
     * @param string $repoName
     */
    public function get( $repoName )
    {
        if ( empty( $this->_repos[$repoName] ) ) {
            $className = 'Malwarebytes\\ZendeskBundle\\DataModel\\' . $repoName . '\\Repository';
            if ( class_exists( $className ) ) {
                $this->_repos[$repoName] = new $className( $this->_api );
            } else {
                throw new Exception( "No class by name of {$className}" );
            }
        }
        return $this->_repos[$repoName];
    }
}