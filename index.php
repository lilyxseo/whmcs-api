<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Domain and Hosting</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Manage Domain and Hosting</h1>

        <h2 class="mt-5">Add New Domain</h2>
        <form id="addDomainForm">
            <div class="form-group">
                <label for="domain">Domain Name:</label>
                <input type="text" class="form-control" id="domain" name="domain" required>
            </div>
            <div class="form-group">
                <label for="registrar">Registrar:</label>
                <input type="text" class="form-control" id="registrar" name="registrar" required>
            </div>
            <div class="form-group">
                <label for="regperiod">Registration Period (years):</label>
                <input type="number" class="form-control" id="regperiod" name="regperiod" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Domain</button>
        </form>

        <h2 class="mt-5">Add New Hosting</h2>
        <form id="addHostingForm">
            <div class="form-group">
                <label for="domain">Domain Name:</label>
                <input type="text" class="form-control" id="hosting_domain" name="domain" required>
            </div>
            <div class="form-group">
                <label for="package">Package Name:</label>
                <input type="text" class="form-control" id="package" name="package" required>
            </div>
            <div class="form-group">
                <label for="billingcycle">Billing Cycle:</label>
                <select class="form-control" id="billingcycle" name="billingcycle">
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="semiannually">Semi-Annually</option>
                    <option value="annually">Annually</option>
                    <option value="biennially">Biennially</option>
                    <option value="triennially">Triennially</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Hosting</button>
        </form>

        <h2 class="mt-5">Domain List</h2>
        <div id="domainList"></div>

        <h2 class="mt-5">Hosting List</h2>
        <div id="hostingList"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load domain and hosting list
            loadDomainList();
            loadHostingList();

            // Add new domain form submission
            $('#addDomainForm').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'backend.php',
                    type: 'POST',
                    data: {
                        action: 'addDomain',
                        domain: $('#domain').val(),
                        registrar: $('#registrar').val(),
                        regperiod: $('#regperiod').val()
                    },
                    success: function(response) {
                        alert('Domain added successfully!');
                        loadDomainList();
                        $('#addDomainForm')[0].reset();
                    },
                    error: function() {
                        alert('Failed to add domain.');
                    }
                });
            });

            // Add new hosting form submission
            $('#addHostingForm').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'backend.php',
                    type: 'POST',
                    data: {
                        action: 'addHosting',
                        domain: $('#hosting_domain').val(),
                        package: $('#package').val(),
                        billingcycle: $('#billingcycle').val()
                    },
                    success: function(response) {
                        alert('Hosting added successfully!');
                        loadHostingList();
                        $('#addHostingForm')[0].reset();
                    },
                    error: function() {
                        alert('Failed to add hosting.');
                    }
                });
            });

            function loadDomainList() {
                $.ajax({
                    url: 'backend.php',
                    type: 'POST',
                    data: {
                        action: 'getDomains'
                    },
                    success: function(response) {
                        $('#domainList').html(response);
                    },
                    error: function() {
                        alert('Failed to load domain list.');
                    }
                });
            }

            function loadHostingList() {
                $.ajax({
                    url: 'backend.php',
                    type: 'POST',
                    data: {
                        action: 'getHostings'
                    },
                    success: function(response) {
                        $('#hostingList').html(response);
                    },
                    error: function() {
                        alert('Failed to load hosting list.');
                    }
                });
            }
        });
    </script>
</body>
</html>
