# PhpSpec Rest View Extension

[![Build Status](https://travis-ci.org/Codifico/phpspec-rest-view-extension.svg?branch=master)](https://travis-ci.org/Codifico/phpspec-rest-view-extension)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Codifico/phpspec-rest-view-extension/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Codifico/phpspec-rest-view-extension/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/codifico/phpspec-rest-view-extension/v/stable.svg)](https://packagist.org/packages/codifico/phpspec-rest-view-extension)
[![Latest Unstable Version](https://poser.pugx.org/codifico/phpspec-rest-view-extension/v/unstable.svg)](https://packagist.org/packages/codificophpspec-rest-view-extension) [![License](https://poser.pugx.org/codifico/phpspec-rest-view-extension/license.svg)](https://packagist.org/packages/codifico/phpspec-rest-view-extension)
[![Total Downloads](https://poser.pugx.org/codifico/phpspec-rest-view-extension/downloads.svg)](https://packagist.org/packages/codifico/phpspec-rest-view-extension)

Provides an easy way to manage expectation about Rest View object, based on [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle) View object.

## Instalation

```bash
php composer.phar require codifico/phpspec-rest-view-extension:dev-master --dev
```

Activate extension by specifying its class in your phpspec.yml:

```
# phpspec.yml
extensions:
    - Codifico\PhpSpec\RestViewExtension\Extension
```

## Usage

The spec file usage:

```php
# UserControllerSpec.php
<?php

namespace spec\AppBundle\Controller;

use AppBundle\Controller\UserController;
use FOS\RestBundle\Util\Codes;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @mixin UserController
 *
 * @method beConstructedWith(UserInterface $user)
 */
class UserControllerSpec extends ObjectBehavior
{
    function let(UserInterface $user)
    {
        $this->beConstructedWith($user);
    }

    function it_show_the_current_user(UserInterface $user)
    {
        $this->getUser()->shouldBeRestViewWith([
            'data' => $user,
            'statusCode' => Codes::HTTP_OK,
            'serializationGroups' => ['user_profile'],
            'headers' => [
                'cache-control' => ['no-cache'],
                'date' => ["@string@.isDateTime()"],
            ]
        ]);
    }
}
```

and coresponding controller file:

```php
# UserController.php
<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Util\Codes;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;

class UserController
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return \FOS\RestBundle\View\View|null
     */
    public function getUser()
    {
        $context = SerializationContext::create();
        $context->setGroups('user_profile');

        $view = View::create($this->user, Codes::HTTP_OK, []);
        $view->setSerializationContext($context);

        return $view;
    }
}
```

## Copyright

Copyright (c) 2015 Marcin Dryka (drymek). See LICENSE for details.

## Contributors

* Marcin Dryka [drymek](http://github.com/drymek) [lead developer]
* Other [awesome developers](https://github.com/Codifico/phpspec-rest-view-extension/graphs/contributors)
