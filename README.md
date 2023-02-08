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

## Get a single product
```
{% set product = craft.rentman.getProductById(2550) %}
{{product.displayname}}
```

<hr />

## Get products by category
```
<ul>
{% for product in craft.rentman.getProductsByCategory(3207) %}
    <li><a href="{{product.getUrl()}}">{{product.displayname}}</a></li>
{% endfor %}
</ul>
```

<hr />

## Get all products
```
<ul>
{% for product in craft.rentman.getAllProducts() %}
    <li><a href="{{product.getUrl()}}">{{product.displayname}}</a></li>
{% endfor %}
</ul>
```

<hr />

## Get main categories
```
<ul>
{% for mainCategory in craft.rentman.getCategories() %}
    <li><a href="{{mainCategory.getUrl()}}">{{mainCategory.displayname}}</a></li>
{% endfor %}
</ul>
```

<hr />

## Get categories first two levels
```
<ul>
{% for mainCategory in craft.rentman.getCategories() %}
    <li><a href="{{mainCategory.getUrl()}}">{{mainCategory.displayname}}</a></li>
    {% if mainCategory.hasChildren() %}
        <ul>
        {% for child in mainCategory.getChildren().all() %}
            <li><a href="{{child.getUrl()}}">{{child.displayname}}</a></li>
        {% endfor %}
        </ul>
    {% endif %}
{% endfor %}
</ul>
```

<hr />

## Print full categories tree
```
{{craft.rentman.printCategoryTree(true)|raw}}
```
<hr />

## Print full categories tree with active category
```
{{craft.rentman.printCategoryTree(true, 3162)|raw}}
```
<hr />

## Print partial categories tree with active category
```
{{craft.rentman.printCategoryTree(false, 3193)|raw}}
```