<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\Paginator
 */
namespace Malwarebytes\ZendeskBundle\DataModel;
use \ArrayIterator;
use \Iterator;
/**
 * Allows us to create new instances from a given model factory.
 * @author relwell
 */
class Paginator implements Iterator
{
    /**
     * The url for the next page of results, if available.
     * @var string
     */
    protected $_nextPage;
    
    /**
     * The core iterator instance, which we switch out.
     * @var ArrayIterator
     */
    protected $_iterator;
    
    /**
     * Used to get the next page of results.
     * @var AbstractRepository
     */
    protected $_repository;
    
    /**
     * Constructor method.
     * @param AbstractRepository $repository
     */
    public function __construct( AbstractRepository $repository, $entities = null, $nextPage = null )
    {
        $this->_repository = $repository;
        if ( $entities !== null ) {
            $this->setEntities( $entities );
        }
        if ( $nextPage !== null ) {
            $this->setNextPage( $nextPage );
        }
    }
    
    /**
     * Allows an external repo to set the entities handled by the paginator.
     * @param array $entities
     * @return Paginator
     */
    public function setEntities( array $entities )
    {
        $this->_iterator = new ArrayIterator( $entities );
        return $this;
    }

    /**
     * Lets the paginator store the next URL for more entities.
     * @param string $page
     * @return Paginator
     */
    public function setNextPage( $page )
    {
        $this->_nextPage = $page;
        return $this;
    }
    
    /**
     * Returns the $_nextPage property.
     * @return string
     */
    public function getNextPage()
    {
        return $this->_nextPage;
    }
    
    /**
     * Here, we're using ArrayIterator::valid() to hook into the repository if we have another page. 
     * @see ArrayIterator::valid()
     * @return bool
     */
    public function valid()
    {
        if ( (! $this->_iterator->valid() ) && $this->_nextPage !== null ) {
            $this->_repository->updatePaginator( $this );
        } 
        return $this->_iterator->valid();
    }
    
    /**
     * Wraps our iterator instance's current() method.
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->_iterator->current();
    }
    
    /**
     * Wraps our iterator instance's key() method.
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->_iterator->key();
    }
    
    /**
     * Wraps our iterator instance's next() method.
     * @see Iterator::next()
     */
    public function next()
    {
        return $this->_iterator->next();
    }
    
    /**
     * Wraps our iterator instance's rewind() method.
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        return $this->_iterator->rewind();
    }
}