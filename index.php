<?php

/**
 * The main template file
 */

get_header();
?>
<main>

    <section class="content">
        <div class="container">

            <!-- Main content -->
            <article class="articles">
                <h2 class="title">Статьи</h2>

                <div class="articles__wrapper">
                    <?php
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                    $args = array(
                        'post_type' => 'post',
                        'category_name' => 'articles',
                        'posts_per_page' => 10,
                        'paged' => $paged,
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post(); ?>
                            <div class="article">
                                <div class="article__img">
                                    <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                                </div>

                                <div class="article__content">
                                    <a href="<?php the_permalink(); ?>" class="article__title"><?php the_title(); ?></a>
                                    <div class="article__subtitle"><?php the_excerpt(); ?></div>

                                    <div class="article__footer">
                                        <div class="article__autor">Автор: <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                                <?php the_author(); ?>
                                            </a>
                                        </div>

                                        <div class="article__rating rating" data-post-id="<?php the_ID(); ?>">
                                            <button class="rating__like-btn">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_6201_204)">
                                                        <path d="M11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22Z" fill="#43B05C" />
                                                        <path d="M11 5.71997V16.72" stroke="white" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M16.5 11H5.5" stroke="white" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_6201_204">
                                                            <rect width="22" height="22" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </button>
                                            <?php
                                            global $wpdb;
                                            $table_name = $wpdb->prefix . 'post_likes';
                                            $rating = $wpdb->get_var($wpdb->prepare(
                                                "SELECT SUM(vote) FROM $table_name WHERE post_id = %d",
                                                get_the_ID()
                                            ));
                                            $rating = $rating ? $rating : 0;

                                            $rating_class = '';
                                            if ($rating > 0) {
                                                $rating_class = 'rating__positive';
                                            } elseif ($rating < 0) {
                                                $rating_class = 'rating__negative';
                                            }
                                            ?>
                                            <span class="rating__value <?php echo $rating_class; ?>">
                                                <?php echo $rating; ?>
                                            </span>
                                            <button class="rating__dislike-btn">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_6201_209)">
                                                        <path d="M11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22Z" fill="#ED8A19" />
                                                        <path d="M16.72 11H5.28" stroke="white" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_6201_209">
                                                            <rect width="22" height="22" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                    <?php endwhile;

                        // pagination
                        $pagination = paginate_links(array(
                            'total' => $query->max_num_pages,
                            'current' => $paged,
                            'format' => '?paged=%#%',
                            'show_all' => false,
                            'mid_size' => 1,
                            'end_size' => 2,
                            'prev_text' => __('Назад'),
                            'next_text' => __('Вперед'),
                            'type' => 'list',
                        ));

                        if ($pagination) {
                            echo '<nav class="pagination">' . $pagination . '</nav>';
                        }

                    endif;

                    wp_reset_postdata();
                    ?>
                </div>
            </article><!-- End main content -->

            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar__wrapper">
                    <h2 class="title">Sidebar</h2>
                </div>
            </aside><!-- End sidebar -->
        </div>
    </section>





</main>








<?php get_footer(); ?>