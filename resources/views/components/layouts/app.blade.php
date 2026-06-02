<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>

    <x-navbar />

    <div class="container-fluid">
        <div class="row">

            <div class="col-md-2">
                <x-sidebar />
            </div>

            <div class="col-md-10">
                {{ $slot }}
            </div>

        </div>
    </div>

</body>
</html>