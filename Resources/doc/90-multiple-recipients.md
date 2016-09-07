Configuring multiple recipients support
=======================================

Configure your application

```yaml
# app/config/config.yml

fos_message:
    db_driver: orm
    thread_class: AppBundle\Entity\Thread
    message_class: AppBundle\Entity\Message
    new_thread_form:
      type:               FOS\MessageBundle\FormType\NewThreadMultipleMessageFormType
      handler:            fos_message.new_thread_multiple_form.handler
      model:              FOS\MessageBundle\FormModel\NewThreadMultipleMessage
      name:               message
```

Currently multiple functionality is based on FOSUserBundle but you can define custom form type for
multiple recipients and use your own recipients transformer.
