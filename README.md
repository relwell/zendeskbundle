h1. Dependencies

h2. Zendesk API
This library requires a Zendesk API available at GitHub.
Since this isn't controlled by Composer call the following from your application's root directory:
 
    git clone git://github.com/ludwigzzz/Zendesk-API.git vendor/zendesk-api

In app/autoload.php, add the following lines before AnnotationRegistry::registerLoader(array($loader, 'loadClass')):

    $loader->addClassMap( array( 'zendesk' =>  __DIR__.'/../vendor/zendesk-api/zendesk.php' ) );

Add your Zendesk API information to Resources/config/api.yml, which is not managed in Git for security purposes.
We do provide api.stub.yml, which you can fill in and copy over.