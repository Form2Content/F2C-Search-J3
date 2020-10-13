function F2C_SetCustomOrdering(sortfield, direction, sorttype)
{
	var ord = document.getElementById('f2csearch_order_by');
	ord.value = sortfield;
	var dir = document.getElementById('f2csearch_order_dir');
	dir.value = direction;
	var ort = document.getElementById('f2csearch_order_type');
	ort.value = sorttype;
	var frm = document.getElementById('adminForm');
	frm.submit();
}
