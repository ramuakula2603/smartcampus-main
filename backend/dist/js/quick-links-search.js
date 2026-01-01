(function($) {
  'use strict';

  function filterQuickLinks() {
    var $input     = $('#quickLinksSearchInput');
    var $grid      = $('.card-columns-sidebar');
    var $cards     = $grid.find('.card-sidebar');
    var $clear     = $('#quickLinksClear');
    var $noResults = $('#quickLinksNoResults');

    if (!$input.length || !$grid.length || !$cards.length || !$clear.length || !$noResults.length) {
      return;
    }

    var rawQuery = $input.val() || '';
    var query    = rawQuery.toLowerCase();
    var hasQuery = rawQuery !== '';
    var anyMatch = false;

    $cards.each(function() {
      var $card = $(this);
      var cardMatch = false;

      // Search in section title and all links (case-insensitive)
      $card.find('h4, ul li a').each(function() {
        var text = ($(this).text() || '').trim().toLowerCase();
        if (!hasQuery || text.indexOf(query) !== -1) {
          cardMatch = true;
        }
      });

      if (hasQuery && !cardMatch) {
        $card.hide();
      } else {
        $card.show();
      }

      if (cardMatch) {
        anyMatch = true;
      }
    });

    // Toggle clear (X) visibility
    if (hasQuery) {
      $clear.show();
    } else {
      $clear.hide();
    }

    // Show / hide "no results" message
    if (hasQuery && !anyMatch) {
      $noResults.show();
      $noResults.find('.ql-no-results-text').text('"' + rawQuery + '"');
    } else {
      $noResults.hide();
    }
  }

  $(document).ready(function() {
    var $modal = $('#quickLinksModal');
    var $input = $('#quickLinksSearchInput');

    // Rely on Bootstrap data-toggle="modal" on #quickLinksTrigger to open the modal.
    // When modal is shown, focus the search input and reset filter
    if ($modal.length && $.fn && $.fn.modal) {
      $modal.on('shown.bs.modal', function() {
        if ($input.length) {
          $input.trigger('focus');
        }
        filterQuickLinks();
      });
    }

    // Initial render (in case modal content is visible server-side)
    filterQuickLinks();

    // Delegate events so it works even if elements are re-rendered
    $(document).on('input keyup', '#quickLinksSearchInput', function() {
      filterQuickLinks();
    });

    // Clear search (and keep modal open)
    $(document).on('click', '#quickLinksClear', function(e) {
      e.preventDefault();
      $('#quickLinksSearchInput').val('');
      filterQuickLinks();
    });

    // Close modal on Esc while focused in search input
    $(document).on('keydown', '#quickLinksSearchInput', function(e) {
      if (e.key === 'Escape' || e.keyCode === 27) {
        if ($modal.length && $.fn.modal) {
          $modal.modal('hide');
        }
      }
    });
  });

})(jQuery);
