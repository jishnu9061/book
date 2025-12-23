<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parenthandle = 'parent-style'; 
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version')
    );
};

function custom_post_types_setup() {
    // Register Treatment Post Type
    register_post_type('treatment', array(
        'labels' => array(
            'name' => __('Treatments'),
            'singular_name' => __('Treatment'),
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'treatments'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'taxonomies' => array('category', 'post_tag'),
        'show_in_rest' => true,
    ));

    // Register Doctors Post Type
    register_post_type('doctor', array(
        'labels' => array(
            'name' => __('Doctors'),
            'singular_name' => __('Doctor'),
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'doctors'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'taxonomies' => array('category', 'post_tag'),
        'show_in_rest' => true,
    ));

    // Register Hospitals Post Type
    register_post_type('hospital', array(
        'labels' => array(
            'name' => __('Hospitals'),
            'singular_name' => __('Hospital'),
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'hospitals'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'taxonomies' => array('category', 'post_tag'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'custom_post_types_setup');

// Register Metaboxes
function custom_post_type_metaboxes() {
    // Treatment Metabox
    add_meta_box(
        'treatment_details', 
        'Treatment Details', 
        'render_treatment_metabox', 
        'treatment', 
        'side', 
        'default'
    );
    add_meta_box(
        'treatment_hospitals_meta_box',
        'Select Hospitals',
        'render_treatment_hospitals_meta_box',
        'treatment', 
        'side',
        'high'
    );

    // Hospital Metabox
    add_meta_box(
        'hospital_details',
        'Hospital Details',
        'render_hospital_metabox',
        'hospital',
        'side',
        'default'
    );

    // Doctor Metabox
    add_meta_box(
        'doctor_details',
        'Doctor Details',
        'render_doctor_metabox',
        'doctor',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'custom_post_type_metaboxes');

// Render Treatment Metabox
function render_treatment_metabox($post) {
    $days = get_post_meta($post->ID, '_treatment_days', true);
    $price = get_post_meta($post->ID, '_treatment_price', true);
    ?>
    <label for="treatment_days">Days:</label>
    <input type="number" id="treatment_days" name="treatment_days" value="<?php echo esc_attr($days); ?>" />
    <br><br>
    <label for="treatment_price">Price:</label>
    <input type="number" id="treatment_price" name="treatment_price" value="<?php echo esc_attr($price); ?>" />
    
    <?php
}

function render_treatment_hospitals_meta_box($post) {
    // Get saved values
    $hospital_1 = get_post_meta($post->ID, '_hospital_1', true);
    $hospital_2 = get_post_meta($post->ID, '_hospital_2', true);
    $hospital_3 = get_post_meta($post->ID, '_hospital_3', true);

    // Fetch all hospital posts
    $hospitals = get_posts([
        'post_type' => 'hospital',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    ?>
    <div>
        <label for="hospital_1">Hospital 1:</label>
        <select id="hospital_1" name="hospital_1" class="hospital-select">
            <option value="">Select a hospital</option>
            <?php foreach ($hospitals as $hospital): ?>
                <option value="<?php echo $hospital->ID; ?>" <?php selected($hospital_1, $hospital->ID); ?>>
                    <?php echo esc_html($hospital->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p id="hospital_1_details"></p>
    </div>

    <div>
        <label for="hospital_2">Hospital 2:</label>
        <select id="hospital_2" name="hospital_2" class="hospital-select">
            <option value="">Select a hospital</option>
            <?php foreach ($hospitals as $hospital): ?>
                <option value="<?php echo $hospital->ID; ?>" <?php selected($hospital_2, $hospital->ID); ?>>
                    <?php echo esc_html($hospital->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p id="hospital_2_details"></p>
    </div>

    <div>
        <label for="hospital_3">Hospital 3:</label>
        <select id="hospital_3" name="hospital_3" class="hospital-select">
            <option value="">Select a hospital</option>
            <?php foreach ($hospitals as $hospital): ?>
                <option value="<?php echo $hospital->ID; ?>" <?php selected($hospital_3, $hospital->ID); ?>>
                    <?php echo esc_html($hospital->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p id="hospital_3_details"></p>
    </div>
    <script>
        document.querySelectorAll('.hospital-select').forEach(function(select) {
            select.addEventListener('change', function() {
                const hospitalId = this.value;
                const detailsElement = this.nextElementSibling;

                if (hospitalId) {
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=get_hospital_details&hospital_id=' + hospitalId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                detailsElement.innerHTML = `Country: ${data.country}, Surgeries: ${data.surgeries}`;
                            } else {
                                detailsElement.innerHTML = 'No details available.';
                            }
                        });
                } else {
                    detailsElement.innerHTML = '';
                }
            });
        });
    </script>
    <?php
}

add_action('save_post', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['hospital_1'])) {
        update_post_meta($post_id, '_hospital_1', sanitize_text_field($_POST['hospital_1']));
    }
    if (isset($_POST['hospital_2'])) {
        update_post_meta($post_id, '_hospital_2', sanitize_text_field($_POST['hospital_2']));
    }
    if (isset($_POST['hospital_3'])) {
        update_post_meta($post_id, '_hospital_3', sanitize_text_field($_POST['hospital_3']));
    }
});



// Render Hospital Metabox
function render_hospital_metabox($post) {
    $country = get_post_meta($post->ID, '_hospital_country', true);
    $surgeries = get_post_meta($post->ID, '_hospital_surgeries', true);
    ?>
    <label for="hospital_country">Country:</label>
    <input type="text" id="hospital_country" name="hospital_country" value="<?php echo esc_attr($country); ?>" />
    <br><br>
    <label for="hospital_surgeries">Number of Surgeries:</label>
    <input type="number" id="hospital_surgeries" name="hospital_surgeries" value="<?php echo esc_attr($surgeries); ?>" />
    <?php
}

// Render Doctor Metabox
function render_doctor_metabox($post) {
    $experience = get_post_meta($post->ID, '_doctor_experience', true);
    $surgeries = get_post_meta($post->ID, '_doctor_surgeries', true);
    $hospital_id = get_post_meta($post->ID, '_doctor_hospital', true);

    $hospitals = get_posts([
        'post_type' => 'hospital',
        'numberposts' => -1,
    ]);
    ?>
    <label for="doctor_experience">Experience (Years):</label>
    <input type="number" id="doctor_experience" name="doctor_experience" value="<?php echo esc_attr($experience); ?>" />
    <br><br>
    <label for="doctor_surgeries">Number of Surgeries:</label>
    <input type="number" id="doctor_surgeries" name="doctor_surgeries" value="<?php echo esc_attr($surgeries); ?>" />
    <br><br>
    <label for="doctor_hospital">Hospital:</label>
    <select id="doctor_hospital" name="doctor_hospital">
        <option value="">Select Hospital</option>
        <?php foreach ($hospitals as $hospital): ?>
            <option value="<?php echo $hospital->ID; ?>" <?php selected($hospital_id, $hospital->ID); ?>>
                <?php echo esc_html($hospital->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}
// Save Treatment Metabox Data
function save_custom_metabox_data($post_id) {
    // Save Treatment Data
    if (isset($_POST['treatment_days'])) {
        update_post_meta($post_id, '_treatment_days', sanitize_text_field($_POST['treatment_days']));
    }
    if (isset($_POST['treatment_price'])) {
        update_post_meta($post_id, '_treatment_price', sanitize_text_field($_POST['treatment_price']));
    }
    if (isset($_POST['treatment_hospitals'])) {
        $sanitized_hospitals = array_map('sanitize_text_field', $_POST['treatment_hospitals']);
        update_post_meta($post_id, '_treatment_hospitals', $sanitized_hospitals);
    } else {
        delete_post_meta($post_id, '_treatment_hospitals');
    }

    // Save other data (for Hospital and Doctor)
    if (isset($_POST['hospital_country'])) {
        update_post_meta($post_id, '_hospital_country', sanitize_text_field($_POST['hospital_country']));
    }
    if (isset($_POST['hospital_surgeries'])) {
        update_post_meta($post_id, '_hospital_surgeries', sanitize_text_field($_POST['hospital_surgeries']));
    }
    if (isset($_POST['doctor_experience'])) {
        update_post_meta($post_id, '_doctor_experience', sanitize_text_field($_POST['doctor_experience']));
    }
    if (isset($_POST['doctor_surgeries'])) {
        update_post_meta($post_id, '_doctor_surgeries', sanitize_text_field($_POST['doctor_surgeries']));
    }
    if (isset($_POST['doctor_hospital'])) {
        update_post_meta($post_id, '_doctor_hospital', sanitize_text_field($_POST['doctor_hospital']));
    }
}
add_action('save_post', 'save_custom_metabox_data');


add_action('elementor/dynamic_tags/register', function($dynamic_tags) {
    // Register each custom dynamic tag
    $dynamic_tags->register_tag('Custom_Treatment_Days_Tag');
    $dynamic_tags->register_tag('Custom_Treatment_Price_Tag');
    // Register dynamic tags for Hospital 1, 2, 3
    $dynamic_tags->register_tag('Custom_Hospital_1_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_2_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_3_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_1_Featured_Image_Tag');

    // Register dynamic tags for Hospital 1, 2, 3 Country
    $dynamic_tags->register_tag('Custom_Hospital_1_Country_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_2_Country_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_3_Country_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_2_Featured_Image_Tag');

    // Register dynamic tags for Hospital 1, 2, 3 Surgeries
    $dynamic_tags->register_tag('Custom_Hospital_1_Surgeries_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_2_Surgeries_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_3_Surgeries_Tag');
    $dynamic_tags->register_tag('Custom_Hospital_3_Featured_Image_Tag');
});

// Base class for all custom tags
abstract class Custom_Treatment_Tag_Base extends \Elementor\Core\DynamicTags\Tag {
    public function get_group() {
        return 'post'; // Registers the tag in the "Post" group in Elementor.
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY]; 
    }
}
// Base Class for Hospital Dynamic Tags
abstract class Custom_Hospital_Tag_Base extends \Elementor\Core\DynamicTags\Tag {
    abstract protected function get_hospital_meta_key();

    protected function get_hospital_details($hospital_id, $meta_key = '') {
        $hospital = get_post($hospital_id);
        if (!$hospital) return null;

        $details = [
            'name' => $hospital->post_title,
            'country' => get_post_meta($hospital_id, '_hospital_country', true),
            'surgeries' => get_post_meta($hospital_id, '_hospital_surgeries', true),
        ];

        return $meta_key ? ($details[$meta_key] ?? 'N/A') : $details;
    }
}
// Tag for Treatment Days
class Custom_Treatment_Days_Tag extends Custom_Treatment_Tag_Base {
    public function get_name() {
        return 'custom_treatment_days_tag';
    }

    public function get_title() {
        return 'Treatment Days';
    }

    public function render() {
        $post_id = get_the_ID();
        $days = get_post_meta($post_id, '_treatment_days', true);
        echo esc_html($days);
    }
}

// Tag for Treatment Price
class Custom_Treatment_Price_Tag extends Custom_Treatment_Tag_Base {
    public function get_name() {
        return 'custom_treatment_price_tag';
    }

    public function get_title() {
        return 'Treatment Price';
    }

    public function render() {
        $post_id = get_the_ID();
        $price = get_post_meta($post_id, '_treatment_price', true);
        echo esc_html($price);
    }
}

// Tag for Treatment Hospitals
class Custom_Hospital_1_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_1_tag';
    }
    public function get_title() {
        return 'Hospital 1 Name';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_1';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'name');
        echo esc_html($details ?: 'No Hospital Assigned');
    }
}

// Hospital 1 Country Tag
class Custom_Hospital_1_Country_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_1_country_tag';
    }
    public function get_title() {
        return 'Hospital 1 Country';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_1';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'country');
        echo esc_html($details ?: 'N/A');
    }
}

// Hospital 1 Surgeries Tag
class Custom_Hospital_1_Surgeries_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_1_surgeries_tag';
    }
    public function get_title() {
        return 'Hospital 1 Surgeries';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_1';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'surgeries');
        echo esc_html($details ?: '0');
    }
}

