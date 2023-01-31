Concrete classes for Doctrine ORM
=================================

This page lists some example implementations of FOSMessageBundle models for the Doctrine
ORM.

Given the examples below with their namespaces and class names, you need to configure
FOSMessageBundle to tell them about these classes.

Create and add the following config to your `config/fos_message.yaml` file.

```yaml
# config/fos_message.yaml

fos_message:
    db_driver: orm
    thread_class: App\Entity\Thread
    message_class: App\Entity\Message
```

[Continue with the installation][]

Message class
-------------

```php
<?php
// src/Entity/Message.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use FOS\MessageBundle\Entity\Message as BaseMessage;

#[ORM\Entity]
class Message extends BaseMessage
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var \FOS\MessageBundle\Model\ThreadInterface
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Thread', inversedBy: 'messages')]
    protected $thread;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\User')]
    protected $sender;

    /**
     * @var MessageMetadata[]|Collection
     */
    #[ORM\OneToMany(targetEntity: 'App\Entity\MessageMetadata', mappedBy: 'message', cascade: ['all'])]
    protected $metadata;
}
```

MessageMetadata class
---------------------

```php
<?php
// src/Entity/MessageMetadata.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\MessageMetadata as BaseMessageMetadata;

#[ORM\Entity]
class MessageMetadata extends BaseMessageMetadata
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var \FOS\MessageBundle\Model\MessageInterface
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Message', inversedBy: 'metadata')]
    protected $message;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\User')]
    protected $participant;
}
```

Thread class
------------

```php
<?php
// src/Entity/Thread.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use FOS\MessageBundle\Entity\Thread as BaseThread;

#[ORM\Entity]
class Thread extends BaseThread
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\User')]
    protected $createdBy;

    /**
     * @var Message[]|Collection
     */
    #[ORM\OneToMany(targetEntity: 'App\Entity\Message', mappedBy: 'thread')]
    protected $messages;

    /**
     * @var ThreadMetadata[]|Collection
     */
    #[ORM\OneToMany(targetEntity: 'App\Entity\ThreadMetadata', mappedBy: 'thread', cascade: ['all'])]
    protected $metadata;
}
```

ThreadMetadata class
--------------------

```php
<?php
// src/Entity/ThreadMetadata.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;

#[ORM\Entity]
class ThreadMetadata extends BaseThreadMetadata
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var \FOS\MessageBundle\Model\ThreadInterface
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Thread', inversedBy: 'metadata')]
    protected $thread;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\User')]
    protected $participant;
}
```

[Continue with the installation]: 01-installation.md
