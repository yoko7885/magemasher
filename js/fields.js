/* global $ bootbox MageMasher */
var fields = MageMasher.Fields =
{
	load : function()
	{
		$('a.do_search').click(fields.do_search);
	}
	
	, do_search : function()
	{
	    var request_params = {};
	    request_params.as = 'dYHtB9ANEcg%3D';
	    request_params.do = 'Qh36TRW6HtA%3D';
	    request_params.field_id = $(this).attr('field_id');
	
	    /*---------------------------------*
	     * JSONP通信開始
	     *---------------------------------*/
	    $.getJSONP(request_params, function(jsonp)
	    {
	    	if (jsonp.result.code == '1')
	    	{
	    		var item = jsonp.result.item;
	    		var now = jsonp.result.now;
	    		
	    		bootbox.alert("<strong>" + item.name + "</strong> を入手しました。");
	    		$("#sch_now_pt").html(now);
	    	}
	    	else if (jsonp.result.code == '2')
	    	{
	    		var message = jsonp.result.message;
	    		bootbox.alert(message);
	    	}
	    });
	}
};

$(function()
{
    fields.load();
});
