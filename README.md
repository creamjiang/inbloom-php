inBloom PHP SDK
===============

This is a PHP library to provide an SDK to inBloom's web services (http://inbloom.org)

Features
--------

The SDK is still under development, but so far there is support for GET (read) commands to inBloom services.

Features planned:
 * inBloom calls -> entity mapping
 * Automatic HATEOAS linking between entities
 * ???

Code Sample
-----------

    $apiToken = /* OAUTH2_TOKEN */ //We don't provide a mechanism for getting an OAuth2 token, use your favorite OAuth2 library

    $api = new InBloom/SandboxApi($apiToken);

    //Retrieve the home entity, this is a good starting point to take advantage of HATEOAS links
    $home = $api->fetch('home'); //$api->fetch('any/available/api/url')

    //From our home object we have easy access to other objects via the provided HATEOAS links:

    // InBloom/Teacher object
    $me = $home->self();

    // InBloom/EntityArray of InBloom/Entity/School objects
    $schools = $home->getSchools();

    // InBloom/EntityArray of InBloom/Entity/Section objects
    $sections = $home->getSections();

    // InBloom/EntityArray are Iterable and Traversable:
    foreach($sections as $section)
    {
        echo $section->id.'<br />';
    }


    //Easy access values on entities
    $id = $me->id;
    $name = $me->name['firstName'].' '.$me->name['lastSurname'];

    echo "My name is {$name} and my unique id is: {$id}<br />";

    //Easily modify values on entities
    $me->yearsOfPriorTeachingExperience = 4;

    //Then save those entities with the new values (Still under development)
    $me->save();

Documentation
-------------

We're still working out the code and will provide better documentation when we're done with sweeping changes to our approach.  Feel free to ask questions or tinker.

License
-------

Code is open-sourced software licensed under the MIT License.  See LICENSE.txt for details.


