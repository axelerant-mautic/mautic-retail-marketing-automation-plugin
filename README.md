CONTENTS OF THIS FILE
---------------------

* Introduction
* Requirements
* Installation
* Configuration
* FAQ
* Maintainers


Introduction
------------

This plugin is to add Retail Marketing automation flows into [Mautic][MauticHome] marketing automation tool.

The plugin depends on [Custom Object plugin][PluginCustomObjectsHome].

When enable this plugin will create the followings,
1. [Product](Docs/custom-object-product.md) object as custom object
2. Segment to create a Product card customer list
3. Email with [token](Docs/token.md) that replaces the list of products
4. [Campaign](Docs/campaign.md)


Requirements
------------  

### Mautic

1. Mautic version 4 and onwards
2. Enable the API or Basic Auth
3. [Custom Object plugin][PluginCustomObjectsHome] stable version. The **Custom Object plugin** should be installed prior to installation of `RetailMarketingBundle`.

### Retail Marketing Automation

The integration should send a payload as per [this example](Docs/custom-object-product.md#usage).


Installation
------------

1. Add `RetailMarketingBundle` in `plugins` directory.
2. Go to `Plugins` page
3. Click on `Install/Upgrade Plugins` button, and install `RetailMarketingBundle` plugin.

If you have shell access then execute `php bin\console mautic:plugins:reload` to install the plugin.


Configuration
-------------

Enable the plugin.


FAQ
---
**Question:** Will I be able to add more custom fields other than default fields?

**Answer:** Yes, you can. Please custom object document to add more fields


Maintainers
-----------

1. Rahul Shinde, [Axelerant Technologies][AxelerantHome]
2. Prateek Jain, [Axelerant Technologies][AxelerantHome]
3. Abhisek Dhariwal, [Axelerant Technologies][AxelerantHome]
4. Gaurav Chauhan, [Axelerant Technologies][AxelerantHome]
5. Jitesh Khatwani, [Axelerant Technologies][AxelerantHome]

[MauticHome]: <https://www.mautic.org>
[AxelerantHome]: <https://axelerant.com>
[PluginCustomObjectsHome]: <https://github.com/acquia/mc-cs-plugin-custom-objects>