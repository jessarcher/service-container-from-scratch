# Service Container from Scratch

Source code from my Brisbane Laravel Meetup talk.

Check out the recording! https://youtu.be/sMYmZjfqPN0?t=2960

The talk starts with an almost empty `app/Container/Container.php` and a complete test suite at `tests/Container/ContainerTest.php`.

In my talk, I go through each test and write the code to make it work, up until the final test on dependency injection. At that point I copy in some prewritten code from the `stubs/ContainerFull.php` file that is a bit too full-on to live code.

I have committed several stages of the container in the `stubs` directory and a `setup` script that makes it easy to reset the repo to a known state.

Any questions, let me know on Twitter [@jessarchercodes](https://twitter.com/jessarchercodes)
