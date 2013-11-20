Todo this is just a draft for myself

All the information below only applies if you extended classes and use the orm model for now
----------------------------------------------------------------


For those who extended FormHandlers
------------------------------------
The code in the form handlers have changed so you'll need to extend new classes and update your code

For those who extended the builders
-------------------------------------
There is an example class about how to extend the current builders.

The composer service is no longer used
-----------------------
The orm version doesn't use the composer service anymore

The sender service is no longer used
-------------------------------------
We no longer use the sender classes

Sending a message programatically has changed
----------------------------------------------
TODO basicly request the builder class, build it, request the actionsmanager class and you are done.
TODO list interface changes
