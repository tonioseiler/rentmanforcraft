# Rentman for Craft

Automatically Import Rentman Products to Craft. Let visitors create orders. Orders are automatically send to rentman as a project request.

## Requirements

This plugin requires Craft CMS 4.3.5 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Rentman for Craft”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require furbo/rentman-for-craft

# tell Craft to install the plugin
./craft plugin/install rentman-for-craft
```


## Code Examples

{% set product = craft.rentman.getProductById(2550) %}
{{product.displayname}}

<hr />

{% for product in craft.rentman.getProductsByCategory(3207) %}
    {{product.displayname}}
{% endfor %}

<hr />

{% for product in craft.rentman.getAllProducts() %}
    {{product.displayname}}
{% endfor %}
