<?php
// no direct access
defined('JPATH_PLATFORM') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$textBeforeItemCount 	= $this->menuParms->get('text_before_item_count', $this->params->get('text_before_item_count'));
$textAfterItemCount 	= $this->menuParms->get('text_after_item_count', $this->params->get('text_after_item_count'));
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
<div class="f2csearchresults<?php echo $pageClassSuffix;?>">

	<?php if ($showPageHeading) : ?>
	<div class="page-header">
		<h1><?php echo $pageHeader; ?></h1>
	</div>
	<?php endif; ?>
	
	<?php if ($this->params->get('page_subheading')) : ?>
	<h2><?php echo $this->escape($this->params->get('page_subheading')); ?></h2>
	<?php endif; ?>

	<?php if($description) : ?>
		<div class="f2csearch-fal-desc"><?php echo $description; ?></div>
	<?php endif; ?>

	<div class="container">
		<?php if($this->params->get('show_item_count')) :?>
		<div id="f2c_item_count" class="left">
			<?php echo $this->escape($textBeforeItemCount . $this->params->get('item_count') . $textAfterItemCount); ?>
		</div>
		<?php endif; ?>
		<?php if($this->params->get('show_ordering')) { echo $this->orderingList; } ?>
	</div>	
	
	<?php $leadingcount = 0; ?>
	<?php if (!empty($this->lead_items)) : ?>
	<div class="items-leading">
		<?php foreach ($this->lead_items as &$item) : ?>
			<div class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
				<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
				?>
			</div>
			<div class="clearfix"></div>
			<?php $leadingcount++; ?>
		<?php endforeach; ?>
	</div><!-- end items-leading -->
	<div class="clearfix"></div>
	<?php endif; ?>

	<?php
		$introcount=(count($this->intro_items));
		$counter=0;
	?>
	<?php if (!empty($this->intro_items)) : ?>
	
		<?php foreach ($this->intro_items as $key => &$item) : ?>
		<?php
			$key= ($key-$leadingcount)+1;
			$rowcount=( ((int)$key-1) %	(int) $this->columns) +1;
			$row = $counter / $this->columns ;
	
			if ($rowcount==1) : ?>
			<div class="items-row cols-<?php echo (int) $this->columns;?> <?php echo 'row-'.$row ; ?> row-fluid">
			<?php endif; ?>
				<div class="span<?php echo round((12 / $this->columns));?>">
					<div class="item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
						<?php
							$this->item = &$item;
							echo $this->loadTemplate('item');
						?>
					</div><!-- end item -->
					<?php $counter++; ?>
				</div><!-- end spann -->
			<?php if (($rowcount == $this->columns) or ($counter ==$introcount)): ?>
			</div><!-- end row -->
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if (!empty($this->link_items)) : ?>
	<div class="items-more">
	<?php echo $this->loadTemplate('links'); ?>
	</div>
	<?php endif; ?>

	<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php  if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="counter pull-right"><?php echo $this->pagination->getPagesCounter(); ?></p>
			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php  endif; ?>
</div>
</form>