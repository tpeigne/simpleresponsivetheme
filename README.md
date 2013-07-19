Simple Responsive Theme
=======================

Simple Responsive Theme for PrestaShop 1.5.x is a theme based on the most advanced responsive front-end framework : [Foundation][1] from ZURB.

The theme is based on 1 column, it's a clear theme with simple colors for an easy and quickly customization.

Fonctionnalities
----------------

* Full HTML5 & CSS3 theme
* Successful W3C validation
* Browser and Device Support (Chrome, Firefox, Opera, Safari, IE7+, iOS, Android 2+, Windows Phone 7)
* 7 responsive modules fully customizable
* Beautiful top and main bar menu with dropdown for tablet and mobile
* Responsive slider, home featured and main nav menu with an administration
* Social product share with ShareThis (Facebook, Twitter...)
* Customizable theme with an administration to includes plugins for Foundation (responsive tabs, Reveal, Clearing, Joyride...). More plugins can be found on Foundation website.
* Completely customizable
* Applicable for multi-store and multi-languages

Installation
------------

Before the installation of the theme, you have to remove front office modules from their hooks if you want to have the theme displayed correctly.

Then, you can put the simplresponsivetheme folder in your themes folder and activate all the responsive modules.

When the installation of the theme is over, you have to disable the mobile theme in order to see the responsive design on mobile and tablets.

Note for other module installation :

To have the same theme as the demo theme [here][2], you have to install other native PrestaShop modules.

BlockNewsletter :
To display the blocknewsletter in the simpleresponsivetheme, you have to install the blocknewsletter from PrestaShop. First you have to remove the module from the left column hook (displayLeftColumn), and then hook the module on the footer (displayFooter).

Other PrestaShop modules :
Other modules are compatible with the simpleresponsivetheme. The productcomments module is compatible and you only have to install it in the administration panel.

Custom image type creation :

You have to create a custom image type in order to display correctly the product image on the product page. Create a custom image type called "product_resp" with 450px for height and 450px for width and available for the product page.

Recommendations
---------------

It's important to remove PrestaShop front office modules from their hooks before the installation of the simpleresponsivetheme. Otherwise, the responsivetheme won't be displayed correctly.

Demonstration
-------------

A demo of the theme is available online here : [demo.thomaspeigne.com][2]

[1]: http://foundation.zurb.com/
[2]: http://demo.thomaspeigne.com/
