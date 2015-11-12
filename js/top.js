$(function()
{
	$('#do_search').click(function()
	{
	    var request_params = {};
	    request_params.as = 'dYHtB9ANEcg%3D';
	    request_params.do = 'Qh36TRW6HtA%3D';
	
	    /*---------------------------------*
	     * JSONP通信開始
	     *---------------------------------*/
	    $.getJSONP(request_params, function(jsonp)
	    {
	    	if (jsonp.result == '1')
	    	{
	    		bootbox.alert("通信OK！");
	    	}
	    });
		
	});
});
