<?php
/**
 * Class definition for Malwarebytes\Zendesk\Model\Paginator
 */
namespace Malwarebytes\Zendesk\Model;
/**
 * Allows us to create new instances from a given model factory.
 * @author relwell
 */
class Paginator
{
    /**
     * Lets us know what factory we need to be calling
     * @var AbstractModelFactory
     */
    protected $_factory;
    
    public function __construct( AbstractModelFactory $factory )
    {
        $this->_factory = $factory;
    }
    
    /**
     * Needs to implement iterable
     * @see ArrayIterator::valid()
     */
    public function valid()
    {
        // if there exists a pagination link in the next response, call it, and add it to the array
        
    }
}