<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'user-login';
include 'view/prologue.html.php';
include 'view/header.html.php';

$error = $this['login_error'];
$errors = $this['errors'];
extract($_POST);
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $("#register_accept").click(function (event) {
        if (this.checked) {
            $("#register_continue").removeClass('disabled').addClass('aqua');
            $("#register_continue").removeAttr('disabled');
        } else {
            $("#register_continue").removeClass('aqua').addClass('disabled');
            $("#register_continue").attr('disabled', 'disabled');
        }
    });
});
</script>

<div id="sub-header">
	<div class="clearfix">
		<div>
			<h2><?php echo Text::html('login-banner-header'); ?></h2>
		</div>
		<div class="mod-pojctopen"><?php echo Text::html('open-banner-header'); ?></div>
	</div>
</div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>


    <div id="main">
        <div class="login">
            <div>
                <h2><?php echo Text::get('login-access-header'); ?></h2>
                <?php if (!empty($error)): ?>
                <p class="error"><?php echo Text::get('login-fail'); ?></p>
                <?php endif ?>

                <form action="/user/login" method="post">
                    <input type="hidden" name="return" value="<?php echo $_GET['return']; ?>" />
                    <div class="username">
                        <label><?php echo Text::get('login-access-username-field'); ?>
                        <input type="text" name="username" value="<?php echo $username?>" /></label>
                    </div>

                    <div class="password">
                        <label><?php echo Text::get('login-access-password-field'); ?>
                        <input type="password" name="password" value="<?php echo $username?>" /></label>
                    </div>

                    <input type="submit" name="login" value="<?php echo Text::get('login-access-button'); ?>" />

                </form>

                <a href="/user/recover"><?php echo Text::get('login-recover-link'); ?></a>

            </div>
        </div>

        <div class="external-login">
            <div>
            <h2><?php echo Text::get('login-oneclick-header'); ?></h2>
            </div>
        </div>

        <div class="register">
            <div>
                <h2><?php echo Text::get('login-register-header'); ?></h2>
                <form action="/user/register" method="post">

                    <div class="username">
                        <label for="RegisterUsername"><?php echo Text::get('login-register-username-field'); ?></label>
                        <input type="text" id="RegisterUsername" name="username" value="<?php echo htmlspecialchars($username) ?>"/>
                    </div>

                    <?php if(isset($errors['username'])) { ?><p><em><?php echo $errors['username']?></em></p><?php } ?>

                    <div class="email">
                        <label for="RegisterEmail"><?php echo Text::get('login-register-email-field'); ?></label>
                        <input type="text" id="RegisterEmail" name="email" value="<?php echo htmlspecialchars($email) ?>"/>
                    </div>

                    <div class="remail">
                        <label for="RegisterREmail"><?php echo Text::get('login-register-confirm-field'); ?></label>
                        <input type="text" id="RegisterREmail" name="remail" value="<?php echo htmlspecialchars($remail) ?>"/>
                    </div>

                    <?php if(isset($errors['email'])) { ?><p><em><?php echo $errors['email']?></em></p><?php } ?>

                    <div class="password">
                        <label for="RegisterPassword"><?php echo Text::get('login-register-password-field'); ?></label>
                        <input type="password" id="RegisterPassword" name="password" value="<?php echo htmlspecialchars($password) ?>"/>
                    </div>

                     <div class="rpassword">
                        <label for="RegisterRPassword"><?php echo Text::get('login-register-confirm_password-field'); ?></label>
                        <input type="password" id="RegisterRPassword" name="rpassword" value="<?php echo htmlspecialchars($rpassword) ?>"/>
                    </div>

                    <?php if(isset($errors['password'])) { ?><p><em><?php echo $errors['password']?></em></p><?php } ?>

                    <input class="checkbox" id="register_accept" name="confirm" type="checkbox" value="true" />
                    <label class="conditions" for="register_accept"><?php echo Text::html('login-register-conditions'); ?></label><br />

                    <button class="disabled" disabled="disabled" id="register_continue" name="register" type="submit" value="register"><?php echo Text::get('login-register-button'); ?></button>

            </form>
            </div>
        </div>

    </div>

<?php include 'view/footer.html.php' ?>