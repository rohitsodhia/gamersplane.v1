<?php
/*
	Main content display
*/

global $first;
$extraClass = $first?'first':'';
if ($first) $first = FALSE;
?>

		<article id="post-<?php the_ID(); ?>" <?php post_class($extraClass); ?>>
			<header class="entry-header">
				<?php if ( is_sticky() ) : ?>
				<hgroup>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo 'Permalink to '.the_title_attribute(array('echo' => 0)); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				</hgroup>
				<?php else : ?>
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo 'Permalink to '.the_title_attribute(array('echo' => 0)); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
				<?php endif; ?>

				<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-meta">
					Posted on <?php the_date(); ?> <?php the_time(); ?> by <a href="<?=SITEROOT?>/users/<?php echo get_the_author_meta('id'); ?>" class="username"><?php the_author() ?></a>
				</div><!-- .entry-meta -->
				<?php endif; ?>
				
				<?php if ( comments_open() && ! post_password_required() ) : ?>
				<div class="comments-link">
					<?php comments_popup_link('0', '1', '%'); ?>
				</div>
				<?php endif; ?>
				
				<hr>
			</header><!-- .entry-header -->
			
			<?php if ( is_search() ) : // Only display Excerpts for Search ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
			<?php else : ?>
			<div class="entry-content">
				<?php the_content('Continue reading <span class="meta-nav">&rarr;</span>'); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->
			<?php endif; ?>

			<footer class="entry-meta">
				<?php $show_sep = false; ?>
				<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
				<?php
					/* translators: used between list items, there is a space after the comma */
					$categories_list = get_the_category_list(', ');
					if ( $categories_list ):
				?>
				<span class="cat-links">
					<?php echo '<span class="entry-utility-prep entry-utility-prep-cat-links">Posted in</span> '.$categories_list;
					$show_sep = true; ?>
				</span>
				<?php endif; // End if categories ?>
				<?php
					/* translators: used between list items, there is a space after the comma */
					$tags_list = get_the_tag_list(', ');
					if ( $tags_list ):
					if ( $show_sep ) : ?>
				<span class="sep"> | </span>
					<?php endif; // End if $show_sep ?>
				<span class="tag-links">
					<?php echo '<span class="entry-utility-prep entry-utility-prep-tag-links">Tagged</span> '.$tags_list;
					$show_sep = true; ?>
				</span>
				<?php endif; // End if $tags_list ?>
				<?php endif; // End if 'post' == get_post_type() ?>

				<?php if ( comments_open() ) : ?>
				<?php if ( $show_sep ) : ?>
				<span class="sep"> | </span>
				<?php endif; // End if $show_sep ?>
				<span class="comments-link"><?php comments_popup_link('<span class="leave-reply">Leave a reply</span>', '<b>1</b> Reply','<b>%</b> Replies'); ?></span>
				<?php endif; // End if comments_open() ?>

				<?php edit_post_link('Edit', '<span class="edit-link">', '</span>'); ?>
			</footer><!-- #entry-meta -->
		</article><!-- #post-<?php the_ID(); ?> -->
