# TODOs

## Backend menu
The current selected submenu does not get the class "sel", and this means it always seems that we are on the "Products" listing.
To solve, this may help: https://craftcms.stackexchange.com/questions/28831/setting-plugin-subnav-to-active


## getSubCategoriesJson(parentCategory)
Paolo: I would like this method, and it should return a json containing the subcategories id.
Using this I can implement this: https://blowup-rental.ch/vermietung/flags/butterflies/reflectors-etc
Which is: I choose a cat that has subcats, on the right side I show, for eachsub cat:
- the subcat title (with link to the subcat)
- the list of products in that subcat
Note: best would be if also this method is recursive, to deal with many nested subcats (not needed for this project, but 
nice feature for the plugin).
In general, I think that exposing the cats and prods also with json methods could be a good idea, especially for customising the
html that will be printed instead of having chunks of html in the methods (ul, li, "active" etc)
