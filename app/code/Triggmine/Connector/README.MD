Installing from ZIP-archive
===========================

1. Put the ZIP-archive into the root directory and unpack it. To unpack, run the command in Magento root: `sudo unzip TriggMine-Magento-2-master.zip`

2. Then, run the command in Magento `root: php bin/magento module:enable Triggmine_Connector`
This will let Magento know about the module.

3. Run the command `php bin/magento setup:upgrade` in Magento root. This will ensure any installer scripts we may have are executed properly and store the current data version.

4. Run the command `php bin/magento setup:di:compile` if you have a single website and store, or `php bin/magento setup:di:compile-multi-tenant` if you have multiple ones.
**PLEASE NOTE:** DI configuration must be cleared before running compiler. Please delete /var/di , clear the cache and code generation directories.
For clearing, run the command in Magento root: `sudo rm -rf var/di var/generation/* var/cache/* var/page_cache/* var/view_preprocessed/* var/composer_home/cache/*`

5. For clearing cache run the command `php bin/magento cache: clean` and `php bin/magento cache: flush`
