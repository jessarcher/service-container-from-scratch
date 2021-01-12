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
         * The problems with using "new" in your application code (e.g. controllers, jobs, etc.)
         *
         * - We can't swap it for a mock it in a test.
         * - We can't make it a singleton if needed.
         * - We can't depend on an abstract interface like a MailerInterface - we need to know the exact implementation (e.g. SmtpMailer).
         * - We can't use dependency injection, so the constructor arguments need to be provided every time we need an instance.
         * - We also don't have a central place to configure them (we don't have "inversion of control").
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
        // $this->markTestSkipped();

        $container = Container::getInstance();

        /*
         * Now that we have a singleton service container it's time to start binding things.
         * Let's start with binding using a closure.
         * This is really useful when we have a class that needs some logic to be instantiated, such as pulling in config values.
         * This is an example of "inversion of control" or IoC because we're moving control of how this is instantiated to a central place instead of duplicating it.
         * Because we're using a closure, it will only be called when we resolve or make the class.
         */
        $container->bind(SmtpMailer::class, function () {
            return new SmtpMailer('mail.example.com');
        });

        /*
         * And here we ask the container to give us an instance
         * Our business logic that requires this class no longer needs to know about what's involved to resolve this
         */
        $smtpMailer = $container->make(SmtpMailer::class);

        $this->assertInstanceOf(SmtpMailer::class, $smtpMailer);

        /*
         * Double check that we get a new instance
         */
        $anotherSmtpMailer = $container->make(SmtpMailer::class);

        $this->assertNotSame($smtpMailer, $anotherSmtpMailer);

        /*
         * This gives us IoC and the ability to mock by binding something different in a test scenario.
         */
    }








    /** @test */
    public function we_can_also_use_a_string_for_a_key()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        $container->bind('mailer', fn () => new SmtpMailer('mail.example.com'));

        $smtpMailer = $container->make('mailer');

        $this->assertInstanceOf(SmtpMailer::class, $smtpMailer);
    }


















    /** @test */
    public function we_can_also_bind_an_interface_to_a_concretion()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        $container->bind(MailerInterface::class, fn () => new SmtpMailer('mail.example.com'));

        $smtpMailer = $container->make(MailerInterface::class);

        $this->assertInstanceOf(SmtpMailer::class, $smtpMailer);

        /* Just because we can use interfaces, doesn't mean we should! */
    }













    /** @test */
    public function we_can_pass_a_concrete_class_as_the_second_parameter()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        $container->bind(MailerInterface::class, ArrayMailer::class);

        $smtpMailer = $container->make(MailerInterface::class);

        $this->assertInstanceOf(ArrayMailer::class, $smtpMailer);
    }













    /** @test */
    public function we_can_also_make_classes_weve_never_seen_aka_zero_config_resolution()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        /*
         * If the container doesn't need instructions for resolving a class, it doesn't need to be registered at all.
         * This is handy because we can still add a binding just within a test to swap in a mock.
         */

        $smtpMailer = $container->make(ArrayMailer::class);

        $this->assertInstanceOf(ArrayMailer::class, $smtpMailer);
    }















    /** @test */
    public function we_can_recursively_resolve()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        $container->bind(MailerInterface::class, SmtpMailer::class);
        $container->bind(SmtpMailer::class, fn () => new SmtpMailer('smtp.example.com'));

        $mailer = $container->make(MailerInterface::class);

        $this->assertInstanceOf(SmtpMailer::class, $mailer);
    }




















    /** @test */
    public function we_can_also_bind_a_singleton()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        /*
         * We talked about singletons earlier with the container itself.
         * Now let's give our container the ability to resolve singleton classes.
         * As above, we'll be providing a closure as the "instructions" for how to instantiate our class.
         * The only difference is that the closure will only be called the first time we resolve the class.
         * We will store the instance so that we can return it every other time.
         */
        $container->singleton(SmtpMailer::class, fn () => new SmtpMailer('mail.example.com'));

        $smtpMailer1 = $container->make(SmtpMailer::class);
        $smtpMailer2 = $container->make(SmtpMailer::class);

        $this->assertSame($smtpMailer1, $smtpMailer2);
        $this->assertInstanceOf(SmtpMailer::class, $smtpMailer1);
    }

















    /** @test */
    public function test_it_binds_a_singleton_by_passing_the_instance()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        /*
         * If we want instantiate our singleton manually at the time of registration instead of within a closure, we can do this too.
         * Useful when swapping an instance in a test
         */

        $instance = new ArrayMailer();
        $container->instance(ArrayMailer::class, $instance);
        $resolved = $container->make(ArrayMailer::class);

        $this->assertSame($instance, $resolved);
    }















    /** @test */
    public function test_it_binds_a_singleton_by_class_name_only()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        /*
         * If your singleton class can be instantiated without help, Laravel allows us to just specify the class name on its own and it will new it up for us
         */

        $container->singleton(ArrayMailer::class);

        $smtpMailer1 = $container->make(ArrayMailer::class);
        $smtpMailer2 = $container->make(ArrayMailer::class);

        $this->assertSame($smtpMailer1, $smtpMailer2);
        $this->assertInstanceOf(ArrayMailer::class, $smtpMailer1);
    }





































    /** @test */
    public function test_it_does_dependency_injection()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();

        $mailer = $container->make(ApiMailer::class);

        $this->assertInstanceOf(ApiMailer::class, $mailer);
    }



















    /** @test */
    public function test_it_throws_a_binding_resolution_exception_for_an_unregistered_concrete_class_with_unresolvable_dependencies()
    {
        // $this->markTestSkipped();

        $container = Container::getInstance();
        $container->flush();

        $this->expectException(\App\Container\BindingResolutionException::class);
        $this->expectExceptionMessage('Unresolvable dependency');

        $container->make(SmtpMailer::class);
    }
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
