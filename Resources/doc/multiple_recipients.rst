Configuring multiple recipients support:
========================================

Configure your application::

    # app/config/config.yml

    ornicar_message:
        db_driver: orm
        thread_class: Acme\MessageBundle\Entity\Thread
        message_class: Acme\MessageBundle\Entity\Message
        new_thread_form:
          type:               ornicar_message.new_thread_multiple_form.type
          handler:            ornicar_message.new_thread_multiple_form.handler
          model:              Ornicar\MessageBundle\FormModel\NewThreadMultipleMessage
          name:               message


Currently multiple functionality is based on FOSUserBundle but you can define custom form type for multiple recipients and use your own recipients transformer