<?php
require_once('wp-blog-header.php');
$kyero_version = '3';
$postCount = 10; // The number of posts to show in the feed
$postType = 'iwp_property'; // post type to display in the feed
query_posts( array( 'post_type' => $postType ) ); 

// Send the headers
header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
echo '<?xml version="1.0" encoding="utf-8"?>';

$xmlout = '<root>';
	$xmlout .= '<kyero>';
		$xmlout .= '<feed_version>' . $kyero_version . '</feed_version>';
	$xmlout .= '</kyero>';
	echo $xmlout;
	//Properties loop
	while(have_posts()) : the_post();
		$property = IWP_Property::get_property(get_the_ID());
		if($property) {
			$status = $property->get_status_obj();
		}			
		if($property->has_status('publish') && $status->name = 'For Sale') {
			$xmlout = '<property>';
				$xmlout .= '<id>HI' . $property->get_id() . '</id>';
				$xmlout .= '<date>' . get_the_modified_date('Y-m-d H:i:s') . '</date>';
				$xmlout .= '<ref>HI' . $property->get_id() . '</ref>';
				$xmlout .= '<price>' . $property->get_price_number() . '</price>';
				$xmlout .= '<currency>' . iwp_get_currency() . '</currency>';
				$xmlout .= '<price_freq>' . $status->name . '</price_freq>';
				//Not supported by theme
				$xmlout .= '<part_ownership>0</part_ownership>';
				//Not supported by theme
				$xmlout .= '<leasehold>0</leasehold>';
				$xmlout .= '<new_build>0</new_build>';
				$type = $property->get_type();
				$xmlout .= '<type>' . $type->name . '</type>';
				//Address details poorly supported by theme/plugins
				$xmlout .= '<town>' . get_post_meta( get_the_ID(), '_iwp_address', true ) . '</town>';
				$locations = wp_get_post_terms(get_the_ID(), 'iwp_location');
				foreach ($locations as $location){
					$location_list .= $location->name.', ';
				}
				$xmlout .= '<province>' . rtrim($location_list,', ') . '</province>';
				//This should be changed based on website
				$xmlout .= '<country>Spain</country>';
				$xmlout .= '<location>';
					$xmlout .= '<latitude>' . get_post_meta( get_the_ID(), '_iwp_latitude', true ) . '</latitude>';
					$xmlout .= '<longitude>' . get_post_meta( get_the_ID(), '_iwp_longitude', true ) . '</longitude>';
				$xmlout .= '</location>';
				$xmlout .= '<location_detail>' . $property->get_address() . '</location_detail>';
				$xmlout .= '<beds>' . $property->get_property_field_value( 'bedrooms' ) . '</beds>';
				$xmlout .= '<baths>' . $property->get_property_field_value( 'bathrooms' ) . '</baths>';
				//Not supported by theme, use features instead
				$xmlout .= '<pool>0</pool>';
				$xmlout .= '<surface_area>';
					$xmlout .= '<built>' . $property->get_property_field_value( 'area_size' ) . '</built>';
					$xmlout .= '<plot>' . $property->get_property_field_value( 'land_size' ) . '</plot>';
				$xmlout .= '</surface_area>';
				//Not supported by theme
				$xmlout .= '<energy_rating>';
					$xmlout .= '<consumption>X</consumption>';
					$xmlout .= '<emissions>X</emissions>';
				$xmlout .= '</energy_rating>';
				$xmlout .= '<url>';
					$xmlout .= '<en>' . get_the_permalink() . '</en>';
				$xmlout .= '</url>';
				//Concatinate Title & Description. Remove all HTML and Escape Codes.
				$xmlout .= '<desc>';
					$theContent = wp_filter_nohtml_kses(html_entity_decode(get_the_title()));
					$theContent .= ' : ';
					$theContent .= wp_filter_nohtml_kses(html_entity_decode(get_the_content()));
					$xmlout .= '<en>' . $theContent . '</en>';
				$xmlout .= '</desc>';
				//Loop through features
				$xmlout .= '<features>';
					$features = $property->get_features();
					foreach ( $features as $key => $feature ) {
   			     		$xmlout .= '<feature>' . $feature->name . '</feature>';
		        	}
				$xmlout .= '</features>';
				//Not supported by theme
				$xmlout .= '<notes></notes>';
				$xmlout .= '<images>';
					//Loop through images
					$gallery = $property->get_gallery();
					$counter = 0;
					foreach ( $gallery as $key => $image_id ) {
						$counter++;
						$image_url = wp_get_attachment_url( $image_id );
						$xmlout .= '<image id="' . $counter . '">';
							$xmlout .= '<url>' . $image_url . '</url>';
						$xmlout .= '</image>';
						}
				$xmlout .= '</images>';
			$xmlout .= '</property>';
			}
		//Output the property
		echo $xmlout;
	endwhile;
$xmlout .= '</root>';
//Finish the script
echo $xmlout;
// Reset Query
wp_reset_query();
?>
