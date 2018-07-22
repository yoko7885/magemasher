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
<script src="./js/fields.js?ver={$smarty.const.VERSION}"></script>
