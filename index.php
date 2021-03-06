<?php
session_start();
 $err_msg = '';

if ( isset($_REQUEST['css_type']) && $_REQUEST['css_type'] === '1' ) {
	$_FORM_TYPE = 1;// Vertical
} else {
	$_FORM_TYPE = 0;// Horizontal
}

// The fact we're not using the default visualCaptcha's fieldname is just to show part of visualCaptcha's flexibility
require_once( dirname(__FILE__).'/includes/recaptchalib.php' );
//if ( isset($_REQUEST['form_submit']) && $_REQUEST['form_submit'] === '1' ) {
// if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' ) {
if ( isset( $_POST['submit-bt'] ) && $_POST['submit-bt'] == 'Shorten' ) {
	$url_val = trim($_POST['url']);
	  if(!empty($url_val) ) {
	  	//validating the 
		if (!preg_match("/\b(https:\/\/|http:\/\/|www|)?[\.]?+[a-z0-9A-Z]+\.[com|co.in|.net]+[a-z0-9A-Z\/?]+/i", $url_val)) {			
			$err_msg.= "Invalid URL<br>";
		 }
		 //recaptcha code
		 // $privatekey = CAPTCHA_PVT_KEY;
		 if(!empty($_POST['recaptcha_response_field'])){
		 $privatekey ="6LfQBPISAAAAAP5N53TlNuTk-VrVrNwLA7UjpQAK";
		 $resp = recaptcha_check_answer ($privatekey,
		                                 $_SERVER["REMOTE_ADDR"],
		                                 $_POST["recaptcha_challenge_field"],
		                                 $_POST["recaptcha_response_field"]);
		 if (!$resp->is_valid) {
		   // What happens when the CAPTCHA was entered incorrectly	   
		 	$err_msg.= "CAPTCHA entered incorrectly, Try again<br>";
		  }
		}else{
			$err_msg.= "Please enter CAPTCHA";
		}
		  
	    }
		 else{
	 		$err_msg.= "Please enter URL";
	 	} 
	
}

//session_start();
/*
 * This is an example file for a public interface and a bookmarklet. It
 * is provided so you can build from it and customize to suit your needs.
 * It's not really part of the project. Don't submit feature requests 
 * about this file. It's _your_ job to make it what you need it to be :)
 *
 * Rename to .php
 *
 */

// Start YOURLS engine
require_once( dirname(__FILE__).'/includes/load-yourls.php' );

// Change this to match the URL of your public interface. Something like: http://yoursite.com/index.php
$page = YOURLS_SITE . '/index.php';
//$page = YOURLS_SITE . '/sample-public-front-page.php' ;

// Part to be executed if FORM has been submitted
//if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' ) {
// if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' && $err_msg=='') {
if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' && empty($err_msg)) {
	// Get parameters -- they will all be sanitized in yourls_add_new_link()
	$url     = $_REQUEST['url'];
	$keyword = isset( $_REQUEST['keyword'] ) ? $_REQUEST['keyword'] : '' ;
	$title   = isset( $_REQUEST['title'] ) ?  $_REQUEST['title'] : '' ;
	$text    = isset( $_REQUEST['text'] ) ?  $_REQUEST['text'] : '' ;

	// Create short URL, receive array $return with various information
	$return  = yourls_add_new_link( $url, $keyword, $title );
	
	$shorturl = isset( $return['shorturl'] ) ? $return['shorturl'] : '';
	$message  = isset( $return['message'] ) ? $return['message'] : '';
	$title    = isset( $return['title'] ) ? $return['title'] : '';
	$status   = isset( $return['status'] ) ? $return['status'] : '';
	
	// Stop here if bookmarklet with a JSON callback function ("instant" bookmarklets)
	if( isset( $_GET['jsonp'] ) && $_GET['jsonp'] == 'yourls' ) {
		$short = $return['shorturl'] ? $return['shorturl'] : '';
		$message = "Short URL (Ctrl+C to copy)";
		header('Content-type: application/json');
		echo yourls_apply_filter( 'bookmarklet_jsonp', "yourls_callback({'short_url':'$short','message':'$message'});" );
		
		die();
	}
}

