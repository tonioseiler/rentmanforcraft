<img src="resources/img/plugin-logo.png" width="100" height="100">

<h1 align="left">Rentman for Craft</h1>
<p>Automatically import <a href="https://rentman.io/" target="_blank">Rentman</a> products to <a href="https://craftcms.com/" target="_blank">Craft</a>. It lets visitors create orders. Orders are automatically sent to Rentman as project requests.</p>


![Screenshot](resources/img/rentman-craft-backend-snapshot-v3.jpg)
![Screenshot](resources/img/rentman-craft-product-detail-v3.jpg)
![Screenshot](resources/img/rentman-craft-products-v3.jpg)
![Screenshot](resources/img/rentman-craft-project-v3.jpg)



## Requirements

This plugin requires Craft CMS 4.2.0 or later, and PHP 8.0.2 or later.

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

## Craft Setup Example

1. Go to Settings > Plugins > Rentman for Craft > Main settings
2. Add the API URL, usually `https://api.rentman.net/`
3. Add the API Key, more infos here: https://support.rentman.io/hc/en-us/articles/360013767839-The-Rentman-API
4. Add a cron job that executes the php script `httpdocs/craft rentman-for-craft/rentman/update-all` (adapt the path to your installation) for example once a day
5. Choose the templates and urls for products, categories and projects
5. Click on the **Customisation** tab: here you can choose your own templates and settings for generated emails and pdfs


## Code Examples

### Get a single product
```
{% set product = craft.rentman.getProductById(2550) %}
{{product.displayname}}
```

<hr />

### Get products by category
```
<ul>
{% for product in craft.rentman.getProductsByCategory(3207) %}
    <li><a href="{{product.getUrl()}}">{{product.displayname}}</a></li>
{% endfor %}
</ul>
```

<hr />

### Get all products
```
<ul>
{% for product in craft.rentman.getAllProducts() %}
    <li><a href="{{product.getUrl()}}">{{product.displayname}}</a></li>
{% endfor %}
</ul>
```

<hr />

### Get main categories
```
<ul>
{% for mainCategory in craft.rentman.getCategories() %}
    <li><a href="{{mainCategory.getUrl()}}">{{mainCategory.displayname}}</a></li>
{% endfor %}
</ul>
```

<hr />

### Get categories first two levels
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


### Get all categories in an array (recursive)

```
{% set categories = craft.rentman.getCategoriesRecursive(0) %}  
```
<hr />

### Print full categories tree
```
{{craft.rentman.printCategoryTree(true)|raw}}
```
<hr />

### Print full categories tree with active category
```
{{craft.rentman.printCategoryTree(true, 3162)|raw}}
```
<hr />

### Print partial categories tree with active category
```
{{craft.rentman.printCategoryTree(false, 3193)|raw}}
```

