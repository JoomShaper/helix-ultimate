/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

jQuery(function($){
    "use strict";

    //Import Template Settings
    $( '#import-settings' ).on( 'click', function( event ) {
        event.preventDefault();

        console.log('hello world');

        var $that = $( this ),
            temp_settings = $.trim( $that.prev().val() );

        if ( temp_settings == '' ) {
          return false;
        }

        if ( confirm( "Warning: It will change all current settings of this Template." ) != true ){
            return false;
        }

        var data = {
            settings : temp_settings
          };

        var request = {
                'action' : 'import-tmpl-style',
                'option' : 'com_ajax',
                'plugin' : 'helixultimate',
                'request': 'helixultimate',
                'data'   : data,
                'format' : 'json'
            };

        $.ajax({
            type   : 'POST',
            data   : request,
            success: function (response) {
                var data = $.parseJSON(response);
                if ( data.status ){
                    window.location.reload();
                }
            },
            error: function(){
                alert('Somethings wrong, Try again');
            }
        });
        return false;
  });
});
