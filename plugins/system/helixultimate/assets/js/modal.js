/* ========================================================================
* Bootstrap: modal.js v3.1.1
* http://getbootstrap.com/javascript/#modals
* ========================================================================
* Copyright 2011-2014 Twitter, Inc.
* Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
* ======================================================================== */
jQuery(function($) {
  $.fn.helixUltimateModal = function(options) {
    var options = $.extend({
      target : ''
    }, options);

    $('.helix-ultimate-modal-overlay, .helix-ultimate-modal').remove();

    var mediaModal = '<div class="helix-ultimate-modal-overlay"></div>';
    mediaModal += '<div class="helix-ultimate-modal" data-target="#'+ options.target +'">';

    mediaModal += '<div class="helix-ultimate-modal-header">';
    mediaModal += '<a href="#" class="action-helix-ultimate-modal-close"><span class="fa fa-times"></span></a>'
    mediaModal += '<input type="file" id="helix-ultimate-file-input" accept="image/png, image/jpg, image/jpeg, image/gif, image/svg+xml" style="display:none;" multiple>';
    mediaModal += '<div class="helix-ultimate-modal-breadcrumbs"></div>';

    mediaModal += '<div class="helix-ultimate-modal-actions-left">'
    mediaModal += '<a href="#" class="btn btn-success btn-xs helix-ultimate-modal-action-select"><span class="fa fa-check"></span> Select</a>'
    mediaModal += '<a href="#" class="btn btn-secondary btn-xs helix-ultimate-modal-action-cancel"><span class="fa fa-times"></span> Cancel</a>'
    mediaModal += '<a href="#" class="btn btn-danger btn-xs btn-last helix-ultimate-modal-action-delete"><span class="fa fa-minus-circle"></span> Delete</a>'
    mediaModal += '</div>';

    mediaModal += '<div class="helix-ultimate-modal-actions-right">'
    mediaModal += '<a href="#" class="btn btn-success btn-xs helix-ultimate-modal-action-upload"><span class="fa fa-upload"></span> Upload</a>'
    mediaModal += '<a href="#" class="btn btn-primary btn-xs btn-last helix-ultimate-modal-action-new-folder"><span class="fa fa-plus"></span> New Folder</a>'
    mediaModal += '</div>';
    mediaModal += '</div>';

    mediaModal += '<div class="helix-ultimate-modal-inner">';
    mediaModal += '<div class="helix-ultimate-modal-preloader"><span class="fa fa-spinner fa-pulse fa-spin fa-3x fa-fw"></span></div>';
    mediaModal += '</div>';
    mediaModal += '</div>';

    $('body').addClass('helix-ultimate-modal-open').append(mediaModal);
  }

  $.fn.helixUltimateOptionsModal = function(options) {
    var options = $.extend({
      target : '',
      title: 'Options',
      flag: '',
      class: ''
    }, options);

    $('.helix-ultimate-modal-overlay, .helix-ultimate-modal').remove();

    var mediaModal = '<div class="helix-ultimate-modal-overlay"></div>';
    mediaModal += '<div class="helix-ultimate-modal '+ options.class +'" data-target="#'+ options.target +'">';

    mediaModal += '<div class="helix-ultimate-modal-header">';
    mediaModal += '<span class="helix-ultimate-modal-header-title">' + options.title + '</span>';
    mediaModal += '<a href="#" class="action-helix-ultimate-modal-close"><span class="fa fa-times"></span></a>'
    mediaModal += '</div>';

    mediaModal += '<div class="helix-ultimate-modal-inner">';
    mediaModal += '<div class="helix-ultimate-modal-content">';
    mediaModal += '</div>';
    mediaModal += '</div>';

    mediaModal += '<div class="helix-ultimate-modal-footer">';
    mediaModal += '<a href="#" class="btn btn-success btn-xs helix-ultimate-settings-apply" data-flag="'+ options.flag +'"><span class="fa fa-check"></span> Apply</a>'
    mediaModal += '<a href="#" class="btn btn-secondary btn-xs helix-ultimate-settings-cancel"><span class="fa fa-times"></span> Cancel</a>'
    mediaModal += '</div>';

    mediaModal += '</div>';

    $('body').addClass('helix-ultimate-modal-open').append(mediaModal);
  }
});
