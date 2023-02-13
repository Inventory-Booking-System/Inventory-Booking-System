<html>
    <body style="background-color:#E9E9E9">
        <center>
            <table width="600" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px">
                <tbody>
                    <tr>
                        <td valign="middle" style="text-align:center; height:50px; color:#FFF; background-color:#343a40; padding-left:20px; font-size:28px; font-weight:bold; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif">{{ $bookingTitle }} #{{ $setup->id }} {{ ucfirst($bookingType) }}</td>
                    </tr>
                    <tr>
                        <td valign="top" style="color:#444; background-color:#FFF; padding:20px; font-size:14px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif">The following {{ lcfirst($bookingTitle) }} has been {{ $bookingType }}</b><br><br>
                            <b>Title</b><br>{{ $setup->title }}<br><br>
                            <b>Location</b><br>{{ $setup->location->name }}<br><br>
                            <b>Start Date</b><br>{{ $setup->loan->start_date_time }}<br><br>
                            <b>End Date</b><br>{{ $setup->loan->end_date_time }}<br><br>
                            <b>Resources</b><br>
                            @foreach ($setup->loan->assets as $asset)
                                {{ $asset->name }} ({{ $asset->tag }})<br>
                            @endforeach
                            <br>
                            <b>Additional Details</b><br>{{ $setup->loan->details }}<br><br>
                            <center>If you have any queries about this {{ lcfirst($bookingTitle) }}, contact us at <a href="mailto:{{ Config::get('mail.reply_to.address') }}">{{ Config::get('mail.reply_to.address') }}</a></center>
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