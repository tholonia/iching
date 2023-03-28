<?php
/* Edited top work with PHP7 :JWX */
include ($_SERVER['DOCUMENT_ROOT']."/charting/home/constants.php");

$background_color = BACKGROUND_COLOR;

include ($_SERVER['DOCUMENT_ROOT']."/charting/home/header.php");
?>


        
        <div id="content">
            In order to process your astrological information we need you to sign up as a free member. We will keep all your data safe for you. It will also be easy from now on to add other family members or friends in case you want to check them out. Signing up does not entail any obligations on your part other than entering the birth information. We will never sell or disclose any of your personal details to anybody else. Thank you for your interest in my work. I send you my best.
        </div>

        <div id="content">
<!--            <table width="99%" bgcolor="<?php echo $background_color; ?>" cellspacing='0' cellpadding='3' border="0">-->
            <table >
                <tr>
                    <td>
                        <form name='form1' action="process_new.php" method="post" target="_blank">
                            <table cellSpacing='3' cellPadding='0' border='0'>
                                <tr>
                                    <td colspan='3' align='center'>
                                        <h4><center>New to <?php echo YOUR_URL; ?>?<br>Sign up for FREE</center></h4>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='right' colspan='2'>
                                        <span class=pa_textbox>Choose a username:&nbsp;</span>
                                    </td>
                                    <td align='left' colspan='1'>
                                        <input class=pa_textbox maxLength=12 size=17 name=username>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='right' colspan='2'>
                                        <span class=pa_textbox>Choose a password:&nbsp;</span>
                                    </td>
                                    <td align='left' colspan='1'>
                                        <input class=pa_textbox type=password maxLength=16 size=17 name=password1>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='right' colspan='2'>
                                        <span class=pa_textbox>Confirm password:&nbsp;</span>
                                    </td>
                                    <td align='left' colspan='1'>
                                        <input class=pa_textbox type=password maxLength=16 size=17 name=password2>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='right' colspan='2'>
                                        <span class=pa_textbox>E-mail:&nbsp;</span>
                                    </td>
                                    <td align=left colspan='1'>
                                        <input class=pa_textbox maxLength=40 size=46 name=email>
                                    </td>
                                </tr>
                            </table>
                            <br><br><br><input class=pa_button1 type="submit" value="Register me as a new member">
                        </form>
                    </td>

                    <td>&nbsp;</td>

                    <td>
                        <form name="form2" action="login.php" method="post" target="_blank">
                            <table cellSpacing='3' cellPadding='0' border='0'>
                                <tr>
                                    <td colspan='3' align='center'>
                                        <h4><center>Current Member?<br>
                                                Sign in here</center><br></h4>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='right' colspan='2'>
                                        <span class=pa_textbox>Username:&nbsp;</span>
                                    </td>
                                    <td align='left' colspan='1'>
                                        <?php
                                        $username = "";
                                        if (isset($_COOKIE['u_name']))
                                            $username = $_COOKIE['u_name'];
                                        echo "<input class='pa_textbox' type='text' maxLength='12' size='17' name='username' VALUE='$username'>";
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='right' colspan='2'>
                                        <span class=pa_textbox>Password:&nbsp;</span>
                                    </td>
                                    <td align='left' colspan='1'>
                                        <?php
                                        $password = "";
                                        if (isset($_COOKIE['u_pw']))
                                            $password = $_COOKIE['u_pw'];
                                        echo "<input class='pa_textbox' type='password' maxLength='16' size='17' name='password' VALUE='$password'>";
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='center' colspan='3'>
                                        <span class='pa_textbox'><A href="forgotpassword.php"><br><br>Forgot your password?</A></span>
                                    </td>
                                </tr>

                            </table>
                            <br><input class='pa_button2' type=submit value='Log me in'>
                        </form>
                    </td>
                </tr>
            </table>

            <p>&nbsp;</p>

        </div>

    <br><br>

<?php 
include ($_SERVER['DOCUMENT_ROOT']."/charting/home/footer.php");
?>
