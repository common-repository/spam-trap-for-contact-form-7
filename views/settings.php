<?php

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $Nonce = sanitize_text_field($_POST['_wpnonce']);
    if (!wp_verify_nonce($Nonce, 'cf7as_settings_nonce') ) {
        die('Failed security check');
    }

    if (! current_user_can('administrator') ) {
        die("sorry, only administrators can edit these settings");
    }
        
    $Value = 0;
    if (isset($_POST["spam_trap_for_contact_form_7_max_link_allowed"]) ) {
        $Value = intVal($_POST["spam_trap_for_contact_form_7_max_link_allowed"]);
    }
    update_option("spam_trap_for_contact_form_7_max_link_allowed", $Value);

            
    $Value = "";
    if ((isset($_POST["spam_trap_for_contact_form_7_send_to_dnsbl"]))
        && (sanitize_text_field($_POST["spam_trap_for_contact_form_7_send_to_dnsbl"]) == "on")  
    ) {
        $Value = "on";
    }
    update_option("spam_trap_for_contact_form_7_send_to_dnsbl", $Value);
    

    $Value = "";
    if ((isset($_POST["spam_trap_for_contact_form_7_mail_allowed"]))
        && (sanitize_text_field($_POST["spam_trap_for_contact_form_7_mail_allowed"]) == "on")
    ) {
        $Value = "on";
    }
    update_option("spam_trap_for_contact_form_7_mail_allowed", $Value);
    
    $Value = "";
    if ((isset($_POST["spam_trap_for_contact_form_7_mail_blocked"]))
        && (sanitize_text_field($_POST["spam_trap_for_contact_form_7_mail_blocked"]) == "on")
    ) {
        $Value = "on";
    }
    update_option("spam_trap_for_contact_form_7_mail_blocked", $Value);   


    $Value = "";
    if (isset($_POST["spam_trap_for_contact_form_7_mail_to"])) {
        $Value = sanitize_email($_POST["spam_trap_for_contact_form_7_mail_to"]);
    }
    update_option("spam_trap_for_contact_form_7_mail_to", $Value);   



    $Value = "";
    if (isset($_POST["spam_trap_for_contact_form_7_blocked_words"])) {
        $Value = sanitize_textarea_field($_POST["spam_trap_for_contact_form_7_blocked_words"]);
    }
    update_option("spam_trap_for_contact_form_7_blocked_words", $Value);   



    $Value = "";
    if (isset($_POST["spam_trap_for_contact_form_7_blocked_names"])) {
        $Value = sanitize_textarea_field($_POST["spam_trap_for_contact_form_7_blocked_names"]);
    }
    update_option("spam_trap_for_contact_form_7_blocked_names", $Value);   



    $Value = "";
    if (isset($_POST["spam_trap_for_contact_form_7_blocked_email_addresses"])) {
        $Value = sanitize_textarea_field($_POST["spam_trap_for_contact_form_7_blocked_email_addresses"]);
    }
    update_option("spam_trap_for_contact_form_7_blocked_email_addresses", $Value);   



    $Value = "your-message";
    if (isset($_POST["spam_trap_for_contact_form_7_message_field"])) {
        $Value = sanitize_text_field($_POST["spam_trap_for_contact_form_7_message_field"]);
    }
    update_option("spam_trap_for_contact_form_7_message_field", $Value);
    
    
    
    

    $Value = "your-email";
    if (isset($_POST["spam_trap_for_contact_form_7_email_field"])) {
        $Value = sanitize_text_field($_POST["spam_trap_for_contact_form_7_email_field"]);
    }
    update_option("spam_trap_for_contact_form_7_email_field", $Value); 
    
        
    

    $Value = "your-name";
    if (isset($_POST["spam_trap_for_contact_form_7_name_field"])) {
        $Value = sanitize_text_field($_POST["spam_trap_for_contact_form_7_name_field"]);
    }
    update_option("spam_trap_for_contact_form_7_name_field", $Value); 
    

}
?>
     
    <div class="wrap">
    <h2><?php echo esc_html($this->plugin->displayName); ?> &raquo; <?php esc_html('Catch Spammers!'); ?></h2>
        
    <?php    
    if (isset($this->message)) {
        ?>
        <div class="updated fade"><p><?php echo esc_html($this->message); ?></p></div>  
        <?php
    }

    if (isset($this->errorMessage)) {
        ?>
        <div class="error fade"><p><?php echo esc_html($this->errorMessage); ?></p></div>  
        <?php
    }
    ?> 
    
    <div id="poststuff">
         <div id="post-body" class="metabox-holder columns-2">
            <!-- Content -->
            <div id="post-body-content">
                <div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
                    <div class="postbox">
                        <h3 class="hndle">Catch Spammers!</h3>
                         
                        <div class="inside">
            
                            <?php 
                            
                            $MAX_LINK_ALLOWED = intVal(get_option("spam_trap_for_contact_form_7_max_link_allowed", 0));
                            if ($MAX_LINK_ALLOWED < 0) {
                                $MAX_LINK_ALLOWED = 0;
                            }

                
                            $sendToDNSBL = sanitize_text_field(get_option("spam_trap_for_contact_form_7_send_to_dnsbl", ""));
                            

                            $MailTo = sanitize_email(get_option("spam_trap_for_contact_form_7_mail_to", ""));
                            $MailAllowed = sanitize_text_field(get_option("spam_trap_for_contact_form_7_mail_allowed", ""));
                            $MailBlocked = sanitize_text_field(get_option("spam_trap_for_contact_form_7_mail_blocked", ""));
                            $MessageField = sanitize_text_field(get_option("spam_trap_for_contact_form_7_message_field", "your-message"));
                            
                            $emailField = sanitize_text_field(get_option("spam_trap_for_contact_form_7_email_field", "your-message"));
                            $nameField = sanitize_text_field(get_option("spam_trap_for_contact_form_7_name_field", "your-message"));
                            
                        
                        
                            $blockWords = sanitize_textarea_field(get_option("spam_trap_for_contact_form_7_blocked_words", "sex\r\ntits\r\npussy\r\nfuck"));
                            $blockNames = sanitize_textarea_field(get_option("spam_trap_for_contact_form_7_blocked_names", "DavidFat\r\nJosephanync"));
                            $blockEmailAddresses = sanitize_textarea_field(get_option("spam_trap_for_contact_form_7_blocked_email_addresses", ""));
                            
        
                            print "<form method=\"post\" action=\"\">";
                    
                            wp_nonce_field('cf7as_settings_nonce');
                    
                            print "Max allowed links in message<br>";
                            print "<input type=\"number\" value=\"".esc_attr($MAX_LINK_ALLOWED)."\" name=\"spam_trap_for_contact_form_7_max_link_allowed\"><p>";
                            
                            print "Message Field (field name in cf7 to filter)<br>";
                            print "<input type=\"text\" value=\"".esc_attr($MessageField)."\" name=\"spam_trap_for_contact_form_7_message_field\"><p>";

                            
                            
                            print "Email Field (field name in cf7 to filter)<br>";
                            print "<input type=\"text\" value=\"".esc_attr($emailField)."\" name=\"spam_trap_for_contact_form_7_email_field\"><p>";

                            
                            print "Name Field (field name in cf7 to filter)<br>";
                            print "<input type=\"text\" value=\"".esc_attr($nameField)."\" name=\"spam_trap_for_contact_form_7_name_field\"><p>";


                            print "<hr style=\"margin-top: 2em; margin-bottom: 2em;\">";

                            
                            print "Block Words (one per line)<br>";
                            print "<textarea name=\"spam_trap_for_contact_form_7_blocked_words\">".esc_textarea($blockWords)."</textarea><p>";

                            
                            
                            print "Block Names (one per line)<br>";
                            print "<textarea name=\"spam_trap_for_contact_form_7_blocked_names\">".esc_textarea($blockNames)."</textarea><p>";

                            
                            
                            print "Block Email Addresses (one per line)<br>";
                            print "<textarea name=\"spam_trap_for_contact_form_7_blocked_names\">".esc_textarea($blockEmailAddresses)."</textarea><p>";

                            
                            
                            print "<hr style=\"margin-top: 2em; margin-bottom: 2em;\">";

                            print "Email address to send reports to (if enabled below)<br>";
                            print "<input type=\"text\" value=\"".esc_attr($MailTo)."\" name=\"spam_trap_for_contact_form_7_mail_to\"><p>";
                            
                            
                            print "<label for=\"spam_trap_for_contact_form_7_mail_allowed\" style=\"margin-right:1em;\">Send email when form allowed (useful for testing / debugging)</label>";
                            print "<input type=\"checkbox\"".(($MailAllowed == "on")? " checked" :" ")." name=\"spam_trap_for_contact_form_7_mail_allowed\" id=\"spam_trap_for_contact_form_7_mail_allowed\"><p>";
                            
                        
                            print "<label for=\"spam_trap_for_contact_form_7_mail_blocked\" style=\"margin-right:1em;\">Send email when form blocked</label>";
                            print "<input type=\"checkbox\" ".(($MailBlocked == "on")? " checked" :" ")." name=\"spam_trap_for_contact_form_7_mail_blocked\" id=\"spam_trap_for_contact_form_7_mail_blocked\"><p>";
                            

                            print "<hr style=\"margin-top: 2em; margin-bottom: 2em;\">";


                            print "<label for=\"spam_trap_for_contact_form_7_send_to_dnsbl\" style=\"margin-right:1em;\">Send blocked messages to DNSBL</label>";
                            print "<input type=\"checkbox\" ".(($sendToDNSBL == "on")? " checked" :" ")." id=\"spam_trap_for_contact_form_7_send_to_dnsbl\" name=\"spam_trap_for_contact_form_7_send_to_dnsbl\"><p>";
                            ?>
                            <p><strong>NOTE: </strong>this will send the entire message, the sender's email address, sender's name and sender's IP address to dnsbl.softsmart.co.za. This will be manually reviewed and if it is found to be spam the info will be added to the online block list. This block list will in turn be used by this plugin to detect spammy IP addresses / names / email addresses</p>

                            <?php

                            print "<p>";
                            submit_button();
                            print "</form>";
                                    
                            ?>
                            <p>&nbsp;<p>


                        </div>
                    </div>
                    <!-- /postbox -->
     
                </div>
                <!-- /normal-sortables -->
            </div>
            <!-- /post-body-content -->


        <!-- /postbox-container -->
         </div>
     </div>      
</div>
