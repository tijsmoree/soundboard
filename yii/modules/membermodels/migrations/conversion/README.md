Convert database `leden_new` to `members`
-----------------------------------------

The table names `leden_new` and `members` are hardcoded in the script!

## Workflow
How to convert the old database model to the new database model:

1. Make sure both `leden_new` and `members` database, its tables, and the fields of the tables are of the same collation type (preferable `utf8_unicode_ci`)
1. BACKUP THE COMPLETE `members` DATABASE. So in case something goes wrong, you can always go back.
1. Do the query `SET foreign_key_checks=0` in `members`
1. Manually truncate ALMOST ALL tables in `members`, BUT DON'T TRUNCATE `migration`, `queries` and `persons`!! But if you don't have existing persons in the `persons` table which id's you want to keep, you can truncate all tables.
1. Did you keep the table `persons`? If yes, run script 1b, else run script 1a. 
1. If you ran script 1b, truncate the table `persons` in `members`.
1. Do the query `SET foreign_key_checks=1` in `members`
1. Run script 2 in table `members`
1. Run script `insert_debtor_codes.sql`
1. Done!
