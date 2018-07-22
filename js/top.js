/* global $ bootbox MageMasher */
var me = MageMasher.Top =
{
	load : function()
	{
		$('a.do_search').click(me.do_search);
		$('a.update_bag').click(me.update_bag);
		$('div.composit-working-area > div').droppable(
		{
			classes:
			{
				// "ui-droppable-hover": "ui-state-hover"
			}
			, over: function( event, ui )
			{
				var composit_panel = $(this);
				composit_panel.css("background-color", "#ddd");
			}
			, out: function( event, ui )
			{
				var composit_panel = $(this);
				composit_panel.css("background-color", "inherit");
			}
			, drop: function( event, ui )
			{
				var composit_panel = $(this);
				var composit_item = $(ui.draggable);
				
				var composit_panel_color = composit_item.children("div.panel-heading").css("background-image"); 
				composit_panel.css("background-image", composit_panel_color);
				
				composit_item.addClass("active");
			}
		});
 
		me.update_bag();
	}
	
	, update_bag : function()
	{
	    var request_params = {};
	    request_params.as = 'dYHtB9ANEcg%3D';
	    request_params.do = 'q3mEJJ8nV0M%3D';

	    /*---------------------------------*
	     * JSONP通信開始
	     *---------------------------------*/
	    $.getJSONP(request_params, function(jsonp)
	    {
	    	var item = jsonp.result.bag;
	    	
	    	$("#bag").empty();
	    	$.each(item, function()
	    	{
		    	var base_panel = $("<div/>").addClass("col-md-2");
		    	var main_panel = $("<div/>").addClass("panel panel-default bag_item");
		    	main_panel.css("border-color", "#"+this.color_d);
		    	var panel_header = $("<div/>").addClass("panel-heading");
		    	panel_header.css("cssText",
                      "background-image: -webkit-linear-gradient(top,#"+this.color_l+" 0,#"+this.color_d+" 100%);"
                    + "background-image: -o-linear-gradient(top,#"+this.color_l+" 0,#"+this.color_d+" 100%);"
                    + "background-image: -webkit-gradient(linear,left top,left bottom,from(#"+this.color_l+"),to(#"+this.color_d+"));"
                    + "background-image: linear-gradient(to bottom,#"+this.color_l+" 0,#"+this.color_d+" 100%);"
                    + "filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff"+this.color_l+"', endColorstr='#ff"+this.color_d+"', GradientType=0);"
                );
		    	var panel_body = $("<div/>").addClass("panel-body");
		    	var panel_title = $("<h3/>").addClass("panel-title").append(this.name);
		    	var panel_check = $("<i/>").addClass("fa fa-check fa-2x text-danger");
		    	
		    	base_panel.append(main_panel.append(panel_header).append(panel_check).append(panel_body.append(panel_title)));
		    	
		    	$("#bag").append(base_panel);
	    	});
	    	$("div.bag_item").click(me.item_click);
	    	$("div.bag_item").draggable({
				containment: 'document',
				cursor: 'move',
				helper: 'original',
				opacity: 0.5,
				revert: true
			});
	    });
	}
	
	, item_click : function()
	{
		// $(this).addClass("active");
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
    me.load();
});
