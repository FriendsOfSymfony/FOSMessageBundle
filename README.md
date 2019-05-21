FOSMessageBundle
================

This bundle provides messaging features for a Symfony application. Features available include:

- Support for both the Doctrine ORM and ODM for message storage
- Threaded conversations
- Spam detection support
- Soft deletion of threads
- Permissions for messaging.

[![Build Status](https://travis-ci.org/FriendsOfSymfony/FOSMessageBundle.png?branch=master)](https://travis-ci.org/FriendsOfSymfony/FOSMessageBundle) [![Total Downloads](https://poser.pugx.org/FriendsOfSymfony/message-bundle/downloads.png)](https://packagist.org/packages/FriendsOfSymfony/message-bundle) [![Latest Stable Version](https://poser.pugx.org/FriendsOfSymfony/message-bundle/v/stable.png)](https://packagist.org/packages/FriendsOfSymfony/message-bundle)

Documentation
-------------

Documentation for this bundle is stored under `Resources/doc` in this repository.

[Read the documentation for the last stable (2.0)][]

Legacy (Symfony 2, or <=3.3)
------

Due to difficulties in CI testing, deprecation of the `Controller` class and the [deprecation coming November of this year (2019)][] support for anything older than Symfony3.4 (LTS) has been dropped in this bundle.

For more info, see the pull request that ultimately made the decision for us - https://github.com/FriendsOfSymfony/FOSMessageBundle/pull/340

If using versions older than Symfony3.4 (LTS), make sure to use version 1.3 of this bundle.

https://github.com/FriendsOfSymfony/FOSMessageBundle/tree/v1.3.0

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

```
Resources/meta/LICENSE
```

[Read the documentation for the last stable (2.0)]: https://github.com/FriendsOfSymfony/FOSMessageBundle/blob/master/Resources/doc/00-index.md

[deprecation coming November of this year (2019)]: https://symfony.com/roadmap/2.8