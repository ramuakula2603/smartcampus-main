<?php
// Get logo - use logo_url from controller if available
$logo_path = isset($logo_url) && !empty($logo_url) ? $logo_url : '';

$school_name = isset($sch_setting) && !empty($sch_setting->name) ? $sch_setting->name : 'AMARAVATHI JUNIOR COLLEGE';
$school_address = isset($sch_setting) && !empty($sch_setting->address) ? $sch_setting->address : '';
?>

<table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
    <!-- Header -->
    <tr>
        <td width="20%" style="text-align: left; vertical-align: middle;">
            <?php if (!empty($logo_path)): ?>
                <img src="<?php echo $logo_path; ?>" style="max-width: 100px; max-height: 100px;" />
            <?php endif; ?>
        </td>
        <td width="60%" style="text-align: center; vertical-align: middle;">
            <div style="font-size: 20pt; font-weight: bold; margin-bottom: 5px;"><?php echo htmlspecialchars($school_name); ?></div>
            <div style="font-size: 18pt; font-weight: bold; margin-top: 5px;">QUESTION PAPER</div>
            <?php if (!empty($school_address)): ?>
                <div style="font-size: 10pt; margin-top: 5px;"><?php echo htmlspecialchars($school_address); ?></div>
            <?php endif; ?>
        </td>
        <td width="20%"></td>
    </tr>
    <tr>
        <td colspan="3" style="border-top: 2px solid #000; padding-top: 10px; margin-top: 10px;"></td>
    </tr>
    
    <!-- Content -->
    <tr>
        <td colspan="3" style="padding: 15px 10px; text-align: left;">
            <?php 
            if (isset($messages) && is_array($messages) && !empty($messages)): 
                foreach ($messages as $message): 
                    if (!empty($message) && trim($message) !== ''): 
                        // Simply convert newlines to <br> and preserve the text
                        $text = htmlspecialchars($message);
                        $text = nl2br($text);
                        echo $text;
                    endif;
                endforeach;
            endif; 
            ?>
        </td>
    </tr>
    
    <!-- Footer -->
    <tr>
        <td colspan="3" style="text-align: center; padding-top: 20px; border-top: 1px solid #ddd; margin-top: 20px;">
            <div style="font-size: 9pt; color: #666;">Generated on <?php echo date('d/m/Y h:i A'); ?></div>
        </td>
    </tr>
</table>
