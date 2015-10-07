<?php

namespace spec\Codifico\PhpSpec\RestViewExtension;

use Codifico\PhpSpec\RestViewExtension\Extension;
use PhpSpec\ObjectBehavior;
use PhpSpec\ServiceContainer;
use Prophecy\Argument;

/**
 * @mixin Extension
 */
class ExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Extension::class);
    }

    function it_should_register_matcher(ServiceContainer $container)
    {
        $container->set('matchers.rest_view', Argument::type(\Closure::class))->shouldBeCalled();

        $this->load($container);
    }
}
