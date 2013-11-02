FOSMessageBundle Upgrading Notes
================================

To 3.0.0 from 1.2.x

- BC BREAK: DataTransformer, FormFactory, FormHandler, FormModel, FormType subnamespaces were moved to a common Form namespace
- BC BREAK: `ParticipantInterface#getId` was renamed to `getParticipantId`
- BC BREAK: `Document\Thread` and `Entity\Thread` no longer return Participants as an array but as a Doctrine Collection
- BC BREAK: Removed `Entity\Thread#getParticipantsCollection` in favour of `Thread#getParticipants`
- BC BREAK: Added method getMetadataForParticipant to `Model\MessageInterface`
- BC BREAK: Added method getAllMetadata to `Model\ThreadInterface`
- BC BREAK: Added method getClass to `ModelManager\ThreadManagerInterface`
- BC BREAK: `Twig\Extension\MessageExtension` was moved to `Twig\MessageExtension`

- Removed legacy mongodb migration command. Upgrade to 1.2.x before upgrading to 3.0 if you're using the legacy mongodb format
- Moved Denormalizing functions from Managers to their own dedicated services
- Common methods in Entity\Thread and Document\Thread were moved to Model\Thread
- Common methods in Entity\Message and Document\Message were moved to Model\Message
- Model\MessageInterface no longer enforces implementation of getId
