<?php
if (!empty($CFG->themedir) and file_exists("$CFG->themedir/decaf")) {
    require_once ($CFG->themedir."/decaf/lib.php");
} else {
    require_once ($CFG->dirroot."/theme/decaf/lib.php");
}

// $PAGE->blocks->region_has_content('region_name') doesn't work as we do some sneaky stuff 
// to hide nav and/or settings blocks if requested
$blocks_side_pre = trim($OUTPUT->blocks_for_region('side-pre'));
$hassidepre = strlen($blocks_side_pre);
$blocks_side_post = trim($OUTPUT->blocks_for_region('side-post'));
$hassidepost = strlen($blocks_side_post);

if (empty($PAGE->layout_options['noawesomebar'])) {
    $topsettings = $this->page->get_renderer('theme_decaf','topsettings');
    decaf_initialise_awesomebar($PAGE);
    $awesome_nav = $topsettings->navigation_tree($this->page->navigation);
    $awesome_settings = $topsettings->settings_tree($this->page->settingsnav);
}

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();

if(!empty($PAGE->theme->settings->useeditbuttons) && $PAGE->user_allowed_editing()) {
    decaf_initialise_editbuttons($PAGE);
    $bodyclasses[] = 'decaf_with_edit_buttons';
}

if ($hassidepre && !$hassidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($hassidepost && !$hassidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$hassidepost && !$hassidepre) {
    $bodyclasses[] = 'content-only';
}

if(!empty($PAGE->theme->settings->persistentedit) && $PAGE->user_allowed_editing()) {
    if(property_exists($USER, 'editing') && $USER->editing) {
        $OUTPUT->set_really_editing(true);
    }
    $USER->editing = 1;
    $bodyclasses[] = 'decaf_persistent_edit';
}

if (!empty($PAGE->theme->settings->footnote)) {
    $footnote = $PAGE->theme->settings->footnote;
} else {
    $footnote = '<!-- There was no custom footnote set -->';
}

if (check_browser_version("MSIE", "0")) {
    header('X-UA-Compatible: IE=edge');
}
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <meta name="description" content="<?php echo strip_tags(format_text($SITE->summary, FORMAT_HTML)) ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html();
if (empty($PAGE->layout_options['noawesomebar'])) {  ?>
    <div id="awesomebar" class="decaf-awesome-bar">
        <?php
                echo $awesome_nav;
                if ($hascustommenu && !empty($PAGE->theme->settings->custommenuinawesomebar) && empty($PAGE->theme->settings->custommenuafterawesomebar)) {
                    echo $custommenu;
                }
                echo $awesome_settings;
                if ($hascustommenu && !empty($PAGE->theme->settings->custommenuinawesomebar) && !empty($PAGE->theme->settings->custommenuafterawesomebar)) {
                    echo $custommenu;
                }
                echo $topsettings->settings_search_box();
        ?>
    </div>
<?php } ?>

<div id="page">

<!-- START OF HEADER -->
    <div id="page-header" class="clearfix">
		<div id="page-header-wrapper">
	        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
    	    <div class="headermenu">
        		<?php
        		    if (!empty($PAGE->theme->settings->showuserpicture)) {
        				if (isloggedin())
        				{
        					echo ''.$OUTPUT->user_picture($USER, array('size'=>55)).'';
        				}
        				else {
        					?>
						<img class="userpicture" src="<?php echo $OUTPUT->pix_url('image', 'theme'); ?>" />
						<?php
        				}
        			}
//echo $OUTPUT->login_info();
//  	        echo $OUTPUT->lang_menu();
	        	echo $PAGE->headingmenu;
		        ?>	    
	    	</div>
	    </div>
    </div>
    
    <?php if ($hascustommenu && empty($PAGE->theme->settings->custommenuinawesomebar)) { ?>
      <div id="custommenu" class="decaf-awesome-bar"><?php echo $custommenu; ?></div>
 	<?php } ?>
  	  
<!-- END OF HEADER -->

<!-- START OF CONTENT -->

<div id="page-content-wrapper" class="clearfix">
    <div id="page-content">
    
		<div id="centerCol" class="<?php if (!$hassidepost&&!$hassidepre) { echo 'fullWidth'; } ?>">
	        <div class="region-content">
				<?php echo method_exists($OUTPUT, "main_content")?$OUTPUT->main_content():core_renderer::MAIN_CONTENT_TOKEN ?>
	        </div>
		</div>
		
		<?php if ( $hassidepre || $hassidepost ) { ?>
    	<div id="side-post" class="block-region">
    		<div class="region-content">
    			 <?php
    			 	echo $blocks_side_pre;
					echo $blocks_side_post;
				?>
    		</div>
    	</div>
    	<?php } ?>
    	
    </div>
</div>

<!-- END OF CONTENT -->

<!-- START OF FOOTER -->
 
<?php /*
    <div id="page-footer">
		<div class="footnote"><?php echo $footnote; ?></div>
        <p class="helplink">
        <?php echo page_doc_link(get_string('moodledocslink')) ?>
        </p>

        <?php
			  //echo $OUTPUT->login_info();
			  //echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </div> */ ?>

<!-- END OF FOOTER -->

</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
<div id="back-to-top"> 
    <a class="arrow" href="#">▲</a> 
    <a class="text" href="#">Back to Top</a> 
</div>
<script type="text/javascript">
YUI().use('node', function(Y) {
    window.thisisy = Y;
	Y.one(window).on('scroll', function(e) {
	    var node = Y.one('#back-to-top');

	    if (Y.one('window').get('docScrollY') > Y.one('#page-content-wrapper').getY()) {
		    node.setStyle('display', 'block');
	    } else {
		    node.setStyle('display', 'none');
	    }
	});

});
</script>
</body>
</html>