class Custom_Hospital_1_Featured_Image_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() {
        return 'custom_hospital_1_featured_image_tag';
    }

    public function get_title() {
        return 'Hospital 1 Featured Image';
    }

    public function get_group() {
        return 'post';
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY];
    }

    public function render() {
        // Fetch the hospital ID stored in the treatment meta box
        $hospital_id = get_post_meta(get_the_ID(), '_hospital_1', true);
        if ($hospital_id) {
            // Fetch the featured image URL for the hospital post
            $featured_image = get_the_post_thumbnail_url($hospital_id, 'full');
            
            if ($featured_image) {
                echo $featured_image;
            } else {
                echo esc_url(''); // Optional: Default fallback image if no featured image
            }
        } else {
            echo esc_html('No hospital selected.'); // Message if no hospital is assigned
        }
    }
}


// Hospital 2 Name Tag
class Custom_Hospital_2_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_2_tag';
    }
    public function get_title() {
        return 'Hospital 2 Name';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_2';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'name');
        echo esc_html($details ?: 'No Hospital Assigned');
    }
}

// Hospital 2 Country Tag
class Custom_Hospital_2_Country_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_2_country_tag';
    }
    public function get_title() {
        return 'Hospital 2 Country';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_2';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'country');
        echo esc_html($details ?: 'N/A');
    }
}

