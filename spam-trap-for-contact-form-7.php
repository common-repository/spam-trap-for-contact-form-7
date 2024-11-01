<?php
/**
 * Plugin Name: Spam Trap for Contact Form 7 
 * Plugin URI: http://www.softsmart.co.za/
 * Version: 1.1.1
 * Author: John McMurray (john@softsmart.co.za)
 * Author URI: http://www.softsmart.co.za/
 * Description: This plugin adds spam checks to catch contact form 7 spam
 * License: GPL2
 */

/*  Copyright 2016 SoftSmart.co.za

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * SPAM TRAP CONTACT FORM 7
 *
 * @category  ANTI SPAM
 * @package   SPAM TRAP CONTACT FORM 7
 * @author    John Mc Murray <john@softsmart.co.za>
 * @copyright 2017 www.softsmart.co.za
 * @link      http://www.softsmart.co.za
 */

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

class SpamTrapForContactForm7
{

    /**
     * Constructor
     *
     * @return null
     */
    public function __construct() 
    {
        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name = 'spam_trap_for_contact_form_7'; // Plugin Folder
        $this->plugin->displayName = 'Spam Trap for Contact Form 7'; // Plugin Name
        $this->plugin->version = '1.1.1';
        $this->plugin->folder = plugin_dir_path(__FILE__);
        $this->plugin->url = plugin_dir_url(__FILE__);
        
        
        // Hooks
        //add_action('admin_init', array(&$this, 'registerSettings')); // Will add register and deregister functions in the next revision
        add_action('admin_menu', array(&$this, 'adminPanelsAndMetaBoxes'));
        
        //add_action( 'wpcf7_before_send_mail', array(&$this, 'check_contact_form_7_spam'));
        add_filter('wpcf7_skip_mail', array(&$this, 'check_contact_form_7_spam'), 10, 2);

        add_filter('wpcf7_form_hidden_fields', array(&$this, 'filter_wpcf7_form_hidden_fields'));
        add_action('admin_init', array(&$this, 'child_plugin_has_parent_plugin'));
    }
     


    function child_plugin_has_parent_plugin() 
    {
        if (is_admin() && current_user_can('activate_plugins') 
            && !is_plugin_active('contact-form-7/wp-contact-form-7.php')
        ) {
            add_action('admin_notices', array($this, 'child_plugin_notice'));

            deactivate_plugins(plugin_basename(__FILE__)); 

            if (isset($_GET['activate']) ) {
                unset($_GET['activate']);
            }
        }
    }

    function child_plugin_notice()
    {
        ?>
        <div class="error"><p>Sorry, this plugin requires the contact form 7 to be installed and active.</p></div>
        <?php
    }


    /**
     * Creates admin panel for setting emails address for notices and spam threshold
     *
     * @return null
     */
    function adminPanelsAndMetaBoxes() 
    {
        add_submenu_page('options-general.php', $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array(&$this, 'adminPanel'));
    }
    

    /**
     * Output the Administration Panel
     * Save POSTed data from the Administration Panel into a WordPress option
     *
     * @return null 
     */
    function adminPanel() 
    {
        // Load Settings Form
        include_once plugin_dir_path(__FILE__).'/views/settings.php';
    }


