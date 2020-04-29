<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die(_e('You are not allowed to call this page directly.', 'final-tiles-grid-gallery-lite')); } ?>

<?php $ftg_subtitle = "Dashboard" ?>    

<?php
	$galleries = $this->FinalTilesdb->getGalleries();
?>

<div class="bd wrap">
	<h1 class="wp-heading-inline"><?php _e('Final Tiles Gallery') ?> <small><?php echo FTGVERSION . " " . FTG_PLAN ?></small></h1>
	<h2 class="ftg-subtitle"><?php _e('Galleries','final-tiles-grid-gallery-lite') ?></h2>
	<hr class="wp-header-end">
	<?php if(count($galleries) == 0) : ?>
	<div class="row ">
		<div class="col s9">
			<div class="card-panel light-green lighten-4">
				<h5 class="cyan-text text-darken-3"><?php _e('Welcome to Final Tiles Grid Gallery!', 'final-tiles-grid-gallery-lite')?></h5>
				<p>
					<?php _e('Create your first awesome gallery, click', 'final-tiles-grid-gallery-lite')?> <a href="?page=ftg-add-gallery"><?php _e('here', 'final-tiles-grid-gallery-lite')?></a>.
				</p>
			</div>
		</div>
		<div class="col s3">
			<?php if ( ftg_fs()->is_not_paying()) : ?>			
			<ul class="collapsible gallery-actions">
				<li class="active">
					<div class="collapsible-header"><?php _e('Upgrade', 'final-tiles-grid-gallery-lite') ?>: <?php _e('unlock features', 'final-tiles-grid-gallery-lite') ?></div>
					<div class="collapsible-body">
						<div class="ftg-upsell">
							<a href="<?php echo ftg_fs()->get_upgrade_url() ?>"><i class="fa fa-hand-o-right"></i> <?php _e('Upgrade', 'final-tiles-grid-gallery-lite') ?></a>
						</div>
						<p>or save 30% purchasing the <strong>BUNDLE</strong>:</p>
						<div class="ftg-upsell">
							<a target="_blank" href="https://www.final-tiles-gallery.com/wordpress/bundle">
							<i class="fa fa-star"></i>
							Bundle: 30% <?php _e('discount', 'final-tiles-grid-gallery-lite') ?></a>
						</div>
						<p class="upsell-info">
						<?php _e('GET 3 plugins', 'final-tiles-grid-gallery-lite') ?>: Final Tiles Gallery Ultimate + EverlightBox + PostSnippet
						</p>
					</div>
				</li>
			</ul>
			<?php endif ?>
		</div>
	</div>
	<?php else : ?>
	<div id="gallery-list" class="row">
		<form id="reloadform"></form>
		<div class="col s9">
		<?php wp_nonce_field('FinalTiles_gallery', 'FinalTiles_gallery'); ?>

			<table class="wp-list-table widefat fixed striped posts">
				<thead>
				<tr>
					<td scope="col" class="manage-column column-title column-primary">
						<?php _e('Title', 'final-tiles-grid-gallery-lite') ?>
					</td>	
					<td scope="col" class="manage-column column-title column-primary">
						<?php _e('Description', 'final-tiles-grid-gallery-lite') ?>
					</td>
					<td scope="col" class="manage-column column-title column-primary">		
						<?php _e('type', 'final-tiles-grid-gallery-lite') ?>			
					</td>
					<td scope="col" class="manage-column column-title column-primary">		
						<?php _e('Shortcode', 'final-tiles-grid-gallery-lite') ?>			
					</td>
				</thead>

				<tbody id="the-list">
					<?php foreach($galleries as $gallery) : ?>
					<tr id="gallery-<?php print $gallery->Id ?>" class="iedit author-self level-0 post-10 type-post status-publish format-standard hentry">
						<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
							<strong>
								<a href="?page=ftg-lite-gallery-admin&id=<?php print $gallery->Id ?>"><?php print $gallery->name ?></a>
							</strong>
							<div class="row-actions">
								<span class="edit">
									<a href="?page=ftg-lite-gallery-admin&id=<?php print $gallery->Id ?>" aria-label="Edit"><?php _e("Edit","final-tiles-grid-gallery-lite") ?></a> |
								</span>
								<span class="edit">
									<a href="#" class="clone-gallery" data-gid="<?php print $gallery->Id ?>" aria-label="Clone"><?php _e("Clone gallery","final-tiles-grid-gallery-lite") ?></a> |
								</span>
								<span class="trash">
									<a href="#delete-gallery-modal" data-gid="<?php print $gallery->Id ?>" class="modal-trigger submitdelete"><?php _e("Delete gallery", "final-tiles-grid-gallery-lite") ?></a>
								</span>							
							</div>
						</td>
						<td class="title column-title has-row-actions column-primary">
							<?php print $gallery->description ?>
						</td>
						<td class="title column-title has-row-actions column-primary">
							<?php print $gallery->source ?>
						</td>
						<td class="title column-title has-row-actions column-primary">
                            <input readonly type="text" value="[FinalTilesGallery id='<?php echo $gallery->Id ?>']" style="height:30px;">
                            <a href="#" title="Click to copy shortcode" class="copy-ftg-shortcode button button-primary dashicons dashicons-format-gallery" style="width:40px;"></a><span style="margin-left:15px;"></span>
						</td>
					</tr>
					<?php endforeach ?>
				</tbody>	
			</table>
		</div>
		<div class="col s3">
			<?php if (ftg_fs()->is_not_paying() ) : ?>
			<ul class="collapsible gallery-actions">
				<li class="active">
					<div class="collapsible-header"><?php _e('Upgrade', 'final-tiles-grid-gallery-lite') ?>: <?php _e('unlock features', 'final-tiles-grid-gallery-lite') ?></div>
					<div class="collapsible-body">
						<div class="ftg-upsell">
							<a href="<?php echo ftg_fs()->get_upgrade_url() ?>"><i class="fa fa-hand-o-right"></i> <?php _e('Upgrade', 'final-tiles-grid-gallery-lite') ?></a>
						</div>
						<p>or save 30% purchasing the <strong>BUNDLE</strong>:</p>
						<div class="ftg-upsell">
							<a target="_blank" href="https://www.final-tiles-gallery.com/wordpress/bundle">
							<i class="fa fa-star"></i>
							Bundle: 30% <?php _e('discount', 'final-tiles-grid-gallery-lite') ?></a>
						</div>
						<p class="upsell-info">
						<?php _e('GET 3 plugins', 'final-tiles-grid-gallery-lite') ?>: Final Tiles Gallery Ultimate + EverlightBox + PostSnippet
						</p>
					</div>
				</li>
			</ul>
			<?php endif ?>			
			<?php if (ftg_fs()->is_paying() && false) : ?>
			<ul class="collapsible gallery-actions">
				<li class="active">
					<div class="collapsible-header">
					<?php _e('Redeem your coupon', 'final-tiles-grid-gallery-lite')?>
					</div>
					<div class="collapsible-body">
						<a href="?page=ftg-add-gallery" class="button components-button is-primary"><?php _e('Add gallery', 'final-tiles-grid-gallery-lite')?></a>
						<a href="#" class="no-thank-you"><?php _e('No, thank you', 'final-tiles-grid-gallery-lite') ?></a>
					</div>
				</li>
			</ul>
			<?php endif ?>
			<ul class="collapsible gallery-actions">
				<li class="active">
					<div class="collapsible-header">
					<?php _e('Galleries', 'final-tiles-grid-gallery-lite')?>
					</div>
					<div class="collapsible-body">
						<a href="?page=ftg-add-gallery" class="button components-button is-primary"><?php _e('Add gallery', 'final-tiles-grid-gallery-lite')?></a>
					</div>
				</li>
			</ul>			
		</div>

	</div>
	<?php endif ?>	