// Insert <head> markup and all CSS & JS files
yourls_html_head();

// Display title
//echo "<h1>YOURLS - Your Own URL Shortener</h1>\n";

// Display left hand menu
//yourls_html_menu() ;

?>
<?php yourls_html_footer();?>
<div class="contentarea homecontent">
	<div class="ltpannel homeinner center">		
		<div class="gap"></div>		
			<p class="urlrow"><a href="<?php yourls_site_url(); ?>" title="lyc.so"><img src="images/lycsoLogo.png" alt="lyc.so" title="lyc.so" /></a></p>
			<?php
			//if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' ) {
			// if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' && $err_msg=='') {
			if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' && empty($err_msg)) {
				// Display result message of short link creation
				($status == 'success')?$class = 'successtext':$class = 'warning';
				if( isset( $message ) && $class=='success') {					
					echo "<div class='successtext lurl'>Long URL <span>$message</span></div><br>";
				}
				if( isset( $message ) && $class=='warning') {					
					echo "<div class='successtext lurl'>Long URL <span>$url</span></div>";
				}
				if( isset($shorturl) && $shorturl!=''){
					echo "<div class='successtext lurl'>Shorten URL <span>$shorturl</span></div><br>";
				}				
				
				if( $status == 'success' ) {
					// Include the Copy box and the Quick Share box
					yourls_share_box( $url, $shorturl, $title, $text );
					
					// Initialize clipboard -- requires js/share.js and js/jquery.zclip.min.js to be properly loaded in the <head>
					echo "<script>init_clipboard();</script>\n";
				}
				echo "<span class='clear'></span><div><a href='/'><input type='submit' class='btn' value='Create More'></a></div>";

			}else{
			?>
			<?php
			if ( ! empty($err_msg) ) {
			?>
				<div class="warning">
					<span>
						<?php 
							echo $err_msg;							
						?>
					</span>
				</div>
			<?php
			}
			?>
			<script type="text/javascript">
			 var RecaptchaOptions = {
			    theme : 'blackglass'
			 };		 
			
			$('#submit-bt').live('click', function() {
				var uval = $('#url').val();
				uval = $.trim(uval);
				$("#url").val(uval); 
			});
			</script>			
			<form name="frm_sample" id="frm_sample" method="post" action="">				
				<div class="margin20_T">
					<div class="urlrow">
					<table class="shorten-table">
						<td width="11%" align="right" valign="top"><label class="strong">Paste URL :</label></td>
						<td width="80%" valign="top">
							<input type="text" name="url" id="url" value="<?php echo $_REQUEST['url'];?>" class="fullwidth"/>
							<div class="margin20_T">													
								<?php
							     $publickey = CAPTCHA_PUB_KEY;
							     echo recaptcha_get_html($publickey);
							    ?>
							</div>
						</td>
						<td width="10%" align="left" valign="top">
						<input type="submit" name="submit-bt" id="submit-bt" class="btn" value="Shorten"/>
						</td>
					</table>					
				   </div>
				</div>			
			</form>				
			<?php } ?>	

	</div>	
</div>
<!--contentarea end-->

<div class="add">
	<!-- new code -->
	<script type="text/javascript">
		if (!window.OX_ads)
		{ OX_ads = []; }
		OX_ads.push(
		{ "auid" : "537094873" }
		);
	</script>
	<script type="text/javascript">
		document.write('<scr'+'ipt src="http://ox-d.lycos.com/w/1.0/jstag"><\/scr'+'ipt>');
	</script>
	<noscript><iframe id="14f66a6be9" name="14f66a6be9" src="http://ox-d.lycos.com/w/1.0/afr?auid=537094873&cb=INSERT_RANDOM_NUMBER_HERE"><a href="http://ox-d.lycos.com/w/1.0/rc?cs=14f66a6be9&cb=INSERT_RANDOM_NUMBER_HERE" ><img src="http://ox-d.lycos.com/w/1.0/ai?auid=537094873&cs=14f66a6be9&cb=INSERT_RANDOM_NUMBER_HERE" border="0" alt="Add Banner" class="banner"></a></iframe></noscript>
	<!-- end -->
</div>

<!--Display page footer -->
	


