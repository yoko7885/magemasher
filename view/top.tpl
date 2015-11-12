<!DOCTYPE html>
<html lang="en">
{include file='../view/header.tpl'}
<link href="./css/top.css?ver={$smarty.const.VERSION}" rel="stylesheet">
<!-- NAVBAR
================================================== -->
  <body>

    <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="0">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="item active">
          <img class="first-slide" src="./materials/field1.jpg" alt="First slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>アイウエオ草原</h1>
              <p>あいうえおかきくけこさしすせそたちつてと。わをんらりるれろ。</p>
              <p><a class="btn btn-lg btn-primary" id="do_search" role="button">探索</a></p>
            </div>
          </div>
        </div>
        <div class="item">
          <img class="second-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Second slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>Another example headline.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Learn more</a></p>
            </div>
          </div>
        </div>
        <div class="item">
          <img class="third-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Third slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>One more for good measure.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
            </div>
          </div>
        </div>
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

    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    <div class="container marketing">


      <div class="row featurette">
        <div class="col-md-7">
          <!--<div class="col-lg-4">-->
          <!--  <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">-->
          <!--  <h2>Heading</h2>-->
          <!--</div>-->
          <h2 class="featurette-heading">First featurette heading. <span class="text-muted">It'll blow your mind.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
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
                <div class="col-sm-8">{$user.now_pt} pt / {$user.sch_pt} pt</div>
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

      <!-- /END THE FEATURETTES -->

      <!-- FOOTER -->
      <footer>
        <p>&copy; 2014 Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
      </footer>

    </div><!-- /.container -->
    {include file='../view/footer.tpl'}
    <script src="./js/top.js"></script>
  </body>
</html>
