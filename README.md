Documentation: https://github.com/ornicar/OrnicarMessageBundle/blob/master/Resources/doc/index.rst

---

Provides messenging features for your Symfony2 application.

The persistence is storage agnostic. Any backend may be implemented: Doctrine, Propel, etc.
Presently, only the MongoDB implementation is complete.

MessageBundle supports threads, spam detection, soft deletion and messenging permissions.

MessageBundle can be used with FOSUserBundle, but it is not required.

**Note:** PR #32 introduced significant schema changes for the MongoDB model
layer. The ``ornicar:message:mongodb:migrate:metadata`` console command may be
used to migrate your existing schema. Please refer to the command's help message
for additional information.

---

For documentation, see:

    Resources/doc/index.rst

License:

    Resources/meta/LICENSE
