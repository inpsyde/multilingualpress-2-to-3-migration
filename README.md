# MultilingualPress 2 to 3 Migration
A WP plugin that allows migrating data from MultilingualPress version 2 to version 3.

## Installation
The migration tool is a WP CLI command, shipped as a regular WP plugin.
Install it as you would any other plugin, i.e. in any of the following ways:

- If a build is available, you can install from ZIP.
- Install with Composer: `composer require inpsyde/multilingualpress2to3:^0.1`.
- Clone the repo into your `plugins` directory.

## Requirements

1. MLP3 must contain the changes made in [`eebfc1b`][`inpsyde/multilingualpress@eebfc1b`].

    This is necessary in order to satisfy requirement *3*.

1. This plugin must be active.

    It registers the WP CLI command, and is also necessary in order to satisfy requirement *3*.

2. MLP3 must be active during migration.

    - The tool assumes that the tables to migrate the data into have already been created.
    - The tool uses some info exposed by MLP3 classes:
        * Languages table structure, to create the temporary table.
        
4. All options tables must have the same collation.

    This is necessary in order to run a `UNION` query on them, which is needed for
    migrating redirections.

## Usage
In the root of your WP installation, run the following command to see all available arguments and flags:

```
wp help mlp2to3
```

[`inpsyde/multilingualpress@eebfc1b`]: https://bitbucket.org/inpsyde/multilingualpress/commits/eebfc1b9caba54e028afc491fd3005d722a89995