// Hospital 2 Surgeries Tag
class Custom_Hospital_2_Surgeries_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_2_surgeries_tag';
    }
    public function get_title() {
        return 'Hospital 2 Surgeries';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_2';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'surgeries');
        echo esc_html($details ?: '0');
    }
}
class Custom_Hospital_2_Featured_Image_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_2_featured_image_tag';
    }

    public function get_title() {
        return 'Hospital 2 Featured Image';
    }

    public function get_group() {
        return 'post';
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY];
    }

    protected function get_hospital_meta_key() {
        return '_hospital_2';
    }

    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        if ($hospital_id) {
            $featured_image = get_the_post_thumbnail_url($hospital_id, 'full');
            if ($featured_image) {
                echo esc_url($featured_image);
            } else {
                echo esc_url(''); // Default fallback image URL if no featured image is found.
            }
        } else {
            echo esc_url(''); // Default fallback image URL if no hospital is assigned.
        }
    }
}

// Hospital 3 Name Tag
class Custom_Hospital_3_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_3_tag';
    }
    public function get_title() {
        return 'Hospital 3 Name';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_3';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'name');
        echo esc_html($details ?: 'No Hospital Assigned');
    }
}

// Hospital 3 Country Tag
class Custom_Hospital_3_Country_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_3_country_tag';
    }
    public function get_title() {
        return 'Hospital 3 Country';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_3';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'country');
        echo esc_html($details ?: 'N/A');
    }
}

