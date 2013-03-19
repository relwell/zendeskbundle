<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Test\DataModel\Audit\Comment
 */
namespace Malwarebytes\ZendeskBundle\Test\DataModel\Audit;
use Malwarebytes\ZendeskBundle\DataModel\Audit\Comment;
/**
 * Tests comment
 * @author relwell
 */
class CommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Comment::isPublic
     */
    public function testIsPublic()
    {
        $comment = new Comment( array( 'public' => true ) );
        $this->assertTrue(
                $comment->isPublic()
        );
        $comment = new Comment( array( 'public' => false ) );
        $this->assertFalse(
                $comment->isPublic()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Comment::__toString
     */
    public function test__toString()
    {
        $comment = new Comment( array( 'body' => 'this is my body' ) );
        $this->assertEquals(
                'this is my body',
                (string) $comment
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Comment::getAttachments
     */
    public function testGetAttachments()
    {
        $comment = new Comment( array( 'attachments' => array( 'foo.jpg' ) ) );
        $this->assertEquals(
                array( 'foo.jpg' ),
                $comment->getAttachments()
        );
    }
}