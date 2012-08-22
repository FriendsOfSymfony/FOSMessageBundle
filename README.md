Documentation: https://github.com/FriendsOfSymfony/FOSMessageBundle/blob/master/Resources/doc/index.rst

Note: Symfony 2.0 users should use the `2.0` branch of this bundle. The `master` branch tracks Symfony 2.1.

---

Provides messenging features for your Symfony2 application.

The persistence is storage agnostic. Any backend may be implemented: Doctrine, Propel, etc.
Presently, only the MongoDB implementation is complete.

MessageBundle supports threads, spam detection, soft deletion and messenging permissions.

MessageBundle can be used with FOSUserBundle, but it is not required.

**Note:** PR #32 introduced significant schema changes for the MongoDB model
layer. The ``fos:message:mongodb:migrate:metadata`` console command may be
used to migrate your existing schema. Please refer to the command's help message
for additional information.

---

For documentation, see:

    Resources/doc/index.rst

License:

    Resources/meta/LICENSE
