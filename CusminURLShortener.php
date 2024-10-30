<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class CusminURLShortener
{
    const SLUG = 'cusmin-url-shortener';
    const PLUGIN_NAME = 'Cusmin URL Shortener';

    private $options;

    public function __construct(){
        if(!is_admin()) {
            $this->registerPublicHooks();
            return;
        }

        $this->loadOptions();
        $this->registerHooks();
    }

    public function registerScripts(){
        wp_register_script( self::SLUG, plugins_url( '/js/'.self::SLUG.'.js', __FILE__ ), array( 'jquery' ) );
        wp_register_style( self::SLUG, plugins_url( '/css/'.self::SLUG.'.css', __FILE__ ) );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( self::SLUG );
        wp_enqueue_style( self::SLUG );
    }

    public function ajax_short(){

        if(!is_user_logged_in()){
            die;
        }

        if ( !wp_verify_nonce( $_REQUEST['nonce'], "cusmin_us_short_nonce")) {
            exit("No naughty business please");
        }

        if(empty($_REQUEST["url"])){
            die('URL is not set');
        }

        $url = $_REQUEST["url"];

        $sh = new CusminGoogl($this->getGoogleAPIKey());
        if(!empty($_REQUEST['expand'])){
            $url = $sh->expand($url);
        }else{
            $url = $sh->shorten($url);
        }

        if(empty($url)){
            return wp_send_json(['status' => 'error', 'message' => 'Check API Key']);
        }

        return wp_send_json(['status' => 'success', 'url' => $url]);

        die();
    }

    public function ajax_expand(){
        $_REQUEST['expand'] = 'true';
        $this->ajax_short();
    }

    public function footer_scripts(){
        wp_nonce_field( 'cusmin_us_short_nonce', 'cusmin_us_short_nonce' );
    }

    public function registerAjax(){
        add_action("wp_ajax_cusmin_us_short", array($this, "ajax_short"));
        add_action("wp_ajax_nopriv_cusmin_us_short", array($this, "ajax_short"));

        add_action("wp_ajax_cusmin_us_expand", array($this, "ajax_expand"));
        add_action("wp_ajax_nopriv_cusmin_us_expand", array($this, "ajax_expand"));
    }

    private function loadOptions(){
        $this->options = get_option( self::SLUG );
        if(!$this->options){
            $this->options = [];
        }
    }

    private function getGoogleAPIKey(){
        return !empty($this->options['google-api-key'])?$this->options['google-api-key']:'';
    }

    private function getAdminBarShortener(){
        return !empty($this->options['admin-bar-shortener'])?$this->options['admin-bar-shortener']:'';
    }

    private function getUndoShortening(){
        return !empty($this->options['shorten-undo'])?$this->options['shorten-undo']:'';
    }

    private function getFieldShorteningDisabled(){
        return !empty($this->options['shorten-field-disable'])?$this->options['shorten-field-disable']:'';
    }

    private function getAutomaticCFShortening(){
        return !empty($this->options['automatic-cf-shorten'])?$this->options['automatic-cf-shorten']:'';
    }

    private function getAutomaticCFForceShortening(){
        return !empty($this->options['automatic-cf-shorten-force'])?$this->options['automatic-cf-shorten-force']:'';
    }

    private function getShortenedPermalink(){
        return !empty($this->options['shorten-permalink'])?$this->options['shorten-permalink']:'';
    }

    public function settingsPage(){
        ?>
        <div class="wrap">
            <div class="cusmin-us-logo"></div>
            <h1><?php echo self::PLUGIN_NAME; ?> Settings&nbsp;<a target="_blank" href="https://cusmin.com/url-shortener?ref=cus-title" class="button button-secondary">Help</a></h1>
            <iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fcusmin.com%2Furl-shortener&width=96&layout=button&action=like&size=small&show_faces=false&share=true&height=65&appId" width="96" height="65" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
            <form method="post" action="options.php">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><h2>Google URL Shortener</h2></th>
                    </tr>
                    <tr valign="top">
                        <th scope="row">API KEY (<a target="_blank" href="https://console.developers.google.com/apis/library/urlshortener.googleapis.com?q=shortener">get your key</a>)</th>
                        <td><input style="width: 330px" type="text" name="<?php echo self::SLUG; ?>[google-api-key]" value="<?php echo $this->getGoogleAPIKey(); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><h2>Settings</h2></th>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Hide Admin Bar shortener</th>
                        <td><input type="checkbox" name="<?php echo self::SLUG; ?>[admin-bar-shortener]" value="1"<?php checked( $this->getAdminBarShortener() ); ?>  /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Disable in-field shortening</th>
                        <td><input type="checkbox" name="<?php echo self::SLUG; ?>[shorten-field-disable]" value="1"<?php checked( $this->getFieldShorteningDisabled() ); ?>  /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Disable reverse shortening</th>
                        <td><input type="checkbox" name="<?php echo self::SLUG; ?>[shorten-undo]" value="1"<?php checked( $this->getUndoShortening() ); ?>  /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><h2>Advanced Settings</h2></th>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Shorten URLs in the Custom Fields Automatically
                            <span class="description">This will shorten URLs in the custom fields and ACF fields on post save. It could take longer to save the post the first time, if this option is enabled.</span>
                        </th>
                        <td><input type="checkbox" name="<?php echo self::SLUG; ?>[automatic-cf-shorten]" value="1"<?php checked( $this->getAutomaticCFShortening() ); ?>  /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Create Hidden Shortened Post Permalink Field
                            <span class="description">This will create a new custom field named "<strong>cus_permalink</strong>" when a post is saved. You can use this field later in your template like "<strong>get_field('cus_permalink')</strong>" or as a shortcode "<strong>[cus_permalink]</strong>".<br>
                                <p><strong>Shortcode examples</strong></p>
                                <ul>
                                    <li><strong>[cus_permalink]</strong> - Shortened link</li>
                                    <li><strong>[cus_permalink text_only=true]</strong> - Shortened link as a text</li>
                                    <li><strong>[cus_permalink text="My Text" new_tab=true class="my-class"]</strong> - Custom attributes </li>
                                </ul>
                            </span>
                        </th>
                        <td><input type="checkbox" name="<?php echo self::SLUG; ?>[shorten-permalink]" value="1"<?php checked( $this->getShortenedPermalink() ); ?>  /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Force URL Shortening in the Custom Fields After Every Post Save
                            <span class="description">It is not recommended to have this option always enabled, but it's useful to temporary enable it when you want to refresh the URLs of a specific post. It works only if CF URL shortening options are enabled.</span>
                        </th>
                        <td><input type="checkbox" name="<?php echo self::SLUG; ?>[automatic-cf-shorten-force]" value="1"<?php checked( $this->getAutomaticCFForceShortening() ); ?>  /></td>
                    </tr>
                </table>
                <?php
                // This prints out all hidden setting fields
                settings_fields( self::SLUG.'-group' );
                do_settings_sections( self::SLUG.'-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    function admin_menu() {
        add_options_page(
            __( self::PLUGIN_NAME, self::SLUG ),
            __( self::PLUGIN_NAME, self::SLUG ),
            'manage_options',
            self::SLUG,
            array(
                $this,
                'settingsPage'
            )
        );
    }

    function admin_bar_menu(\WP_Admin_Bar $bar){
        $bar->add_menu( array(
            'id'     => 'cusmin-us-ab',
            'parent' => 'top-secondary',
            'group'  => null,
            'title'  => __( '<input placeholder="Shorten URL" type="text" class="ab-cusmin-input"/><div class="cusmin-go-shorten">GO</div>', self::SLUG ),
            'href'   => '#',
            'meta'   => array(
                'class'    => 'cusmin-us-ab--item',
                'tabindex' => 0,
            ),
        ) );
    }

    public function onAdminInit(){
        $this->registerScripts();
        $this->registerAjax();

        register_setting( self::SLUG.'-group', self::SLUG );
    }

    public function pluginLinks( $links ) {
        $newLinks = array(
            '<a href="options-general.php?page='.self::SLUG.'" >' . __( 'Settings' ) . '</a>',
            '<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=url.shortener@cusmin.com&item_name=Support+for+Cusmin+URL+Shortener+Plugin+Development" >' . __( 'Donate' ) . '</a>',
            '<a target="_blank" href="https://cusmin.com/url-shortener?ref=plugins-page" >' . __( 'Help' ) . '</a>'
        );
        return array_merge($newLinks, $links);
    }

    public function disableShorteningBodyClass($classes){
        $classes .= ' '.self::SLUG.'-disable-undo ';
        return $classes;
    }

    public function disableFieldShorteningBodyClass($classes){
        $classes .= ' '.self::SLUG.'-disable-field ';
        return $classes;
    }

    //public function updatePermalinkBeforeSaving($post_id , $postarr ){
    public function updatePermalinkBeforeSaving($post_id, $post_after, $post_before){
        remove_action( 'post_updated', array($this, 'updatePermalinkBeforeSaving'), 10, 3 );

        $cus_permalink = get_post_meta($post_id, 'cus_permalink', true);
        if((!$this->isURLShorten($cus_permalink) || $this->getAutomaticCFForceShortening() || ($post_after->post_name !== $post_before->post_name)) && $post_after->post_status == 'publish'){
            $sh = new CusminGoogl($this->getGoogleAPIKey());
            $url = $sh->shorten(get_permalink($post_id));
            if($url){
                update_post_meta($post_id, 'cus_permalink', $url);
            }
        }

        add_action( 'post_updated', array($this, 'updatePermalinkBeforeSaving'), 10, 3 );
    }

    public function cus_permalink_shortcode($atts){
        $permalink = get_field('cus_permalink');
        if(!$permalink){
            return '';
        }

        if(!empty($atts['text_only'])){
            return $permalink;
        }

        $text = $permalink;
        if(!empty($atts['text'])){
            $text = $atts['text'];
        }
        $new_tab = '';
        if(!empty($atts['new_tab']) && $atts['new_tab']){
            $new_tab = ' target="_blank"';
        }
        $cls = '';
        if(!empty($atts['class'])){
            $cls = ' class="'.$atts['class'].'" ';
        }
        return '<a href="'.$permalink.'" '.$new_tab.' '.$cls.' >'.$text.'</a>';
    }

    public function updatePostLinksBeforeSaving($post_id , $postarr ){
        remove_action( 'save_post', array($this, 'updatePostLinksBeforeSaving'), 99, 2 );

        $postFields = get_fields($post_id);

        if(!empty($postFields)){
            $sh = new CusminGoogl($this->getGoogleAPIKey());
            foreach((array) $postFields as $key => $val){
                if (filter_var($val, FILTER_VALIDATE_URL)) {
                    if(!$this->isURLShorten($val) || $this->getAutomaticCFForceShortening()){
                        $url = $sh->shorten($val);
                        if($url){
                            update_field($key, $url, $post_id);
                        }
                    }
                }
            }
        }
        add_action( 'save_post', array($this, 'updatePostLinksBeforeSaving'), 99, 2 );
    }

    private function isURLShorten($url = ''){
        return strpos($url, 'goo.gl');
    }

    private function registerPublicHooks(){
        add_shortcode( 'cus_permalink', array($this, 'cus_permalink_shortcode') );
    }

    private function registerHooks(){

        if(!$this->getAdminBarShortener()){
            add_action('admin_bar_menu', array( $this, 'admin_bar_menu' ) );
        }
        if($this->getUndoShortening()){
            add_filter( 'admin_body_class', array($this, 'disableShorteningBodyClass') );
        }
        if($this->getFieldShorteningDisabled()){
            add_filter( 'admin_body_class', array($this, 'disableFieldShorteningBodyClass') );
        }
        if($this->getAutomaticCFShortening()){
            add_action( 'save_post', array($this, 'updatePostLinksBeforeSaving'), 99, 2 );
        }
        if($this->getShortenedPermalink()){
            add_action( 'post_updated', array($this, 'updatePermalinkBeforeSaving'), 10, 3 );
        }
        add_action('admin_menu', array( $this, 'admin_menu' ) );
        add_action('admin_init', array($this, 'onAdminInit'));
        add_action('admin_footer', array($this, "footer_scripts"));
        add_filter("plugin_action_links_".self::SLUG.'/index.php', array($this, 'pluginLinks') );
    }
}

//TODO: Add cus-disable class support to the ACF to disable particular URL shortening