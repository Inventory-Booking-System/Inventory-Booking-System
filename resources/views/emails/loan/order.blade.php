<html>
    <body style="background-color:#E9E9E9">
        <center>
            <table width="600" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px">
                <tbody>
                    <tr>
                        <td valign="middle" style="text-align:center; height:50px; color:#FFF; background-color:#343a40; padding-left:20px; font-size:28px; font-weight:bold; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif">{{ $bookingTitle }} #{{ $loan->id }} {{ ucfirst($bookingType) }}</td>
                    </tr>
                    <tr>
                        <td valign="top" style="color:#444; background-color:#FFF; padding:20px; font-size:14px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif">The following {{ lcfirst($bookingTitle) }} has been {{ $bookingType }}</b><br><br>
                            <b>Start Date</b><br>{{ $loan->start_date_time }}<br><br>
                            <b>End Date</b><br>{{ $loan->end_date_time }}<br><br>
                            <b>Resources</b><br>
                            @foreach ($loan->assets as $asset)
                                @if($asset->pivot->returned)
                                    <s>{{ $asset->name }} ({{ $asset->tag }})</s><br>
                                @else
                                    {{ $asset->name }} ({{ $asset->tag }})<br>
                                @endif
                            @endforeach
                            <br><br>
                            <b>Additional Details</b><br>{{ $loan->details }}<br><br>
                            <center>If you have any queries about this {{ lcfirst($bookingTitle) }}, contact us at <a href="mailto:{{ Config::get('mail.reply_to.address') }}">{{ Config::get('mail.reply_to.address') }}</a></center>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" style="color:#999; padding:20px; text-align:center; font-size:12px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif"><p>{{ Config::get('app.name') }}</p></td>
                    </tr>
                </tbody>
            </table>
        </center>
    </body>
</html>