# MultilingualPress 2 to 3 Migration
A WP plugin that allows migrating data from MultilingualPress version 2 to version 3.

## Usage
In the root of your WP installation, run the following command to see all available arguments and flags:

```
wp help mlp2to3
```

It is a *requirement* that MLP3 is active during migration:

- It assumes that the tables to migrate the data into have already been created.
- It uses some info exposed by MLP3 classes:
    * Languages table structure, to create the temporary table.
