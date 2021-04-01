<?php
if (!is_user_logged_in()) {
    wp_redirect(esc_url(site_url('/')));
    exit;
}
get_header();
while (have_posts()) {
    the_post();
    pageBanner();
?>

    <div class="container container--narrow page-section">
        <ul class="min-list link-list" id="my-notes">
            <?php
            $userNotes = new WP_Query(array(
                'post_type' => 'note',
                'post_per_page' => -1,
                'author' => get_current_user_id()
            ));

            while ($userNotes->havePosts()) {
                $userNotes->the_post(); ?>
                <li>
                    <input class="title-field" type="" value="<?php echo esc_attr(get_the_title()); ?>">
                    <textarea name="" id="" cols="30" rows="10">
                            <?php echo esc_attr(get_the_content()); ?>
                        </textarea>
                </li>
            <?php
            }
            ?>
        </ul>
    </div>

<?php
}
get_footer();
?>