</div>

<!-- Delete gallery modal -->
<div id="delete-gallery-modal" class="modal">
	<div class="modal-content">
	  <h4><?php _e('Confirmation', 'final-tiles-grid-gallery-lite')?></h4>
	  <p><?php _e('Do you really want to delete the gallery', 'final-tiles-grid-gallery-lite')?> <span></span> ?</p>
	</div>
	<div class="modal-footer">
	  <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat yes"><?php _e('Yes', 'final-tiles-grid-gallery-lite')?></a>
	  <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat"><?php _e('No', 'final-tiles-grid-gallery-lite')?></a>
	</div>
</div>
<!-- Shortcode gallery modal -->
<div id="shortcode-gallery-modal" class="modal">
	<div class="modal-content">
	  <h4></h4>
	  <p><?php _e('Copy and paste the following shortcode inside a post, page or widget:', 'final-tiles-grid-gallery-lite')?></p>
	  <code></code>
	</div>
	<div class="modal-footer">
	  <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat"><?php _e('Close', 'final-tiles-grid-gallery-lite')?></a>
	</div>
</div>

<div class="preloader-wrapper big active" id="spinner">
    <div class="spinner-layer spinner-blue-only">
      <div class="circle-clipper left">
        <div class="circle"></div>
      </div><div class="gap-patch">
        <div class="circle"></div>
      </div><div class="circle-clipper right">
        <div class="circle"></div>
      </div>
    </div>
  </div>
<script>
	(function ($){
		var galleryId;
		var galleryName;

		$("[data-gid]").click(function () {
			galleryId = $(this).data("gid");
		});

		$(".delete-gallery").click(function (e) {
	        e.preventDefault();	        
	        
	        galleryName = $(this).parents(".data").find(".card-title").text();
	        $("#delete-gallery-modal span").text(galleryName);
        });
        $(".clone-gallery").click(function (e) {
	        e.preventDefault();	        
	        var id = $(this).data("gid");
	        FTG.show_loading();
            $.ajax({
                url: ajaxurl,
                data: {
	                action: 'clone_gallery',
	                id: id,
                  FinalTiles_gallery: $('#FinalTiles_gallery').val()
                },
                dataType: "json",
                type: "post",
                error: function(a, b, c) {
                    FTG.hide_loading();
                },
                success: function(r) {
									location.href = "?page=ftg-lite-gallery-admin";
                }
            });
        });
        $(".show-shortcode").click(function(e) {
	        e.preventDefault();
	        
	        var id = $(this).data("gid");
	        var name = $(this).parents(".data").find(".card-title").text();
	        $("#shortcode-gallery-modal h4").text(name);
	        $("#shortcode-gallery-modal code").text("[FinalTilesGallery id='"+id+"']");
	        $("#shortcode-gallery-modal").openModal();	   
        });
        $("body").on("click", "#delete-gallery-modal .yes", function () {
	        FTG.show_loading();
            $.ajax({
                url: ajaxurl,
                data: {
	                action: 'delete_gallery',
	                id: galleryId,
                    FinalTiles_gallery: $('#FinalTiles_gallery').val()
                },
                dataType: "json",
                type: "post",
                error: function(a, b, c) {
                    console.log(a, b, c);
                    FTG.hide_loading();
                },
                success: function(r) {
	                $("#gallery-" + galleryId).remove();
	                FTG.hide_loading();
                }
            });
        });
	})(jQuery);
</script>