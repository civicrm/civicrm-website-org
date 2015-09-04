/*
Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
for licensing, see license.html or http://ckeditor.com/license
*/

/*
 * this file is used/requested by the 'styles' button.
 * the 'styles' button is not enabled by default in drupalfull and drupalfiltered toolbars.
 */
if(typeof(ckeditor) !== 'undefined') {
    ckeditor.addstylesset( 'drupal',
    [

            { name : 'Heading 2'		, element : 'h2' },
            { name : 'Heading 3'		, element : 'h3' },
            { name : 'Paragraph'		, element : 'p' },
            { name : 'Quote',			, element : 'p', attributes: { 'class': 'quote'} },
            { name : 'Code',			, element : 'pre' },

    ]);
}
