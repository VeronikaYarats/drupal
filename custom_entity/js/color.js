/**
 * @file
 * Contains the definition of the behaviour jsColorizeArticle.
 */

(function ($, Drupal, drupalSettings) {

    'use strict';

    /**
     * Attaches the JS test behavior article
     */
    Drupal.behaviors.jsColorizeArticle = {
        attach: function (context, settings) {
            var colors = drupalSettings.articles.colors;

            jQuery.each(colors, function( index, color ) {
                $( ".item-list").eq(0).find("ul").eq(0).find(".item-list").eq(index).css('background-color', color);
            })
        }
    };
})(jQuery, Drupal, drupalSettings);
