<div id="user-login-block-container">
  <div id="user-login-block-form-fields">
TEST
    <?php print $name; // Display username field ?>
    <?php print $pass; // Display Password field ?>
    <?php print $submit; // Display submit button ?>
    <?php print $rendered; // Display hidden elements (required for successful login) ?>
  </div>
  <div class="links">
    <a href="/user/register">Create an Account</a> | <a href="/user/password">Forgot Password</a>
  </div>
</div>