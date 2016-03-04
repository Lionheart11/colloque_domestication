<?php  
/* 
Template Name: Cr3ativ-Conference

Page retravaillée pour WCS.

*/  
/*
//================= 
//tests modal        
//==================  
// le script css dans le <head>
add_action('wp_head', 'wcs_css_modal');

function wcs_css_modal() {
    //print('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">');
    print(
        '<style>
        .modal-transparent {
          background: transparent;
        }
        .modal-transparent .modal-content {
          background: transparent;
        }
        .modal-backdrop.modal-backdrop-transparent {
          background: #ffffff;
        }
        .modal-backdrop.modal-backdrop-transparent.in {
          opacity: .9;
          filter: alpha(opacity=90);
        }
        </style>'
        );
}

// le script js dans le footer
add_action('wp_footer', 'wcs_js_modal');

function wcs_js_modal() {
    wp_enqueue_script( 'wcs_js_modal', get_template_directory_uri() . '/js/wcs_js_modal.js', array("jquery") );
}
//================= 
//fin tests modal        
//==================  
*/
?>

<?php get_header(); ?>

<!-- Start of content wrapper -->
<div> <!--id="cr3ativconference_contentwrapper"> -->
    <div class="wcs-conf"> <!--class="conf-wrapper">-->

        <h1><?php _e("[:fr]Programme[:en]Schedule[:]");?></h1>
