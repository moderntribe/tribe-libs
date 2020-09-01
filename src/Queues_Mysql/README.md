# Queues - MySQL Backend

Registers the MySQL backend to store queue tasks.

Tasks are stored in a table in the MySQL database. Use the filter
`tribe/queues/mysql/table_name` to customize the table name, which
defaults to `$prefix_queue`

## Additional CLI Commands

`wp s1 queues add-table`
Creates the necessary MySQL table for using a MySQL backend.
