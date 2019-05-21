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

1. MLP2 must contain the changes in [`7dccc9c`][`inpsyde/MultilingualPress@7dccc9c`].

    This is necessary in order to prevent automatic deletion of the `site_relations` table
    by MLP2 on uninstall. This table has the same name in MLP2 and MLP3, and therefore
    should remain after MLP2 is uninstalled.

1. This plugin must be active.

    It registers the WP CLI command, and is also necessary in order to satisfy requirement *3*.

1. MLP3 must be active during migration.

    - The tool assumes that the tables to migrate the data into have already been created.
    - The tool uses some info exposed by MLP3 classes:
        * Languages table structure, to create the temporary table.
        
1. All options tables must have the same collation.

    This is necessary in order to run a `UNION` query on them, which is needed for
    migrating redirections.

## Usage
In the root of your WP installation, run the following command to see all available arguments and flags:

```
wp help mlp2to3
```

## Known Limitations

1. When migrating the language repository, most languages will be migrated.

    Ideally, only the custom (modified) languages would be migrated. However,
    in the current state it is not possible to determine which languages are
    different from their defaults. Due to inconsistencies between language
    defaults in MLP2 vs MLP3, the best possible comparison strategy determines
    most MLP2 languages to be different from those in MLP3 defaults. This
    results in the custom languages being migrated, but also over a hundred
    others.


[`inpsyde/multilingualpress@eebfc1b`]: https://bitbucket.org/inpsyde/multilingualpress/commits/eebfc1b9caba54e028afc491fd3005d722a89995
[`inpsyde/MultilingualPress@7dccc9c`]: https://github.com/inpsyde/MultilingualPress/commit/7dccc9ce10b0f361369e4987371312d859a9d73c