// Hospital 3 Surgeries Tag
class Custom_Hospital_3_Surgeries_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_3_surgeries_tag';
    }
    public function get_title() {
        return 'Hospital 3 Surgeries';
    }
    public function get_group() {
        return 'post';
    }
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    protected function get_hospital_meta_key() {
        return '_hospital_3';
    }
    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        $details = $this->get_hospital_details($hospital_id, 'surgeries');
        echo esc_html($details ?: '0');
    }
}
class Custom_Hospital_3_Featured_Image_Tag extends Custom_Hospital_Tag_Base {
    public function get_name() {
        return 'custom_hospital_3_featured_image_tag';
    }

    public function get_title() {
        return 'Hospital 3 Featured Image';
    }

    public function get_group() {
        return 'post';
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY];
    }

    protected function get_hospital_meta_key() {
        return '_hospital_3';
    }

    public function render() {
        $hospital_id = get_post_meta(get_the_ID(), $this->get_hospital_meta_key(), true);
        if ($hospital_id) {
            $featured_image = get_the_post_thumbnail_url($hospital_id, 'full');
            if ($featured_image) {
                echo esc_url($featured_image);
            } else {
                echo esc_url(''); // Default fallback image URL if no featured image is found.
            }
        } else {
            echo esc_url(''); // Default fallback image URL if no hospital is assigned.
        }
    }
}


add_action('elementor/dynamic_tags/register', function($dynamic_tags) {
    // Register the custom tag for Doctors
    $dynamic_tags->register_tag('Custom_Doctors_Tag');
    $dynamic_tags->register_tag('Custom_Doctors_Experience_Tag');
    $dynamic_tags->register_tag('Custom_Doctors_Surgeries_Tag');
    $dynamic_tags->register_tag('Custom_Doctors_Hospital_Tag');
});

