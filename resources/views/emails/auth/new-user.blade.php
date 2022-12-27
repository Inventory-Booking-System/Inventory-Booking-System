<html>
    <body style="background-color:#E9E9E9">
        <center>
            <table width="600" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px">
                <tbody>
                    <tr>
                        <td valign="middle" style="text-align:center; height:50px; color:#FFF; background-color:#343a40; padding-left:20px; font-size:28px; font-weight:bold; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif">Account Created</td>
                    </tr>
                    <tr>
                        <td valign="top" style="color:#444; background-color:#FFF; padding:20px; font-size:14px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif">An account has been setup for you on the Inventory Booking System.</b><br><br>
                            <b>Email</b><br>{{ $user->email }}<br><br>
                            <b>Password</b><br>{{ $password }}<br><br>
                            <center>You will be prompted to change this password during first login</center>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" style="color:#999; padding:20px; text-align:center; font-size:12px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif"><p>{{ env('APP_NAME') }}</p></td>
                    </tr>
                </tbody>
            </table>
        </center>
    </body>
</html>