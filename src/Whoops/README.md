# Whoops - Better PHP Error Messages

In Local Environments (and local environments only), you can enable [Whoops Error Messaging](http://filp.github.io/whoops/).

Whoops provides a cleaner, more helpful interface for viewing errors when they happen,
including a clear callstack, server details at the time of the error, preview of the code
that errored, and all sorts of other goodies.

To enable Whoops, simply add `define( 'WHOOPS_ENABLE', true );` to your local-config.php.
