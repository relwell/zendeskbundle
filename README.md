About
==============
Malwarebytes\ZendeskBundle is a bundle designed to encapsulate Zendesk's REST API using 
a repository/entity scheme for its data model. It is a continuous work in progress.


Dependencies
================
Zendesk API Client
----------------
This library requires a Zendesk API client available on GitHub.
Since this isn't controlled by Composer call the following from your application's root directory:
 
    git clone git://github.com/relwell/Zendesk-API.git vendor/zendesk-api

In app/autoload.php, add the following lines before AnnotationRegistry::registerLoader(array($loader, 'loadClass')):

    $loader->addClassMap( array( 'ZendeskApi\Client' =>  __DIR__.'/../vendor/zendesk-api/ZendeskApi/Client.php' ) );

Add the following fields to your application's parameters.yml:

    parameters:
        zendesk_api_key:   yourkey
        zendesk_api_user:  youruser
        zendesk_api_subdomain: yoursubdomain