<?php
        // Récupère toutes les "communications"

        add_filter('posts_orderby','cr3ativoderby2');
        $wp_query = new WP_Query(array(
                        'post_type'         => 'cr3ativconference',
                        'posts_per_page'    => -1,
                        'order'             => 'ASC',
                        'meta_key'          => 'cr3ativconf_date',
                        'meta_query'        => array(
                                                    array(
                                                    'key' => 'cr3ativconf_date',
                                                    ),
                                                    array(
                                                    'key' => 'cr3ativconf_starttime',
                                                    ),
                                                ),
                            )); 
        remove_filter('posts_orderby','cr3ativoderby2');
          
        $sessiondate = '';
        $is_same_date = true;
        $is_same_session = true;
        $day_name = '';
        $is_break = false;
        $has_desc = true;

        $cssclass = "";


        while (have_posts()) : the_post();
            $post_id = $post->ID;

            $meetingdate    = get_post_meta($post->ID, 'cr3ativconf_date', $single = true); 
            $starttime      = get_post_meta($post->ID, 'cr3ativconf_starttime', $single = true);
            $endtime        = get_post_meta($post->ID, 'cr3ativconf_endtime', $single = true); 

            $is_same_date = ($sessiondate == $meetingdate);
            $sessiondate = $meetingdate;

            // pour chaque communication, récupère les catégories (sessions, journées, pauses)
            $terms = wp_get_post_terms($post->ID, 'cr3ativconfcategory', array('fields'=>'all'));
            foreach($terms as $term) {

                // s'il s'agit d'une journée, d'une pause ou d'une communication sans description
                // (ex "introduction"), on enregistre l'état
                if (!$term->parent) {

                    $has_desc = ($term->slug != "no-desc");
                    $is_break = ($term->slug == "break");
                    if ($has_desc && !$is_break) {
                        $day_name = $term->name;
                        $day_desc = $term->description;
                        $day_slug = $term->slug;

                        $cssclass = empty($cssclass)?"wcs-altern":"";
                    }
                }
                // sinon, il s'agit d'une véritable "communication"
                else {
                    $is_same_session = ($session_name == $term->name);
                    $session_name = $term->name;
                    $session_desc = $term->description;
                }
            }

        ?>

        <div> <!-- style="background-color:rgb(194,194,194);margin:0;"> -->
     
            <?php if (!$is_same_date) { ?>

                <div class="wcs-day">
               
                    <!-- TITRE JOURNEE style="color:#cc9345" -->
                    <h4><?php echo $day_name; ?></h4>
         
                    <!-- ADDRESSE -->
                    <h6><?php _e( $day_desc ); ?></h6>

               </div>
               
            <?php 
                }
                
                // SESSIONS
                if (!$is_same_session){ 
                    print("
                        <div class='wcs-session'>
                            <h5>$session_name</h5>
                            <h6>".__( $session_desc )."</h6>
                        </div>
                        ");
                } 
            ?>

            <!-- COMMUNICATION -->
            <div class="wcs-comm">
                <h6>

                    <?php 
                        // HEURE COMMUNICATION
                        print("<span class='wcs-hours'>");
                        if ($starttime != ('')) {  
                            print($starttime); 
                        }
                    
                        if ($endtime != ('')) { 
                            print(" - $endtime"); 
                        }
                        print("</span>");

                        // TITRE COMMUNICATION
                        print("<span class='wcs-comm-title'>");

                        if ($has_desc && !$is_break) {
                    ?> 
                    <a href="<?php the_permalink() ?>" title="<?php _e( '[:fr]Afficher [:en]Display [:i]', 'squarecode' ); ?>&nbsp; <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                    <?php
                        }
                        else {
                            the_title();
                        }
                        print("</span>");
                    ?>                
                    
                </h6>
            </div>

            <?php  ?>

            


            <!-- INTERVENANTS -->
            <div class="wcs-pers">
            <?php


            $cr3ativ_confspeakers = get_post_meta($post->ID, 'cr3ativconf_persons', $single = true); 
	        if ( is_array($cr3ativ_confspeakers) ) { 
				
	        	foreach ( $cr3ativ_confspeakers as $cr3ativ_confspeaker ) {

	        		$speaker    = get_post( $cr3ativ_confspeaker );
                    $isconf   = get_post_meta( $speaker->ID, 'speakerisconf', $single = true ); 
                    if ($isconf=='1') {
                        $lastname   = get_post_meta( $speaker->ID, 'speakerlastname', $single = true ); 
                        $firstname  = get_post_meta( $speaker->ID, 'speakerfirstname', $single = true ); 
                        $infos   = get_post_meta( $speaker->ID, 'speakeradditionnal', $single = true ); 

                        print('<div>');
                        print("<strong>".strtoupper($lastname)." $firstname</strong>");
                        if (!empty($infos)) {
                            print(" (<em>$infos</em>)");
                        }
                        print("</div>");
                    }
				}
                foreach ( $cr3ativ_confspeakers as $cr3ativ_confspeaker ) {

                    $speaker    = get_post( $cr3ativ_confspeaker );
                    $isconf   = get_post_meta( $speaker->ID, 'speakerisconf', $single = true ); 
                    if ($isconf!='1') {
                        $lastname   = get_post_meta( $speaker->ID, 'speakerlastname', $single = true ); 
                        $firstname  = get_post_meta( $speaker->ID, 'speakerfirstname', $single = true ); 
                        $infos   = get_post_meta( $speaker->ID, 'speakeradditionnal', $single = true ); 

                        print('<div>');
                        print("<strong>".strtoupper($lastname)." $firstname</strong>");
                        if (!empty($infos)) {
                            print(" (<em>$infos</em>)");
                        }
                        print("</div>");
                    }
                }
				
			} 
            
            ?>
            </div><!-- End of speaker list -->

<?php

/*
//================= 
//tests modal        
//==================  
            <!-- Start of session content -->
<div class="modal modal-transparent fade" id="modal-transparent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
        
        <h4 class="modal-title" id="myModalLabel"><?php the_title(); ?></h4>
      </div>
      <div class="modal-body"><?php the_content(); ?></div>
    </div>
  </div>
</div>
//==================
// fin test modal
//==================
*/
?>    
                   <?php
/*                    
            <div>
                <p>
                    <?php the_content(); ?>
                   <p> 
                   
                   //<a class="conference-more" href="<?php the_permalink (); ?>"><?php _e( '[:fr]En savoir plus...[:en]See more...[:]', 'cr3at_conf' ); ?></a>
                   
                   </p>
                </p><!-- End of session content -->
            </div>
*/
?>                   
        </div>
        <?php endwhile; ?>

    </div><!-- End of content wrapper -->

    <!-- Clear Fix --><div class="cr3ativconference_clear"></div>

</div><!-- End of content wrapper -->

<?php get_footer(); ?>