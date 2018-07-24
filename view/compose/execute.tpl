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
          <h2 class="featurette-heading" style="margin-top:30px;">加工の開始 <span class="text-muted">It'll blow your mind.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
        <div class="col-md-5">
        {include file='../../view/userinfo.tpl'}
        </div>
      </div>
      <div>
        {foreach from=$lists item=list}
        {$list.name}
        {/foreach}
      </div>
      <form id="form_compose" method="post">
        {include file='../../view/params.tpl'}
        <input type="hidden" name="targets" value="{$targets}" />
        <p><a id="btn_compose_bag" class="btn btn-lg btn-primary compose_bag disabled" role="button">加工開始</a></p>
      </form>
      {include file='../../view/footer.tpl'}
    </div>
    <script src="./js/compose.js?ver={$smarty.const.VERSION}"></script>
  </body>
</html>
