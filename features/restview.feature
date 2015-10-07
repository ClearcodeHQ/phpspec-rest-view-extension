Feature: Set Rest View expectations
  In order to build Rest API
  As a developer
  I need a way to set controller's action expectation

  Background:
	Given a file named "phpspec.yml" with:
	"""
	extensions:
	  - Codifico\PhpSpec\RestViewExtension\Extension
	suites:
	  default:
	    src_path: %paths.config%/Controller
	"""
	And a file named "vendor/autoload.php" with:
	"""
	<?php
	require __DIR__ . '/../Controller/UserController.php';
	"""
	And a file named "spec/Controller/UserControllerSpec.php" with:
	"""
	<?php

	namespace spec\Controller;

	use FOS\RestBundle\Util\Codes;
	use PhpSpec\ObjectBehavior;

	class UserControllerSpec extends ObjectBehavior
	{
		function let()
		{
			$this->beConstructedWith(['username' => 'drymek']);
		}
		
		function it_show_the_current_user()
		{
			$this->getUser()->shouldBeRestViewWith([
				'data' => ['username' => 'drymek'],
				'statusCode' => Codes::HTTP_OK,
				'serializationGroups' => ['user_profile'],
				'headers' => [
					'cache-control' => ['no-cache'],
					'date' => ["@string@.isDateTime()"],
				]
			]);
		}
	}
	"""

  Scenario: Met the expectation
	Given a file named "Controller/UserController.php" with:
	"""
	<?php

	namespace Controller;

	use FOS\RestBundle\Util\Codes;
	use FOS\RestBundle\View\View;
	use JMS\Serializer\SerializationContext;

	class UserController
	{
		private $user = [];

		public function __construct(array $user)
		{
			$this->user = $user;
		}

		/**
		 * @return \FOS\RestBundle\View\View
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
	"""
	When I run "phpspec run --format=tap"
	Then it should pass with:
	"""
	TAP version 13
	ok 1 - Controller\UserController: show the current user
	1..1
	"""

  Scenario: Miss the data
	Given a file named "Controller/UserController.php" with:
	"""
	<?php

	namespace Controller;

	use FOS\RestBundle\Util\Codes;
	use FOS\RestBundle\View\View;
	use JMS\Serializer\SerializationContext;

	class UserController
	{
		private $user = [];

		public function __construct(array $user)
		{
			$this->user = $user;
		}

		/**
		 * @return \FOS\RestBundle\View\View
		 */
		public function getUser()
		{
			$context = SerializationContext::create();
			$context->setGroups('user_profile');

			$view = View::create([], Codes::HTTP_OK, []);
			$view->setSerializationContext($context);

			return $view;
		}
	}
	"""
	When I run "phpspec run --format=tap"
	Then it should fail with:
	"""
	TAP version 13
	not ok 1 - Controller\UserController: show the current user
	  ---
	  message: 'Expected [array:1] to be a data of the View, but it is not. Instead got: [array:0]'
	  severity: fail
	  ...
	1..1
	"""

  Scenario: Miss the status code
	Given a file named "Controller/UserController.php" with:
	"""
	<?php

	namespace Controller;

	use FOS\RestBundle\Util\Codes;
	use FOS\RestBundle\View\View;
	use JMS\Serializer\SerializationContext;

	class UserController
	{
		private $user = [];

		public function __construct(array $user)
		{
			$this->user = $user;
		}

		/**
		 * @return \FOS\RestBundle\View\View
		 */
		public function getUser()
		{
			$context = SerializationContext::create();
			$context->setGroups('user_profile');

			$view = View::create($this->user, 2, []);
			$view->setSerializationContext($context);

			return $view;
		}
	}
	"""
	When I run "phpspec run --format=tap"
	Then it should fail with:
	"""
	TAP version 13
	not ok 1 - Controller\UserController: show the current user
	  ---
	  message: 'The HTTP status code "2" is not valid.'
	  severity: fail
	  ...
	1..1
	"""

  Scenario: Miss the serialization group
	Given a file named "Controller/UserController.php" with:
	"""
	<?php

	namespace Controller;

	use FOS\RestBundle\Util\Codes;
	use FOS\RestBundle\View\View;
	use JMS\Serializer\SerializationContext;

	class UserController
	{
		private $user = [];

		public function __construct(array $user)
		{
			$this->user = $user;
		}

		/**
		 * @return \FOS\RestBundle\View\View
		 */
		public function getUser()
		{
			$context = SerializationContext::create();
			$context->setGroups('other_group');

			$view = View::create($this->user, Codes::HTTP_OK, []);
			$view->setSerializationContext($context);

			return $view;
		}
	}
	"""
	When I run "phpspec run --format=tap"
	Then it should fail with:
	"""
	TAP version 13
	not ok 1 - Controller\UserController: show the current user
	  ---
	  message: 'Expected serialization group to be user_profile, but they are not'
	  severity: fail
	  ...
	1..1
	"""

  Scenario: Miss the header
	Given a file named "Controller/UserController.php" with:
	"""
	<?php

	namespace Controller;

	use FOS\RestBundle\Util\Codes;
	use FOS\RestBundle\View\View;
	use JMS\Serializer\SerializationContext;

	class UserController
	{
		private $user = [];

		public function __construct(array $user)
		{
			$this->user = $user;
		}

		/**
		 * @return \FOS\RestBundle\View\View
		 */
		public function getUser()
		{
			$context = SerializationContext::create();
			$context->setGroups('user_profile');

			$view = View::create($this->user, Codes::HTTP_OK, ['X-Custom' => 'Value']);
			$view->setSerializationContext($context);

			return $view;
		}
	}
	"""
	When I run "phpspec run --format=tap"
	Then it should fail with:
	"""
	TAP version 13
	not ok 1 - Controller\UserController: show the current user
	  ---
	  message: 'Expected headers to be [array:2], but it is not. Instead got: [array:2]. Details: There is no element under path [x-custom] in pattern.'
	  severity: fail
	  ...
	1..1
	"""