    /**
     * Gets the number of links in a user's comment by regex and returns that number. Can be "text" links, eg, http://www.example.com or can be part of an anchor tag
     *
     * @param string $Comment 
     *
     * @return int
     */
    function GetLinkCount($Comment)
    {
        preg_match_all('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $Comment, $result);
        return count($result[0]);
    }

   
     
    /**
     * This function checks the comment for spam. It requires that the comment field is called "your-message" (future versions will make that configurable).
     * It checks the comment against the user defined link count threshold, but also against a run time inserted field in the form called cf7asgotcha.
     * If this gotcha field is missing it implies that the form was submitted in some way other than from our website.
     * If the gotcha field is present AND it has been filled in then it implies that a bot scraped and auto filled our from
     *
     * @param object $contact_form 
     *
     * @return int
     */
    function check_contact_form_7_spam($skip_mail, $contact_form)
    { 
        $MAX_LINK_ALLOWED = intVal(get_option("spam_trap_for_contact_form_7_max_link_allowed", 0));
        $MailTo = sanitize_email(get_option("spam_trap_for_contact_form_7_mail_to", ""));
        $MailAllowed = sanitize_text_field(get_option("spam_trap_for_contact_form_7_mail_allowed", ""));
        $MailBlocked = sanitize_text_field(get_option("spam_trap_for_contact_form_7_mail_blocked", ""));
        $MessageField = sanitize_text_field(get_option("spam_trap_for_contact_form_7_message_field", "your-message"));
        $nameField = sanitize_text_field(get_option("spam_trap_for_contact_form_7_name_field", "your-name"));
        $emailField = sanitize_text_field(get_option("spam_trap_for_contact_form_7_email_field", "your-email"));
        $sendToDNSBL = sanitize_text_field(get_option("spam_trap_for_contact_form_7_send_to_dnsbl", ""));

        $submission = WPCF7_Submission::get_instance();
        $data = $submission->get_posted_data();

        $form = $contact_form->get_properties();
        

        if (! isset($data[$MessageField])) {
            // No message field set, warn user then return

            if ($MailTo != "" ) {
                $headers[] = 'From: '.$MailTo;

                wp_mail($MailTo, "Spam Trap For Contact Form 7", "Hello,\r\n\r\nThe Softsmart.co.za 'spam trap for contact form 7' plugin is configured to filter on a message field called ".$MessageField." but we could not find this field in your comment form.\r\n\r\nPlease check the name of the message field in CF7 and then make sure that Spam Trap For Contact Form 7 is configured to use that message field name.\r\n\r\nWe found: \r\n\r\n".print_r($data, true)."\r\n\r\n", $headers);
            }

            return;
        }

        $ipAddress = "unknown";
        if (filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP)) {
            $ipAddress = filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP);
        }

        $SenderAddress = sanitize_email($form["mail"]["sender"]);
        if (strlen($SenderAddress) > 3) {
            $x = strpos($SenderAddress, "]");

            if ($x !== false) {
                $SenderAddress = trim(substr($SenderAddress,  $x + 1));
            }
        }

        $headers[] = 'From: '.$SenderAddress;
        
        //$Message = filter_var($data[$MessageField], FILTER_UNSAFE_RAW);
        $Message = $data[$MessageField]; // Don't filter this because it removes spammy tags which our regex then can't count in the following call...
        $SpamLinkCount = $this->GetLinkCount($Message);  
        $Message = sanitize_textarea_field($Message); // we can filter now that we've counted spammy links.

        $yourName = "";

        if (isset($data[$nameField])) {
            $yourName = $data[$nameField]; // Don't filter this because it removes spammy tags which our regex then can't count in the following call...
        }
        
        if ($yourName != "") {
                
            // use the greater spam count between the name (keyword/url stuffing) or the actual message.
            $x = $this->GetLinkCount($yourName);

            if ($x > $SpamLinkCount) {
                $SpamLinkCount = $x;
            }

            $yourName = sanitize_text_field($yourName); // we can filter now that we've counted spammy links.
        }



        $yourEmail = "";

        if (isset($data[$emailField])) {
            $yourEmail = sanitize_email($data[$emailField]);
        }

        $CommentSpam = 0;
        $Reason = "";
    
        if ((isset($data["cf7asgotcha"])) && ($data["cf7asgotcha"] != "") ) {
            $CommentSpam = 1;
            $Reason = $Reason."cf7asgotcha is not blank\n";
        }
        
        if (! isset($data["cf7asgotcha"])) {
            $CommentSpam = 1;
            $Reason = $Reason."cf7asgotcha is not set\n";
        }  
          
          
        $blockWords = explode("\r\n", sanitize_textarea_field(get_option("spam_trap_for_contact_form_7_blocked_words", "sex\r\ntits\r\npussy\r\nfuck")));   
           
        
        if (strlen(trim($Message)) == 0 ) {
            $CommentSpam = 1;
            $Reason = $Reason."Empty comment body\n";
        }
            
