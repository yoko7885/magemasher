<!DOCTYPE html>
<html lang="en">
{include file='../view/header.tpl'}
<link href="./css/top.css?ver={$smarty.const.VERSION}" rel="stylesheet">
<!-- NAVBAR
================================================== -->
  <body>
    <div id="loading_small" style="display: none;"><img src="./materials/load.gif"></div>
    <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="0">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        {foreach from=$fields key=k item=v}
        <li data-target="#myCarousel" data-slide-to="{$k}" class="active"></li>
        {/foreach}
      </ol>
      <div class="carousel-inner" role="listbox">
        {foreach from=$fields key=key item=val}
        <div class="item{if $key==0} active{/if}">
          <img class="" src="{$val.field_image}" alt="{$val.field_name}">
          <div class="container">
            <div class="carousel-caption">
              <h1>{$val.field_name}</h1>
              <p>{$val.field_comment}</p>
              <p><a class="btn btn-lg btn-primary do_search" field_id="{$val.field_id}" role="button">探索</a></p>
            </div>
          </div>
        </div>
        {/foreach}
      </div>
      <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div><!-- /.carousel -->

    {**
    <div class="masthead">
      <nav>
        <ul class="nav nav-justified">
          <li class="active"><a href="#">Home</a></li>
          <li><a href="#">Projects</a></li>
          <li><a href="#">Services</a></li>
          <li><a href="#">Downloads</a></li>
          <li><a href="#">About</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
      </nav>
    </div>
    **}

    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    <div class="container marketing">


      <div class="row featurette">
        <div class="col-md-4">
          <!--<div class="col-lg-4">-->
          <!--  <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">-->
          <!--  <h2>Heading</h2>-->
          <!--</div>-->
          <h2 class="featurette-heading" style="margin-top:30px;">アイテムの加工 <span class="text-muted">It'll blow your mind.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
        <div class="col-md-3" style="margin-top:50px;">
          {section name=j start=1 loop=8}
          <div class="composit-working-area">
            {section name=i start=1 loop=8}
              <div id="composit-item-{$smarty.section.j.index}-{$smarty.section.i.index}"></div>
            {/section}
          </div>
          {/section}
        </div>
        <div class="col-md-5">
          <div class="featurette-well center-block well form-horizontal">
              <div class="form-group">
                <label class="col-sm-4">Name</label>
                <div class="col-sm-8">{$user.name}</div>
              </div>
              <div class="form-group">
                <label class="col-sm-4">Sex</label>
                <div class="col-sm-8">{$user.sex}</div>
              </div>
              <div class="form-group">
                <label class="col-sm-4">Birthday</label>
                <div class="col-sm-8">{$user.birthday}</div>
              </div>
              <div class="form-group">
                <label class="col-sm-4">Level</label>
                <div class="col-sm-8">{$user.level}</div>
              </div>
              <div class="form-group">
                <label class="col-sm-4">Search Pt</label>
                <div class="col-sm-8">
                  <label id="sch_now_pt">{$user.now_pt}</label> pt / 
                  <label id="sch_max_pt">{$user.sch_pt}</label> pt</div>
              </div>
              <div class="form-group">
                <label class="col-sm-4">Attack</label>
                <div class="col-sm-8">{$user.atk_pt} pt</div>
              </div>
              <div class="form-group">
                <label class="col-sm-4">Deffence</label>
                <div class="col-sm-8">{$user.def_pt} pt</div>
              </div>
              <div class="form-group">
                <label class="col-sm-4">Remark</label>
                <div class="col-sm-8">{$user.remark}</div>
              </div>
          </div>
        </div>
      </div>
      <p><a class="btn btn-lg btn-primary update_bag" role="button">更新</a></p>
      <div class="row" id="bag"></div>

      <!-- /END THE FEATURETTES -->

      <!-- FOOTER -->
      <footer>
        <p>&copy; 2014 Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
      </footer>

    </div><!-- /.container -->
    {include file='../view/footer.tpl'}
    <script src="./js/top.js?ver={$smarty.const.VERSION}"></script>
  </body>
</html>
