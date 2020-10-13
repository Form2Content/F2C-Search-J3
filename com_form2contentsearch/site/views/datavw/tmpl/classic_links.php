<?php
// no direct access
defined('JPATH_PLATFORM') or die;
?>
<div class="items-more">
<h3><?php echo JText::_('COM_FORM2CONTENTSEARCH_MORE_ARTICLES'); ?></h3>
<ol>
<?php
	foreach ($this->link_items as &$item) :
?>
	<li>
		<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>">
			<?php echo $item->title; ?></a>
	</li>
<?php endforeach; ?>
</ol>
</div>