        if (!empty($blockWords)) {
        
            
            foreach ($blockWords as $blockWord) {
            
                if (strlen(trim($blockWord)) == 0) {
                    continue;
                }
                
                $pattern = '/^[\s\S]*[ ]*'.$blockWord.'(?=[.,]|$| )/';
                $pattern = '/(^| )'.$blockWord.'(?=[.,]|$| )/';
                
                //if (strstr($Message, " ".$blockWord." ") ) {
                if (preg_match($pattern, $Message) ) {
                            
                    $CommentSpam = 1;
                    $Reason = $Reason."Triggered by block word: '".$blockWord."'\r\n";
                
                }
            }
                
        }
        
       
        $blockYourName = explode("\r\n", sanitize_textarea_field(get_option("spam_trap_for_contact_form_7_blocked_your_name", "DavidFat\r\nJosephanync")));
            
                    
        if (!empty($blockYourName)) {
        
        
            foreach ($blockYourName as $block) {
            
                if (strlen(trim($block)) == 0) {
                    continue;
                }
                
                $pattern = '/^[\s\S]*[ ]*'.$block.'(?=[.,]|$| )/';
                $pattern = '/(^| )'.$block.'(?=[.,]|$| )/';
                
                //if (strstr($Message, " ".$blockWord." ") ) {
                if (preg_match($pattern, $yourName)) {
                            
                    $CommentSpam = 1;
                    $Reason = $Reason."Triggered by block name: '".$block."'\r\n";
                
                }
            }
                
        }
       
       
        
        if ($SpamLinkCount > $MAX_LINK_ALLOWED) {
            $CommentSpam = 1;
            $Reason = $Reason."More than ".$MAX_LINK_ALLOWED." links. There are ".$SpamLinkCount."\n";
        } else {
            // set this in case they have debug option enabled in which case we send info even if we don't block
            $Reason = $Reason."Max Link Allowed: ".$MAX_LINK_ALLOWED."; there are ".$SpamLinkCount."\r\n";
        }


        $dnsblIpArray = explode(".", $ipAddress);
        $dnsblIp = implode(".", array_reverse($dnsblIpArray)).".dnsbl.softsmart.co.za";
        $ipInBlockList = gethostbyname($dnsblIp);

        if (substr($ipInBlockList, 0, 8) == "127.0.0.") {
            $CommentSpam = 1;
            $Reason = $Reason."IP Blocked in dnsbl.softsmart.co.za\n";            
        }


        // $dnsblName = $yourName.".dnsbl.softsmart.co.za";
        // $nameInBlockList = gethostbyname($dnsblName)
        // if ( substr($nameInBlockList, 0, 8) == "127.0.0.") {
        //     $blocked = true;
            
        // }



        // $dnsblEmail = $yourEmail.".dnsbl.softsmart.co.za";
        // $emailInBlockList = gethostbyname($dnsblEmail);
        // if ( substr($emailInBlockList, 0, 8) == "127.0.0.") {
        //     $blocked = true;
            
        // }



        if ($CommentSpam == 1) {
            // Set skip_mail to true... This effectively "kills" the form submission
            $skip_mail = true;
        
            // If  the user has requested to be notified of comments by mail then handle that below - useful for a new user testing effectiveness
            if (($MailBlocked == "on") && ($MailTo != "") ) {
                wp_mail($MailTo, "Contact Form 7 Spam Blocked", "Hello,\r\nThe Softsmart.co.za 'spam trap for contact form 7' plugin blocked the following mail:\r\n\r\nIP: ".$ipAddress."\r\nReasons: ".$Reason."\r\n".print_r($data, true), $headers);
            }

            if ($sendToDNSBL) {
                $response = wp_remote_post(
                    "https://dnsbl.softsmart.co.za/api/v1/CommentSpam/add", array(
                    'body'        => array(
                    "name"         => $yourName,
                    "comment"    => $Message,
                    "email"        => $yourEmail,
                    "ip"        => $ipAddress
                    ),
                    )
                );
            }

            
        } else {

            if (($MailAllowed == "on") && ($MailTo != "")) {
                wp_mail($MailTo, "Contact Form 7 Allowed", "Hello,\r\nThe Softsmart.co.za 'spam trap for contact form 7' plugin allowed the following mail:\r\n\r\nIP: ".$ipAddress."\r\nReasons: ".$Reason."\r\n".print_r($data, true), $headers);
            }

        }

        return $skip_mail;
    }
         
    /**
     * Add a hidden field to our contact form 7 form
     *
     * @param array $array 
     *
     * @return int
     */         
    function filter_wpcf7_form_hidden_fields( $array ) 
    { 
        // make filter magic happen here by adding a hidden form field
        $array["cf7asgotcha"] = ''; 
        return $array; 
    }                       
     
}
  
$spamTrapForContactForm7 = new SpamTrapForContactForm7();
?>
