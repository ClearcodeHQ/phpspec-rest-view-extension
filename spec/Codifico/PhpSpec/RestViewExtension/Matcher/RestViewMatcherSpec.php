<?php

namespace spec\Codifico\PhpSpec\RestViewExtension\Matcher;

use Codifico\PhpSpec\RestViewExtension\Matcher\RestViewMatcher;
use Coduo\PHPMatcher\Factory;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Formatter\Presenter\PresenterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin RestViewMatcher
 */
class RestViewMatcherSpec extends ObjectBehavior
{
    function let(PresenterInterface $presenter)
    {
        $factory = new Factory\SimpleFactory();
        $this->beConstructedWith($presenter, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RestViewMatcher::class);
    }

    function it_should_support_rest_view_matcher()
    {
        $this->supports('beRestView', null, [])->shouldReturn(true);
    }

    function it_should_support_rest_view_matcher_with_parameters()
    {
        $this->supports('beRestViewWith', null, [])->shouldReturn(true);
    }

    function it_should_match()
    {
        $subject = [
            'data' => ['username' => 'drymek'],
            'statusCode' => Codes::HTTP_OK,
            'serializationGroups' => ['user_profile'],
            'headers' => [
                'cache-control' => ['no-cache'],
                'date' => ["@string@.isDateTime()"],
            ]
        ];

        $arguments = [$subject];
        $view = View::create($subject['data'], $subject['statusCode'], []);
        $context = new SerializationContext();
        $context->setGroups($subject['serializationGroups']);
        $view->setSerializationContext($context);

        $this->positiveMatch('', $view, $arguments);
    }

    function it_should_thow_exception_on_invalid_data()
    {
        $subject = [
            'data' => ['username' => 'drymek'],
            'statusCode' => Codes::HTTP_OK,
            'serializationGroups' => ['user_profile'],
            'headers' => [
                'cache-control' => ['no-cache'],
                'date' => ["@string@.isDateTime()"],
            ]
        ];

        $arguments = [$subject];
        $view = View::create([], $subject['statusCode'], []);
        $context = new SerializationContext();
        $context->setGroups($subject['serializationGroups']);
        $view->setSerializationContext($context);

        $this->shouldThrow(new FailureException('Expected  to be a data of the View, but it is not. Instead got: '))->during('positiveMatch', ['', $view, $arguments]);
    }

    function it_should_thow_exception_on_invalid_status_code()
    {
        $subject = [
            'data' => ['username' => 'drymek'],
            'statusCode' => Codes::HTTP_OK,
            'serializationGroups' => ['user_profile'],
            'headers' => [
                'cache-control' => ['no-cache'],
                'date' => ["@string@.isDateTime()"],
            ]
        ];

        $arguments = [$subject];
        $view = View::create($subject['data'], 100, []);
        $context = new SerializationContext();
        $context->setGroups($subject['serializationGroups']);
        $view->setSerializationContext($context);

        $this->shouldThrow(new FailureException('Expected  to be a status code of the View, but it is not. Instead got: '))->during('positiveMatch', ['', $view, $arguments]);
    }

    function it_should_thow_exception_on_invalid_headers()
    {
        $subject = [
            'data' => ['username' => 'drymek'],
            'statusCode' => Codes::HTTP_OK,
            'serializationGroups' => ['user_profile'],
            'headers' => [
                'cache-control' => ['no-cache'],
                'date' => ["@string@.isDateTime()"],
            ]
        ];

        $arguments = [$subject];
        $view = View::create($subject['data'], $subject['statusCode'], ['X-Custom' => 'Value']);
        $context = new SerializationContext();
        $context->setGroups($subject['serializationGroups']);
        $view->setSerializationContext($context);

        $this->shouldThrow(new FailureException('Expected headers to be , but it is not. Instead got: . Details: There is no element under path [x-custom] in pattern.'))->during('positiveMatch', ['', $view, $arguments]);
    }

    function it_should_thow_exception_on_invalid_serialization_group()
    {
        $subject = [
            'data' => ['username' => 'drymek'],
            'statusCode' => Codes::HTTP_OK,
            'serializationGroups' => ['user_profile'],
            'headers' => [
                'cache-control' => ['no-cache'],
                'date' => ["@string@.isDateTime()"],
            ]
        ];

        $arguments = [$subject];
        $view = View::create($subject['data'], $subject['statusCode'], []);
        $context = new SerializationContext();
        $context->setGroups(['other']);
        $view->setSerializationContext($context);

        $this->shouldThrow(new FailureException('Expected serialization group to be user_profile, but they are not'))->during('positiveMatch', ['', $view, $arguments]);
    }
}
