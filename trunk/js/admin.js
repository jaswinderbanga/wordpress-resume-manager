var resman_templates = new Array();
var resman_group_max;

function resman_addGroup(groupname) {
	var group_list = jQuery('#'+groupname+'-list').attr('value');
	resman_group_max = group_list.split(',').pop();
	resman_group_max++;
	group_list += ',' + resman_group_max;
	jQuery('#'+groupname+'-list').attr('value', group_list);
	
	var html = resman_templates[groupname];
	var htmlDOM = jQuery(html);
	jQuery('input,select,textarea', htmlDOM).each(resman_nameFilter);

	jQuery('#addnew-'+groupname).before(htmlDOM);
}

resman_nameFilter = function () {
	var name = jQuery(this).attr('name');
	jQuery(this).attr('name', name + resman_group_max);
}

function resman_registerGroupTemplate(groupname, template) {
	resman_templates[groupname] = template;
}