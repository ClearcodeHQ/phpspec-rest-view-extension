<?php

namespace spec\Codifico\PhpSpec\RestViewExtension\Serializer;

use Codifico\PhpSpec\RestViewExtension\Serializer\PropertyMetadataFake;
use JMS\Serializer\Metadata\PropertyMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin PropertyMetadataFake
 */
class PropertyMetadataFakeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PropertyMetadataFake::class);
    }

    function it_should_fake_property_metadata()
    {
        $this->shouldHaveType(PropertyMetadata::class);
    }
}