### Product page
```
{% extends "/layouts/app" %}
{% set product = craft.rentman.getProductById(product.id) %}
{% block title %}{{ product.displayname }}{% endblock %}

{% block content %}
    {% include '/layouts/_partials/_category-tree.twig'  with { categoryId: product.categoryId } %}
    <h1>{{ product.displayname }}</h1>
    <div>Code: {{ product.code }}</div>
    <div>Price day: CHF {{ product.price }}.-</div>
    {% if product.weight %}
        <div>Weight: {{ product.weight }}kg</div>
    {% endif %}
    <br>
    <div class="description">
        {{ product.shop_description_short|raw }}
    </div>
    
    {% set files = product.files|json_decode %}
    {% if files|length > 0 %}
        <ul class="files">
            {% for file in files %}
                {% if file.in_webshop %}
                    <li><a href="{{ file.url }}" target="_blank">{{ file.displayname }}</a></li>
                {% endif %}
            {% endfor %}
        </ul>
    {% endif %}
    
    <div class="image">
        {% set images = product.images|json_decode %}
        {% for image in images %}
            <img src="{{ image.url }}" alt="{{ product.displayname }}">
        {% endfor %}
    </div>
    
    <div class="accessories">
        <h2>Set content</h2>
        <div class="accessories__content">
            {% for setContentProduct in setContentProducts %}
                {% set product = craft.rentman.getProductById(setContentProduct.productId) %}
                {% if product %}
                    <div class="accessories__single">
                        {% if product.in_shop %}
                            <a href="/{{ product.uri }}">
                                {% set images = product.images|json_decode %}
                                {% if images|length > 0 %}
                                    {{ images[0].url }}
                                    {{ product.title }}
                                {% else %}
                                    {{ product.title }}
                                {% endif %}
                            </a>
                        {% else %}
                            {% set images = product.images|json_decode %}
                            {% if images|length > 0 %}
                                {{ images[0].url }}
                                {{ product.title }}
                            {% else %}
                                 {{ product.title }}
                            {% endif %}
                        {% endif %}
                    </div>
                {% endif %}
            {% endfor %}
        </div>
    </div>
    
    {% set atLeastOneAccessory = false %}
        {% set accessories = craft.rentman.getProductAccesories(product.id) %}
        {% if accessories|length > 0 %}
            {% for acc in accessories %}
                {% if not atLeastOneAccessory %}
                    {% set accProduct = craft.rentman.getProductById(acc.productId) %}
                    {% if accProduct %}
                        {% set atLeastOneAccessory = true %}
                    {% endif %}
                {% endif %}
            {% endfor %}
        {% endif %}
        {% if atLeastOneAccessory %}
            <div class="accessories">
                <h2>Accessories</h2>
                <div class="accessories__content">
                    {% for acc in accessories %}
                        {% set accProduct = craft.rentman.getProductById(acc.productId) %}
                        {% if accProduct %}
                            <div class="accessories__single">
                                {% if accProduct.in_shop %}
                                    <a href="/{{ accProduct.uri }}">
                                        {% set images = accProduct.images|json_decode %}
                                        {% if images|length > 0 %}
                                            {{ images[0].url }}
                                            {{ accProduct.title }}
                                        {% else %}
                                            {{ accProduct.title }}
                                        {% endif %}
                                    </a>
                                {% else %}
                                  {% set images = accProduct.images|json_decode %}
                                    {% if images|length > 0 %}
                                        {{ images[0].url }}
                                        {{ accProduct.title }}
                                    {% else %}
                                        {{ accProduct.title }}
                                    {% endif %}
                                {% endif %}
                            </div>
                        {% endif %}
                    {% endfor %}
                </div>
            </div>
        {% endif %}
    ...
{% endblock %}
```

### Category page
```
{% extends "/layouts/app" %}
{% if not category is defined %}
    {% set category = '' %}
    {% for mainCategory in craft.rentman.getCategories()|slice(0,1) %}
        {% set category = mainCategory %}
    {% endfor %}
{% endif %}
{% block title %}{{ category.displayname }}{% endblock %}
{% block content %}

    {% include '/layouts/_partials/_category-tree.twig'  with { categoryId: category.id } %}
    
    {# list products that are directly in this category #}
    <div class="products-list">
        {% if craft.rentman.getProductsByCategory(category.id) %}
            <div class="header listitem grid">
                <div>Title</div>
                <div class="weight">kg</div>
                <div class="price">CHF</div>
                <div></div>
            </div>
            {% for product in craft.rentman.getProductsByCategory(category.id) %}
                <div>
                    <div><a href="/{{ product.uri }}">{{ product.displayname }}</a></div>
                    <div class="weight">{{ product.weight ? product.weight : '-' }}</div>
                    <div class="price">{{ product.price|number_format(2, '.', '’') }}</div>
                    <div class="chooser">
                        <input type="text" pattern="[0-9]+" maxlength="2" value="{{ craft.rentman.getProjectProductQuantity(product.id) }}" class="product-quantity" data-product-id="{{ product.id }}">
                        <span class="icon-minus product-quantity-minus"></span>
                        <span class="icon-plus product-quantity-plus"></span>
                    </div>
                </div>
            {% endfor %}
        {% endif %}
    </div>
    
    {# list subcategories and related products #}
    {% set subcats=craft.rentman.getCategoriesRecursive(category.id) %}
    {% for subcat in subcats %}
        {% set products = craft.rentman.getProductsByCategory(subcat['id']) %}
        {% if products %}
            <h2 class="cat-name"><a href="/{{ subcat.uri }}">{{ subcat.displayname }}</a></h2>
            <div class="header listitem grid">
                <div>Title</div>
                <div class="weight">kg</div>
                <div class="price">CHF</div>
                <div></div>
            </div>
            {% for product in products %}
                <div class="listitem grid">
                    <div><a href="/{{ product.uri }}">{{ product.displayname }}</a></div>
                    <div class="weight">{{ product.weight ? product.weight : '-' }}</div>
                    <div class="price">{{ product.price|number_format(2, '.', '’') }}</div>
                    <div class="chooser">
                        <input type="text" pattern="[0-9]+" maxlength="2" value="{{ craft.rentman.getProjectProductQuantity(product.id) }}" class="product-quantity" data-product-id="{{ product.id }}">
                        <span class="icon-minus product-quantity-minus"></span>
                        <span class="icon-plus product-quantity-plus"></span>
                    </div>
                </div>
            {% endfor %}
        {% endif %}
    {% endfor %}
    
{% endblock %}
```

