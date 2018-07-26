<!DOCTYPE html>
<html lang="en">
{include file='../../view/header.tpl'}
<link href="./css/composit.css?ver={$smarty.const.VERSION}" rel="stylesheet">
  <body>
    <div id="loading_small" style="display: none;"><img src="./materials/load.gif"></div>
    {include file='../../view/menu.tpl'}
    <div class="container marketing">
      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading" style="margin-top:30px;">完了 <span class="text-muted">It'll blow your mind.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
        <div class="col-md-5">
        {include file='../../view/userinfo.tpl'}
        </div>
      </div>
      <form id="form_compose" method="post">
        <div>
          {foreach from=$lists item=list}
          {$list.name}
          {/foreach}
          <div class="well well-small">
          {$compose_items|@var_dump}
          </div>
          
          <div class="bag_item_div">
            <div class="panel panel-default bag_item" style="border-color: #{$compose_items.color_d};">
              <div class="panel-heading" style="background-image: linear-gradient(#{$compose_items.color_l} 0px, #{$compose_items.color_d} 100%);"></div>
              <div class="panel-body">
                <h3 class="panel-title">
                  <input type="text" id="new-item-name" class="form-control input-lg" placeholder="このアイテムの名前を決めてください。">
                  <textarea class="form-control" rows="3" placeholder="説明文を入れてください。"></textarea>
                </h3>
              </div>
            </div>
          </div>
          
          {$compose_rank}
        </div>
        {include file='../../view/params.tpl'}
        <input type="hidden" name="targets" value="{$targets}" />
        <p><a id="btn_compose_do" class="btn btn-lg btn-primary compose_bag disabled" role="button">決定</a></p>
      </form>
      {include file='../../view/footer.tpl'}
    </div>
    <script src="./js/compose.do.js?ver={$smarty.const.VERSION}"></script>
  </body>
</html>