// Define the Doctors Dynamic Tag
class Custom_Doctors_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() {
        return 'custom_doctors_tag';
    }

    public function get_title() {
        return 'Doctors';
    }

    public function get_group() {
        return 'post'; 
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY]; 
    }

    public function render() {
        $post_id = get_the_ID();
        $doctors = get_post_meta($post_id, '_doctors', true); 

        if (is_array($doctors) && !empty($doctors)) {
            $doctor_names = [];
            foreach ($doctors as $doctor_id) {
                $doctor = get_post($doctor_id); 
                if ($doctor) {
                    $doctor_names[] = $doctor->post_title; 
                }
            }
            echo esc_html(implode(', ', $doctor_names)); 
        } else {
            echo 'No doctors assigned.';
        }
    }
}

// Define the Doctors Experience Dynamic Tag
class Custom_Doctors_Experience_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() {
        return 'custom_doctors_experience_tag';
    }

    public function get_title() {
        return 'Doctors Experience';
    }

    public function get_group() {
        return 'post'; 
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY]; 
    }

    public function render() {
        $post_id = get_the_ID();
        $experience = get_post_meta($post_id, '_doctor_experience', true); 

        echo esc_html($experience ? $experience : 'No experience assigned.');
    }
}

// Define the Doctors Surgeries Dynamic Tag
class Custom_Doctors_Surgeries_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() {
        return 'custom_doctors_surgeries_tag';
    }

    public function get_title() {
        return 'Doctors Surgeries';
    }

    public function get_group() {
        return 'post'; 
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY]; 
    }

    public function render() {
        $post_id = get_the_ID();
        $surgeries = get_post_meta($post_id, '_doctor_surgeries', true); 

        echo esc_html($surgeries ? $surgeries : 'No surgeries assigned.');
    }
}

// Define the Doctors Hospital Dynamic Tag
class Custom_Doctors_Hospital_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() {
        return 'custom_doctors_hospital_tag';
    }

    public function get_title() {
        return 'Doctors Hospital';
    }

    public function get_group() {
        return 'post'; 
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY]; 
    }

    public function render() {
        $post_id = get_the_ID();
        $hospitals = get_post_meta($post_id, '_doctor_hospital', true);
    
        if (!is_array($hospitals)) {
            $hospitals = !empty($hospitals) ? [$hospitals] : [];
        }
    
        if (!empty($hospitals)) {
            $hospital_names = [];
            foreach ($hospitals as $hospital_id) {
                $hospital = get_post($hospital_id);
                if ($hospital) {
                    $hospital_names[] = $hospital->post_title;
                }
            }
            if (!empty($hospital_names)) {
                echo esc_html(implode(', ', $hospital_names));
            } else {
                echo 'No valid hospitals found.';
            }
        } else {
            echo 'No hospitals assigned.';
        }
    }
    
}

add_action('wp_ajax_get_hospital_details', function () {
    if (!isset($_GET['hospital_id'])) {
        wp_send_json_error('Invalid request.');
    }

    $hospital_id = intval($_GET['hospital_id']);
    $country = get_post_meta($hospital_id, '_hospital_country', true); 
    $surgeries = get_post_meta($hospital_id, '_hospital_surgeries', true); 

    if ($hospital_id) {
        wp_send_json_success([
            'country' => $country ? $country : 'N/A',
            'surgeries' => $surgeries ? $surgeries : '0',
        ]);
    } else {
        wp_send_json_error('Hospital not found.');
    }
});

function create_testimonial_post_type() {
    $labels = array(
        'name'               => 'Testimonials',
        'singular_name'      => 'Testimonial',
        'menu_name'          => 'Testimonials',
        'name_admin_bar'     => 'Testimonial',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Testimonial',
        'new_item'           => 'New Testimonial',
        'edit_item'          => 'Edit Testimonial',
        'view_item'          => 'View Testimonial',
        'all_items'          => 'All Testimonials',
        'search_items'       => 'Search Testimonials',
        'not_found'          => 'No Testimonials found',
        'not_found_in_trash' => 'No Testimonials found in Trash'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-testimonial',
        'supports'           => array('title', 'editor', 'thumbnail'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'testimonials'),
    );

    register_post_type('testimonial', $args);
}
add_action('init', 'create_testimonial_post_type');


