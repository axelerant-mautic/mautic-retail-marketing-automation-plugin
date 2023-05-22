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
1. [Abandoned Cart](Docs/abandoned-cart.md) object as custom object
2. Segment to create a abandoned card customer list
3. Email with [token](Docs/token.md) that replaces the list of products
4. [Campaign](Docs/campaign.md)


Requirements
------------  

### Mautic

1. Mautic version 4 and onwards
2. Enable the API or Basic Auth
3. [Custom Object plugin][PluginCustomObjectsHome] stable version. The **Custom Object plugin** should be installed prior to installation of `RetailMarketingBundle`.

### Retail Marketing Automation

The integration should send a payload as per [this example](Docs/abandoned-cart.md#usage).


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

 -


Maintainers
-----------

1. Rahul Shinde, [Axelerant Technologies][AxelerantHome]

[MauticHome]: <https://www.mautic.org>
[AxelerantHome]: <https://axelerat.com>
[PluginCustomObjectsHome]: <https://github.com/acquia/mc-cs-plugin-custom-objects>