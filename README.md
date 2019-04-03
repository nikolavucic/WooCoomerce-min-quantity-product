# WooCoomerce minimum quantity product

Adding possibility to set minimum quantity of WooCommerce product. 

This functionality works only for SIMPLE products.  
There is no real need to work on GROUPED products, because grouped product has few simple products in group, and on every single product you can set minimum quantity.  
There is no real need to work on EXTERNAL/AFFILIATE products, because this product have a link to another website/product.  
This functionality will NOT works for VARIABLE products, because when variable product get it's variables, those variables are actualy new product with new price, new SKU ...

### Prerequisites

Tested to:  WordPress v5.1.1
            WooCommerce v3.5.7
            php 7.2

Local development environment used: VVV Varying Vagrant Vagrants v2.3.0

Theme used: Twentynineteen and it's child is downloaded from web too.

TextDomain used: "twentynineteen-child-min-quantity"

Function prefix used: "wentynineteen_child" ( "wc" is standard WooCommerce prefix )

### Installing

This code snippet is located in functions.php file of Child-theme  
( It can be located in functions.php file of WordPress theme - not recommended )  
( It can be used to make a new WordPress plugin )  
