<?php
$theme = $this->customlib->getCurrentTheme();

if ($this->customlib->getRTL() != "") {
    if ($theme == "white") {
        ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/bootstrap-rtl/css/bootstrap-rtl.min.css"/>
        <!-- Theme RTL style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/white-rtl.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/AdminLTE-rtl.min.css" />

        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/skins/_all-skins-rtl.min.css" />

        <?php
} else {
        ?>
        <!-- Bootstrap 3.3.5 RTL -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/bootstrap-rtl/css/bootstrap-rtl.min.css"/>
        <!-- Theme RTL style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/AdminLTE-rtl.min.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/ss-rtlmain.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/skins/_all-skins-rtl.min.css" />
        <?php
}
}

if ($theme == "white") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/ss-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/main.css">
    <!-- Modern Header Styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <!-- Sidebar Treeview Lines -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <!-- White Theme Dashboard & Calendar Styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/dashboard.css">
    <!-- Shared admin page styles (boxes, tables, forms) -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/admin-pages.css">
    <!-- Remix Icon (for modern sidebar icons) -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">

    <?php
} elseif ($theme == "default") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/default/skins/_all-skins.min.css">
    <!-- Modern Header Styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <!-- Theme Color Overrides - Must load AFTER header-modern.css -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/default/ss-main.css">
    <!-- Sidebar Treeview Lines -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <!-- Remix Icon (for modern sidebar icons) -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">

    <?php
} elseif ($theme == "red") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/red/skins/skin-red.css">
    <!-- Modern Header Styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <!-- Theme Color Overrides - Must load AFTER header-modern.css -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/red/ss-main-red.css">
    <!-- Sidebar Treeview Lines -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <!-- Remix Icon (for modern sidebar icons) -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
} elseif ($theme == "blue") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/blue/skins/skin-darkblue.css">
    <!-- Modern Header Styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <!-- Theme Color Overrides - Must load AFTER header-modern.css -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/blue/ss-main-darkblue.css">
    <!-- Sidebar Treeview Lines -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <!-- Remix Icon (for modern sidebar icons) -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
} elseif ($theme == "gray") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/gray/skins/skin-light.css">
    <!-- Modern Header Styles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <!-- Theme Color Overrides - Must load AFTER header-modern.css -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/gray/ss-main-light.css">
    <!-- Sidebar Treeview Lines -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <!-- Remix Icon (for modern sidebar icons) -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
}