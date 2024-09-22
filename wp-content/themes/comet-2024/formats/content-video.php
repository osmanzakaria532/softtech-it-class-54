<article id="video-post" class="post-single">
    <div class="post-info">
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <h6 class="upper">
            <span>By</span>
            <a href="<?php the_author(); ?>"><?php the_author(); ?></a>
            <span class="dot"> </span>
            <span><?php the_time('d F Y'); ?></span>
            <span class="dot"></span>
            <a href="<?php the_permalink(); ?>" class="post-tag"><?php the_tags(); ?></a>
        </h6>
    </div>
    <!-- <div class="post-media">
        <div class="media-video">
            <iframe src="https://www.youtube.com/embed/rrT6v5sOwJg" frameborder="0"></iframe>
        </div>
    </div> -->
    <div class="post-body">
        <?php the_content(); ?>
        <p>
            <a href="#" class="btn btn-color btn-sm">Read More</a>
        </p>
    </div>
</article>