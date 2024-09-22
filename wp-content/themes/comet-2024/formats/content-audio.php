    <article id="audio-post" class="post-single">
        <div class="post-info">
            <h2><a href=" <?php the_permalink(); ?> "><?php the_title(); ?></a></h2>
            <h6 class="upper">
                <span>By</span>
                <a href="<?php the_author(); ?>"> <?php the_author(); ?></a>
                <span class="dot"> </span>
                <span> <?php the_time('d F Y'); ?> </span>
                <span class="dot"></span>
                <a href="#" class="post-tag"><?php the_tags(); ?></a>
            </h6>
        </div>
        <!-- <div class="post-media">
            <div class="media-audio">
                <iframe
                    src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/51057943&amp;amp;color=ff5500&amp;amp;auto_play=false&amp;amp;hide_related=false&amp;amp;show_comments=true&amp;amp;show_user=true&amp;amp;show_reposts=false"
                    frameborder="0">
                </iframe>
            </div>
        </div> -->
        <div class="post-body">
            <?php the_content(); ?>
            <p><a href="<?php the_permalink(); ?>" class="btn btn-color btn-sm">Read More</a></p>
        </div>
    </article>