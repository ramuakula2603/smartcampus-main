<ul class="sessionul fixedmenu">
    <?php
    if ($this->rbac->hasPrivilege('quick_session_change', 'can_view')) {
    ?>
        <li class="removehover">
            <a data-toggle="modal" data-target="#sessionModal"><span><?php echo $this->lang->line('current_session') . ": " . $this->setting_model->getCurrentSessionName(); ?></span><i class="ri-pencil-ai-fill"></i></a>

        </li>
    <?php } ?>

    <li class="quick-links-trigger-li">
        <a href="#" id="quickLinksTrigger" data-toggle="modal" data-target="#quickLinksModal">
            <span><?php echo $this->lang->line('quick_links'); ?></span> <i class="ri-dashboard-fill"></i>
        </a>
    </li>
</ul>
<script>
    (function($) {
        if (typeof $ === 'undefined') {
            return;
        }

        // Ensure clicks inside vertical menus do not propagate and close parents
        $('.verticalmenu').on('click', function(event) {
            event.stopPropagation();
        });

        // Initialize custom scrollbar only when plugin is available
        if ($.fn.mCustomScrollbar) {
            $(".mCustomScrollbar").mCustomScrollbar({
                scrollInertia: 1000,
                mouseWheelPixels: 170,
                autoDraggerLength: false,
            });
        }
    })(window.jQuery);
</script>