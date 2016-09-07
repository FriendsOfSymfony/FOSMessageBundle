Upgrade from 1.2 to 1.3
=======================

The 1.3 version added the support for Symfony 3.0+. Several changes were made for the code to work with the new version.

 * **BC break**: `Controller\MessageController` now implements `Symfony\Component\DependencyInjection\ContainerAwareInterface`
   instead of extending the abstract class `Symfony\Component\DependencyInjection\ContainerAware` (removed in Symfony 3.0).
   If you relied on this (for instance by using `MessageController instanceof ContainerAware`), you may have to change
   your code.

 * Form types are now classes names instead of service references (the usage of service references is therefore deprecated).
   If you used your own form types for new threads and replies, you should update your configuration:

   Before:

   ```yaml
   fos_message:
       # ...
       
       new_thread_form:
           type: app.custom_new_thread_form_service
       reply_form:
           type: app.custom_reply_form_service
   ```

   After:

   ```yaml
   fos_message:
       # ...
       
       new_thread_form:
           type: AppBundle\Form\Type\NewThreadFormType
       reply_form:
           type: AppBundle\Form\Type\ReplyFormType
   ```