### Project page
```
{% block content %}

    {% set isActiveProject = false %}
    {% set activeProject = craft.rentman.getActiveProject() %}
    {% if project is not defined %}
        {% set project = activeProject %}
        {% set isActiveProject = true %}
    {% else %}
        {% set activeProject = craft.rentman.getActiveProject() %}
        {% if activeProject %}
            {% if project.id == activeProject.id %}
                {% set isActiveProject = true %}
            {% endif %}
        {% endif %}
    {% endif %}

    {% if project is not null %}
    
        {% if project.canView() %}

            {% include '/layouts/_partials/_category-tree.twig'  with { categoryId: 0 } %}
        
            <h1">{{ project.title }}</h1>
            {% if project.getItems|length %}
                <div id="project-submit-button-container">
                    {% set missingRequiredFields = false %}
                    {% if isActiveProject %}
                        <button class="mt-5" type="button" onclick="window.location.href='/cart';">cart</button>
                        <button class="mt-5" type="button" onclick="window.location.href='/project-edit';">Edit Project</button>
                        {% if project.contact_person_first_name=='' or project.contact_person_lastname=='' %}
                            {% set missingRequiredFields = true %}
                        {% endif %}
                        <form id="project-submit" method="post" accept-charset="UTF-8" enctype="multipart/form-data" class="inline">
                            {{ csrfInput() }}
                            {{ actionInput('/rentman-for-craft/api/submit-project') }}
                            {{ hiddenInput('projectId', project.id) }}
                            {{ redirectInput('/tnxs') }}
                            <button class="mt-5" type="submit" {{ missingRequiredFields ?  'disabled' }}>Send request</button>
                        </form>
                        
                        {% if missingRequiredFields %}
                            <h2>NOTE</h2>
                            <a href="/project-edit">Edit the project</a> to be able to submit it
                        {% endif %}
                    {% else %}
                        {% if project.status == 'draft' %}
                            <button class="project-setactive mt-5" type="button" data-project-id="{{ project.id }}">Activate project</button>
                        {% endif %}
                        <button class="project-copy mt-5" type="button" data-project-id="{{ project.id }}">Projekt kopieren</button>
                    {% endif %}
                </div>
                <br>
                <div><strong>Shooting days::</strong> {{ project.shooting_days }}</div><br>
                <div>
                    <table>
                        <thead>
                        <tr class="border-b-2 border-b-darkgrey">
                            <th scope="col">Title</th>
                            <th scope="col">kg</th>
                            <th scope="col">CHF</th>
                            <th scope="col">
                                <span class="hidden sm:inline">Quantity </span><span class="inline sm:hidden">Qty</span>
                            </th>
                            <th scope="col">Total ({{ project.shooting_days > 1 ? project.shooting_days~' Days' : '1 Day' }})</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% set productsGroupedByCats = project.getItemsGroupedByCategory %}
                        {% for categoryId, products in productsGroupedByCats %}
                            {% set category = craft.rentman.getCategoryById(categoryId) %}
                            <tr>
                                <th scope="row">
                                    {{ category.displayname }}:
                                </th>
                            </tr>
                            {% for product in products %}
                                {% set productMainData = craft.rentman.getProductById(product.productId) %}
                                <tr>
                                    <th scope="row" ">
                                        <a href="/{{ productMainData.uri }}">{{ productMainData.displayname }}</a>
                                    </th>
                                    <td>
                                        {{ productMainData.weight }}
                                    </td>
                                    <td>
                                        {{ productMainData.price|number_format(2, '.', '’') }}
                                    </td>
                                    <td>
                                        {{ product.quantity }}
                                    </td>
                                    <td>
                                        {{ product.price|number_format(2, '.', '’') }}
                                    </td>
                                </tr>
                            {% endfor %}
                        {% endfor %}
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    {{ project.price|number_format(2, '.', '’') }}
                                </td>
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
                <div class="customer-main-data">
                    <div>
                        <div>
                            {{ project.contact_person_first_name }} {{ project.contact_person_lastname }}<br>
                            {{ project.contact_mailing_street }}<br>
                            {{ project.contact_mailing_postalcode }}  {{ project.contact_mailing_city }}
                            {{ project.contact_mailing_country }}
                        </div>
                        <div>
                            {{ project.contact_person_email }}<br>
                            {{ project.contact_mailing_number }}<br>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="customer-production-data">
                    <div>Produktion</div>
                    <div>
                        {{ project.location_name }}
                        {{ project.location_mailing_street }}
                        {{ project.location_mailing_postalcode }}
                        {{ project.location_mailing_city }}
                        {{ project.location_mailing_country }}
                        {{ project.location_mailing_number }}
                    </div>
                </div>
                <hr>
                <div class="project-dates">
                    <div>Abholdatum:<br>{{ project.in|date }}</div>
                    <div>Rückgabedatum:<br>{{ project.out|date }}</div>
                    <div>Drehbeginn:<br>{{ project.planperiod_start|date }}</div>
                    <div>Drehende:<br>{{ project.planperiod_end|date }}</div>
                </div>
               
                {% if project.remark %}
                    <div>Contact name, telephoon, email and notes:</strong><br>{{ project.remark|nl2br }}</div>
                {% endif %}
        
                <div id="project-submit-button-container">
                    {% set missingRequiredFields = false %}
                    {% if isActiveProject %}
                        <button type="button" onclick="window.location.href='/cart';">Cart</button>
                        <button type="button" onclick="window.location.href='/project-edit';">Edit project</button>
                        {% if project.contact_person_first_name=='' or project.contact_person_lastname=='' %}
                            {% set missingRequiredFields = true %}
                        {% endif %}
                        <form id="project-submit" method="post" accept-charset="UTF-8" enctype="multipart/form-data" class="inline">
                            {{ csrfInput() }}
                            {{ actionInput('/rentman-for-craft/api/submit-project') }}
                            {{ hiddenInput('projectId', project.id) }}
                            {{ redirectInput('/tnxs') }}
                            <button type="submit" {{ missingRequiredFields ?  'disabled' }}>Send request</button>
                        </form>
                        {% if missingRequiredFields %}
                            <h2 class="mt-[2rem]">NOTE</h2>
                            <a href="/project-edit">Edit the project</a> to be able to submit it
                        {% endif %}
                    {% else %}
                        {% if project.status == 'draft' %}
                            <button type="button" data-project-id="{{ project.id }}"Activate project</button>
                        {% endif %}
                        <button type="button" data-project-id="{{ project.id }}">Duplicate project</button>
                    {% endif %}
                </div>
            {% else %}
                <div>Cart is empty</div>
                {% if not isActiveProject %}
                    {% if project.status == 'draft' %}
                        <button  type="button" data-project-id="{{ project.id }}">Activate project</button>
                    {% endif %}
                {% endif %}
            {% endif %}
         
         {% else %}
            {# trying to access someone else's project #}
            {% redirect "/" %}
        {% endif %}
        
    {% else %}
        {# project not defined #}
        {% redirect "/" %}
    {% endif %}
    
{% endblock %}
```

## Translations

You can copy the `rentman-for-craft.php` translations files from the plugin's translations folder to your site's translations folder and translate them there.


## Support

If you have any issues with this plugin, please [create an issue](https://github.com/tonioseiler/rentmanforcraft/issues) on GitHub or contact us at [Furbo](mailto:support@furbo.ch).
