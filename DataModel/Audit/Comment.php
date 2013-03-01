<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\Audit\Comment
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Audit;

class Comment extends \ArrayIterator
{
    /**
     * Whether the comment is public.
     * @return bool
     */
    public function isPublic()
    {
        return $this['public'];
    }
    
    public function __toString()
    {
        return $this['body'];
    }
    
    public function getAttachments()
    {
        return $this['attachments'];
    }
}