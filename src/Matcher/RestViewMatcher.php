<?php

namespace Codifico\PhpSpec\RestViewExtension\Matcher;

use Codifico\PhpSpec\RestViewExtension\Serializer\PropertyMetadataFake;
use Coduo\PHPMatcher\Factory;
use FOS\RestBundle\View\View;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Formatter\Presenter\PresenterInterface;
use PhpSpec\Matcher\BasicMatcher;

class RestViewMatcher extends BasicMatcher
{
    /**
     * @var \PhpSpec\Formatter\Presenter\PresenterInterface
     */
    private $presenter;

    /**
     * @var \Coduo\PHPMatcher\Matcher
     */
    private $matcher;

    /**
     * @var FailureException
     */
    private $exception;

    /**
     * @param PresenterInterface $presenter
     * @param Factory $factory
     */
    public function __construct(PresenterInterface $presenter, Factory $factory)
    {
        $this->presenter = $presenter;
        $this->matcher = $factory->createMatcher();
    }

    /**
     * @param mixed $subject
     * @param array $arguments
     *
     * @return boolean
     */
    protected function matches($subject, array $arguments)
    {
        if (!$this->isRestViewObject($subject)) {
            $this->exception = new FailureException(sprintf(
                'Expected %s to be an instance of FOS\RestBundle\View\View',
                $this->presenter->presentValue($subject)
            ));

            return false;
        }
        /** @var View $subject */

        $argument = isset($arguments[0]) ? $arguments[0] : [];

        if (!$this->dataMatches($subject, $argument)) {
            $this->exception = new FailureException(sprintf(
                'Expected %s to be a data of the View, but it is not. Instead got: %s',
                $this->presenter->presentValue($argument['data']),
                $this->presenter->presentValue($subject->getData())
            ));

            return false;
        }

        if (!$this->statusCodeMatches($subject, $argument)) {
            $this->exception = new FailureException(sprintf(
                'Expected %s to be a status code of the View, but it is not. Instead got: %s',
                $this->presenter->presentValue($argument['statusCode']),
                $this->presenter->presentValue($subject->getStatusCode())
            ));

            return false;
        }

        if (!$this->headersMatches($subject, $argument)) {
            $this->exception = new FailureException(sprintf(
                'Expected headers to be %s, but it is not. Instead got: %s. Details: %s',
                $this->presenter->presentValue($argument['headers']),
                $this->presenter->presentValue($subject->getHeaders()),
                $this->matcher->getError()
            ));

            return false;
        }

        if (!$this->serializationGroupsMatches($subject, $argument)) {
            $this->exception = new FailureException(sprintf(
                'Expected serialization group to be %s, but they are not',
                empty($argument['serializationGroups']) ? 'empty (it\'s impossible!)' : implode(', ', $argument['serializationGroups'])
            ));

            return false;
        }
    }

    /**
     * @param string $name
     * @param mixed $subject
     * @param array $arguments
     *
     * @return FailureException
     */
    protected function getFailureException($name, $subject, array $arguments)
    {
        return $this->exception;
    }

    /**
     * @param string $name
     * @param mixed $subject
     * @param array $arguments
     *
     * @return FailureException
     */
    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException('Invalid expectation');
    }

    /**
     * Checks if matcher supports provided subject and matcher name.
     *
     * @param string $name
     * @param mixed $subject
     * @param array $arguments
     *
     * @return Boolean
     */
    public function supports($name, $subject, array $arguments)
    {
        return 'beRestView' === $name || 'beRestViewWith' === $name;
    }

    private function isRestViewObject($subject)
    {
        return $subject instanceof View;
    }

    private function dataMatches(View $subject, $argument)
    {
        if (isset($argument['data'])) {
            return $subject->getData() === $argument['data'];
        }

        return true;
    }

    private function statusCodeMatches(View $subject, $argument)
    {
        if (isset($argument['statusCode'])) {
            return $subject->getStatusCode() === $argument['statusCode'];
        }

        return true;
    }

    private function headersMatches(View $subject, $argument)
    {
        if (isset($argument['headers'])) {
            return $this->matcher->match($subject->getHeaders(), $argument['headers']);
        }

        return true;
    }

    private function serializationGroupsMatches(View $subject, $argument)
    {
        if (isset($argument['serializationGroups'])) {
            if (empty($argument['serializationGroups'])) {
                return false;
            }

            foreach ($argument['serializationGroups'] as $group) {
                $property = new PropertyMetadataFake();
                $property->groups = [$group];
                $context = $subject->getSerializationContext();

                if ($context->getExclusionStrategy()->shouldSkipProperty($property, $context)) {
                    return false;
                }
            }
        }

        return true;
    }
}
