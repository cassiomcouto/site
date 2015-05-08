function ToggleOptimizerConfigForm(skipConfirmMsg) {
	if($('#catenableoptimizer').attr('checked')) {

		var showForm = true; 
		if(!skipConfirmMsg) {
			showForm = confirm(lang.ConfirmEnableCategoryOptimizer);
		}

		if(showForm) {
			$('#OptimizerConfigForm').show();
		} else {
			$('#catenableoptimizer').attr('checked', false)
		}
	} else {
		$('#OptimizerConfigForm').hide();
	}
}



function ShowTab(T)
{
	if(T=='' || $('#div_'+T).length <= 0 || $('#tab_'+T).length <= 0) {
		return false;
	}

	var activeTab = $('#tabnav .active');
	var tabName = activeTab.attr('id').replace('tab_', '');
	activeTab.removeClass('active');
	$('#div_'+tabName).hide();	
	$('#div_'+T).show();
	$('#tab_'+T).addClass('active');
	$('#currentTab').val(T);
}
