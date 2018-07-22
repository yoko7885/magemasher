/* global $ bootbox MageMasher */
var bag = MageMasher.Bag =
{
	load : function()
	{
		$('a.update_bag').click(bag.update_bag);
		bag.update_bag();
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
		    	var base_panel = $("<div/>").addClass("bag_item_div");
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
	    	$(MageMasher).trigger('bag.update.complete');
	    });
	}
};

$(function()
{
    bag.load();
});
