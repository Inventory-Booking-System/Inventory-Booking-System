<html>
    <body style="background-color:#E9E9E9">
        <center>
            <table width="600" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px">
                <tbody>
                    <tr>
                        <td valign="middle" style="text-align:center; height:50px; color:#FFF; background-color:#343a40; padding-left:20px; font-size:28px; font-weight:bold; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif">{{ $bookingTitle }} #{{ $incident->id }} {{ ucfirst($bookingType) }}</td>
                    </tr>
                    <tr>
                        <td valign="top" style="color:#444; background-color:#FFF; padding:20px; font-size:14px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif">The following {{ lcfirst($bookingTitle) }} has been {{ $bookingType }}</b><br><br>
                            <b>Start Date</b><br>{{ $incident->start_date_time }}<br><br>
                            <b>Location</b><br>{{ $incident->location->name }}<br><br>
                            <b>Issues</b><br>
                            @foreach ($incident->issues as $issue)
                                x{{ $issue->pivot->quantity }} {{ $issue->title }} (£{{ ($issue->cost * $issue->pivot->quantity) }})<br>
                            @endforeach
                            <b>Total Cost:</b> £{{ $incident->totalCost }}<br>
                            <br>
                            <b>Evidence</b><br>{{ $incident->evidence }}<br><br>
                            <b>Details</b><br>{{ $incident->details }}<br><br>
                            <center>If you have any queries about this {{ lcfirst($bookingTitle) }}, contact us at <a href="mailto:"></a></center>
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