
<div class="row" >
    <div class="col-lg-6 justify-content-center">
        <div class="row">
            <div class="col-lg">
                <h1>Incident #{{ $incidentId }}</h1>
                <h2>Location: {{ $location_id }}</h2>
                <h2>Distribution: {{ $distribution_id }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <p>{{ $evidence }}</p>
                <p>{{ $details }}</p>
            </div>
        </div>
        <div class="row">
        </div>
        <div class="row">
        </div>
    </div>

    <div class="col-lg-6">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">First</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>