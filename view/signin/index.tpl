<!DOCTYPE html>
<html lang="en">
  {include file="../../view/header.tpl"}
  <link href="./css/signin.css?ver={$smarty.const.VERSION}" rel="stylesheet">
  <body>

    <div class="container">

      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" name="inputEmail" class="form-control" value="{$inputEmail}" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="inputPassword" class="form-control" value="{$inputPassword}" placeholder="Password" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <input type="hidden" name="as" value="{$as}">
        <input type="hidden" name="do" value="{$do}">
      </form>
      
      {if $error != ""}
      <div class="alert alert-danger" role="alert">{$error}</div>
      {/if}

    </div> <!-- /container -->
    {include file='../../view/footer.tpl'}
  </body>
</html>
