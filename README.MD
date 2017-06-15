Triggmine integration for Magento2
==========================================

1. Go to our official Magento 2.0 public repository.

2. Download it as a ZIP.

3. Unzip the file.

4. Make sure to create the directory structure in your Magento - 'Magento_Root/app/code/Triggmine/Connector'.

5. Drop/move all of the content inside the unzipped folder to directory 'Magento_Root/app/code/Triggmine/Connector' on FTP.

6. Run the command 'php bin/magento module:enable Triggmine_Connector' in Magento root. This will let Magento know about the module.

7. Run the command 'php bin/magento setup:upgrade' in Magento root. This will ensure any installer scripts we may have are executed properly and store the current data version.

8. Run the command 'php bin/magento setup:di:compile' if you have a single website and store, or 'php bin/magento setup:di:compile-multi-tenant' if you have multiple ones.

9. Clear cache from Magento admin.

10. Go to Stores > Configuration > TRIGGMINE > API Credentials and enter the API credentials.