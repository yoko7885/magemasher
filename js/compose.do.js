/* global $ bootbox MageMasher */
var compose = MageMasher.Compose =
{
	load : function()
	{
		$("#btn_compose_do").click(compose.compose_click);
	}
	
	, compose_click : function()
	{
		$("input[name='do']").val("2Ml2JHvxbmw%3D");
		$("#form_compose").submit();
	}
};

$(function()
{
    compose.load();
});
