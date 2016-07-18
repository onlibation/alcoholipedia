<?php If (have_posts()) : ?>

  <div class="page-summary">
  <?php While (have_posts()) : the_post(); ?>
  <div class="sub-page">
    <h3><a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a></h3>
          
    <?php If ($thumb = $this->get_post_thumbnail()) : ?>
    <a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
      <img src="<?php Echo $thumb[1] ?>" width="<?php Echo $thumb[2] ?>" height="<?php Echo $thumb[3] ?>" alt="<?php the_title() ?>" class="preview-image alignleft" />
    </a>
    <?php EndIf; ?>
    

    <div class="clear"></div>
  </div>
  <?php EndWhile; ?>
  </div>

<?php EndIf; ?>