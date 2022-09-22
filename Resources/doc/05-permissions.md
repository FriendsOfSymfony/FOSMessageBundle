Messaging permissions
======================

The default permissions authorizer service will authenticate a user if they're a
participant of the thread and is very permissive by default.

You can implement your own permissions service to replace the built in service and tell
FOSMessageBundle about it:

```yaml
# config/fos_message.yaml

fos_message:
    authorizer: app.authorizer
```

Any such service must implement `FOS\MessageBundle\Security\AuthorizerInterface`.

[Return to the documentation index](00-index.md)
