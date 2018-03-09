ResourceManager
===============

Nothing here of actual use yet.

Management of HTTP resources referenced by PHP Web Applications

Most of the info is in the file [JS_JSON.md](JS_JSON.md)

The basic concept though is that re-used third party JavaScript has a JSON
configuration file with information about the script.

From that JSON, a class that implements the JavaScriptResource interface would
create a PHP object that can be used both to create the needed script node and
optionally by a PHP wrapper to serve the file if serving from the same host the
web app is running on.

A not yet written interface called ResourceManager would be responsible for
finding the configuration file when the web application wants it, much like a
PSR-4 autoloader does, and creates the object.

The directory lib/Face contains a proto-interface for creating an object.

The directory lib/Stract contains an abstract class for managing resources.

The directory lib/ contains a proto class that extends the abstract class and
also implements the interface.

The github
[AliceWonderMiscreations/CommonJS](https://github.com/AliceWonderMiscreations/CommonJS)
has jQuery in it that I plan to use when I experiment with getting this working.

Thoughts are appreciated.

Tests are in the file [test123.php](test123.php) using content from the
CommonJS github mentioned above. Hard-coded file paths, you will have to
modify them to your test needs.