function add_testimonial_meta_box() {
    add_meta_box(
        'testimonial_meta_box',
        'Testimonial Details',
        'render_testimonial_meta_box',
        'testimonial',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_testimonial_meta_box');

function render_testimonial_meta_box($post) {
    // Get existing values
    $youtube_url = get_post_meta($post->ID, '_youtube_url', true);
    $selected_hospital = get_post_meta($post->ID, '_testimonial_hospital', true);
    $diseases = get_post_meta($post->ID, '_testimonial_diseases', true);

    // Get all hospitals from the 'hospital' post type
    $hospitals = get_posts(array(
        'post_type'      => 'hospital',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC'
    ));

    ?>
    <p>
        <label for="youtube_url">YouTube Video URL:</label>
        <input type="text" name="youtube_url" id="youtube_url" value="<?php echo esc_attr($youtube_url); ?>" class="widefat">
    </p>

    <p>
        <label for="testimonial_hospital">Select Hospital:</label>
        <select name="testimonial_hospital" id="testimonial_hospital" class="widefat">
            <option value="">Select a Hospital</option>
            <?php foreach ($hospitals as $hospital) { ?>
                <option value="<?php echo $hospital->ID; ?>" <?php selected($selected_hospital, $hospital->ID); ?>>
                    <?php echo esc_html($hospital->post_title); ?>
                </option>
            <?php } ?>
        </select>
    </p>

    <p>
        <label for="testimonial_diseases">Diseases Name (Comma Separated):</label>
        <input type="text" name="testimonial_diseases" id="testimonial_diseases" value="<?php echo esc_attr($diseases); ?>" class="widefat">
    </p>
    <?php
}

function save_testimonial_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['youtube_url'])) {
        update_post_meta($post_id, '_youtube_url', sanitize_text_field($_POST['youtube_url']));
    }
    
    if (isset($_POST['testimonial_hospital'])) {
        update_post_meta($post_id, '_testimonial_hospital', intval($_POST['testimonial_hospital']));
    }

    if (isset($_POST['testimonial_diseases'])) {
        update_post_meta($post_id, '_testimonial_diseases', sanitize_text_field($_POST['testimonial_diseases']));
    }
}
add_action('save_post', 'save_testimonial_meta');


add_action('elementor/dynamic_tags/register', function ($dynamic_tags) {

    $dynamic_tags->register_tag('Testimonial_Video_Tag');
    $dynamic_tags->register_tag('Testimonial_Hospital_Tag');
    $dynamic_tags->register_tag('Testimonial_Disease_Tag');
});

class Testimonial_Video_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() { return 'testimonial-video'; }
    public function get_title() { return 'Testimonial Video'; }
    public function get_group() { return 'post'; }
    public function get_categories() { return [\Elementor\Modules\DynamicTags\Module::URL_CATEGORY]; }
    public function render() {
        $post_id = get_the_ID();
        $youtube_url = get_post_meta($post_id, '_youtube_url', true);
        if ($youtube_url) echo esc_url($youtube_url);
    }
}

class Testimonial_Hospital_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() { return 'testimonial-hospital'; }
    public function get_title() { return 'Testimonial Hospital'; }
    public function get_group() { return 'post'; }
    public function get_categories() { return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY]; }
    public function render() {
        $post_id = get_the_ID();
        $hospital_id = get_post_meta($post_id, '_testimonial_hospital', true);
        if ($hospital_id) echo esc_html(get_the_title($hospital_id));
    }
}

class Testimonial_Disease_Tag extends \Elementor\Core\DynamicTags\Tag {
    public function get_name() { return 'testimonial-disease'; }
    public function get_title() { return 'Testimonial Diseases'; }
    public function get_group() { return 'post'; }
    public function get_categories() { return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY]; }
    public function render() {
        $post_id = get_the_ID();
        $diseases = get_post_meta($post_id, '_testimonial_diseases', true);
        if ($diseases) echo esc_html($diseases);
    }
}