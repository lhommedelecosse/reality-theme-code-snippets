// This code initializes the custom RSS Feeds for the website
add_filter( 'init', 'pps_init_property_feed' );

function pps_init_property_feed() {
   add_feed( 'ppspropertyfeed', 'pps_property_feed');
}
