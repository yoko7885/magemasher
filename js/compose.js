/* global $ bootbox MageMasher */
var compose = MageMasher.Compose =
{
	load : function()
	{
		$(MageMasher).on('bag.update.complete', function()
		{
			$("div.bag_item").click(compose.item_click);
		});
		$("#btn_compose_bag").click(compose.compose_click);
	}
	
	, item_click : function()
	{
		if (!$(this).hasClass("active"))
		{
			$(this).addClass("active");
		}
		else
		{
			$(this).removeClass("active");
		}
		
		$("a.compose_bag").addClass("disabled");
		if ($("div.bag_item.active").size() > 0) $("a.compose_bag").removeClass("disabled");
	}
	
	, compose_click : function()
	{
		$("#form_compose").submit();
	}
};

$(function()
{
    compose.load();
});
