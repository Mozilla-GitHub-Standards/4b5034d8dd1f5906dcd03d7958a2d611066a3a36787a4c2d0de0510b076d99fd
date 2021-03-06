<?php
global $spEvents;
$spEvents->loadDomainStylesScripts();

// Fetch the formats
$date_format = get_option("date_format");
$time_format = get_option("time_format");

get_header();
?>
<section id="content-main" class="hfeed vcalendar" role="main">
  <header id="events-calendar-header">
    <h1 class="section-title"><?php _e('Events List', $spEvents->pluginDomain) ?></h1>
    <p class="calendar-switch">
      <a class="ical" href="<?php bloginfo('url'); ?>/?ical">Download iCal</a>
      <a class="button on" href="<?php echo events_get_listview_link(); ?>"><?php _e('Event List', $spEvents->pluginDomain)?></a>
      <a class="button" href="<?php echo events_get_gridview_link(); ?>"><?php _e('Calendar', $spEvents->pluginDomain)?></a>
    </p>
  </header><!--#events-calendar-header-->

  <div id="tec-events-loop" class="tec-events">
  <?php while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID() ?>" class="vevent post <?php echo $alt ?>">
    <?php if ( is_new_event_day() ) : ?>
      <h4 class="event-day"><?php echo the_event_start_date( null, false ); ?></h4>
    <?php endif; ?>
      <aside class="event-date">
        <h3>When</h3>
        <?php // All day, single day
          if ( get_post_meta( $post->ID, '_EventAllDay' ) && 
               (the_event_start_date($post->ID) == the_event_end_date($post->ID)) ) : ?>
            <p><time class="dtstart" datetime="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></time></p>
        <?php // All day, multiple days
          elseif (get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID) != the_event_end_date($post->ID)) ) : ?>
            <p>
            <span class="start"><em>Start:</em> <time class="dtstart" datetime="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></time></span>
            <span class="end"><em>End:</em> <time class="dtend" datetime="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format ); ?></time></span>
            </p>
        <?php // Not all day, but the time spans more than one date (e.g., runs past midnight)
          elseif (!get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID, false, $date_format) < the_event_end_date($post->ID, false, $date_format)) ) : ?>
            <p>
            <span class="start"><em>Start:</em> <time class="dtstart" datetime="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format )."<br>".the_event_start_date( $post->ID, false, $time_format ); ?></time></span>
            <span class="end"><em>End:</em> <time class="dtend" datetime="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format )."<br>".the_event_end_date( $post->ID, false, $time_format ); ?></time></span>
            </p>
        <?php // Just a normal event.
          else : ?>
            <p><time class="dtstart" datetime="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></time>
            <span class="start"><em>Start:</em> <?php echo the_event_start_date( $post->ID, false, $time_format ); ?></span>
            <span class="end"><em>End:</em> <time class="dtend" datetime="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_end_date( $post->ID, false, $time_format ); ?></time></span>
            </p>
        <?php endif; ?>

        <?php // If the current datetime is greater than event datetime, the event is in the past
          if ( date('c') > the_event_end_date($post->ID, false, 'c') ) : ?>
          <p class="passed description"><strong>This event has passed.</strong></p>
        <?php endif; ?>
      </aside>

      <?php the_title('<h3 class="entry-title summary"><a class="url" href="'.get_permalink().'" title="'.the_title_attribute('echo=0').'" rel="bookmark">', '</a></h3>'); ?>
      <div class="entry-summary description">
         <?php the_excerpt(); ?>
      </div>
      <p><a class="more-link" href="<?php echo get_permalink($post->ID) ?>"><?php _e('Event details','qmo'); ?></a></p>

    </article> <!-- End post -->
  <?php endwhile; // posts ?>
  </div><!-- #tec-events-loop -->

<?php if ( events_displaying_upcoming() || events_displaying_past() ) : ?>
  <ul class="nav-paging">
  <?php // Display Previous Page Navigation
    if( events_displaying_upcoming() && get_previous_posts_link() ) : ?>
    <li class="prev"><?php previous_posts_link( 'Previous Events' ); ?></li>
  <?php elseif( events_displaying_upcoming() && !get_previous_posts_link( ) ) : ?>
    <li class="prev"><a href="<?php echo events_get_past_link(); ?>"><?php _e('Previous Events', $spEvents->pluginDomain ); ?></a></li>
  <?php elseif( events_displaying_past() && get_next_posts_link() ) : ?>
    <li class="prev"><?php next_posts_link( 'Previous Events' ); ?></li>
  <?php endif; ?>

  <?php // Display Next Page Navigation
    if( events_displaying_upcoming() && get_next_posts_link( ) ) : ?>
    <li class="next"><?php next_posts_link( '<span>Next Events</span>' ); ?></li>
  <?php elseif( events_displaying_past() && get_previous_posts_link( ) ) : ?>
    <li class="next"><?php previous_posts_link( '<span>Next Events</span>' ); ?></li>
  <?php elseif( events_displaying_past() && !get_previous_posts_link( ) ) : ?>
    <li class="next"><a href="<?php echo events_get_upcoming_link(); ?>"><?php _e('Next Events', $spEvents->pluginDomain); ?></a></li>
    <?php endif; ?>
  </ul>
<?php endif; ?>

</section><!-- /content-main -->

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-event') ) : else : endif; ?>
</section>
<?php get_footer(); ?>
