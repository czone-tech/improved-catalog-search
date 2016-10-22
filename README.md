## Improved catalog search filteration for Magento2
This module allows the use of multiple values of the same filter in magento 2 layered navigation search.

If you have any issues using this module, you may contact us at support@czonetechnologies.com

###Why?
While Magento 2 comes with a lot of stuff out-of-the-box, the search filter options that it provides are not the best
. You are allowed to use only one value of a filter, this means that if you want to search the products by multiple 
brands, you cannot do so. 
 
 This extension aims to modify this behavior. It allows the user to select multiple filter options so that he can 
 search the products by multiple brands.

#### Demo
Live demo coming soon

####1 - Installation
##### Manual Installation

 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/CzoneTech
 * Extract the contents of the zipped folder inside it.


#####Using Composer

```
composer require czonetech/improved-catalog-search

```

####2 -  Enabling the module
Using command line access to your server, run the following commands -
```
 $ cd <magento-installation-dir>
 $ php bin/magento module:enable --clear-static-content CzoneTech_ImprovedCatalogSearch
 $ php bin/magento setup:upgrade
 $ rm -r var/di
 $ php bin/magento setup:di:compile
 $ php bin/magento cache:clean
```


## Screenshot
Will be posted soon