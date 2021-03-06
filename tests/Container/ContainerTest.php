<?php

namespace Tests\Container;

use App\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /** @test */
    public function the_problems_with_new()
    {
        /*
         * The problems with using the "new" keyword in your application code (e.g. controllers, jobs, etc.)
         *
         * - We can't swap it for a mocked version it in a test.
         * - We can't make it a singleton if needed.
         * - We can't depend on an abstract interface like a MailerInterface.
         * - We can't use dependency injection - constructor args need to be provided every time we need an instance.
         * - We don't have a central place to configure constructor dependencies (i.e. we don't have "inversion of control").
         */

        $this->expectExceptionMessage('Too few arguments');
        new SmtpMailer();
    }







    /** @test */
    public function the_container_must_be_a_singleton()
    {
        $instance1 = Container::getInstance();
        $instance2 = Container::getInstance();

        $this->assertSame($instance1, $instance2);

        $this->expectErrorMessage('Call to private');
        new Container();
    }












    /** @test */
    public function we_can_provide_instructions_for_resolving_a_class()
    {
        $container = Container::getInstance();

        /*
         * - Great when a class needs help to be instantiated, such as pulling in config values.
         * - This is an example of "inversion of control" or IoC.
         * - Note: The closure is only called when we "resolve" or "make" the class.
         */
        $container->bind(SmtpMailer::class, function () {
            return new SmtpMailer('mail.example.com');
        });

        /*
         * The logic for creating the instance is now centralised and shared.
         */
        $smtpMailer = $container->make(SmtpMailer::class);
        $this->assertInstanceOf(SmtpMailer::class, $smtpMailer);

        /*
         * Double check that we get a new instance each time
         */
        $anotherSmtpMailer = $container->make(SmtpMailer::class);
        $this->assertNotSame($smtpMailer, $anotherSmtpMailer);
    }

    /** @test */
    public function we_can_also_use_a_string_for_a_key()
    {
        $container = Container::getInstance();

        $container->bind('mailer', fn () => new SmtpMailer('mail.example.com'));

        $smtpMailer = $container->make('mailer');

        $this->assertInstanceOf(SmtpMailer::class, $smtpMailer);
    }

    /** @test */
    public function we_can_also_bind_an_interface_to_a_concretion()
    {
        // Note: Just because we can use interfaces, doesn't mean we always should!

        $container = Container::getInstance();

        $container->bind(MailerInterface::class, fn () => new SmtpMailer('mail.example.com'));

        $smtpMailer = $container->make(MailerInterface::class);

        $this->assertInstanceOf(SmtpMailer::class, $smtpMailer);

        /*
         * What have we got so far?
         * - [x] IoC
         * - [x] Ability to depend on an abstraction or even an arbitrary string name
         * - [x] Ability to substitute a mock version in a test*
         * - [ ] Ability to create singletons
         * - [ ] Recursive dependency injection/resolution
         */
    }

    /** @test */
    public function we_can_pass_a_concrete_class_as_the_second_parameter()
    {
        $container = Container::getInstance();

        /*
         * Sometimes we don't need to provide instructions for resolving a class
         */
        $container->bind(MailerInterface::class, ArrayMailer::class);

        $smtpMailer = $container->make(MailerInterface::class);

        $this->assertInstanceOf(ArrayMailer::class, $smtpMailer);
    }









    /** @test */
    public function we_can_also_make_classes_weve_never_seen_aka_zero_config_resolution()
    {
        $container = Container::getInstance();

        $smtpMailer = $container->make(ArrayMailer::class);

        $this->assertInstanceOf(ArrayMailer::class, $smtpMailer);
    }














    /** @test */
    public function we_can_recursively_resolve()
    {
        $container = Container::getInstance();

        $container->bind(MailerInterface::class, SmtpMailer::class);
        $container->bind(SmtpMailer::class, fn () => new SmtpMailer('smtp.example.com'));

        $mailer = $container->make(MailerInterface::class);

        $this->assertInstanceOf(SmtpMailer::class, $mailer);
    }











    /** @test */
    public function we_can_also_bind_a_singleton()
    {
        $container = Container::getInstance();

        $container->singleton(SmtpMailer::class, fn () => new SmtpMailer('mail.example.com'));

        $smtpMailer1 = $container->make(SmtpMailer::class);
        $smtpMailer2 = $container->make(SmtpMailer::class);

        $this->assertSame($smtpMailer1, $smtpMailer2);
        $this->assertInstanceOf(SmtpMailer::class, $smtpMailer1);
    }










    /** @test */
    public function test_it_binds_a_singleton_by_passing_the_instance()
    {
        $container = Container::getInstance();

        $instance = new ArrayMailer();
        $container->instance(ArrayMailer::class, $instance);
        $resolved = $container->make(ArrayMailer::class);

        $this->assertSame($instance, $resolved);
    }













    /** @test */
    public function test_it_binds_a_singleton_by_class_name_only()
    {
        $container = Container::getInstance();

        $container->singleton(ArrayMailer::class);

        $smtpMailer1 = $container->make(ArrayMailer::class);
        $smtpMailer2 = $container->make(ArrayMailer::class);

        $this->assertSame($smtpMailer1, $smtpMailer2);
        $this->assertInstanceOf(ArrayMailer::class, $smtpMailer1);
    }










    /** @test */
    public function test_it_does_dependency_injection()
    {
        $container = Container::getInstance();

        $mailer = $container->make(ApiMailer::class);

        $this->assertInstanceOf(ApiMailer::class, $mailer);
    }

    /*
     * Other things Laravel's container can do
     *
     * - Contextual bindings (when X asks for Y)
     * - Primitive binding (E.g. '$server')
     * - Extended bindings (decorate existing bindings)
     * - Aliases
     * - Extra dependency resolution cases (e.g. variadic arguments)
     * - Events
     * - Tags
     */
}

interface MailerInterface
{
    public function send($message);
}

class ArrayMailer implements MailerInterface
{
    public function send($message)
    {
        // ..
    }
}

class SmtpMailer implements MailerInterface
{
    public function __construct(public string $server)
    {
    }

    public function send($message)
    {
        // ...
    }
}

class ApiMailer implements MailerInterface
{
    public function __construct(public Api $api)
    {
    }

    public function send($message)
    {
        // ...
    }
}

class Api
{
}
