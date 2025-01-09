<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
        setlocale(LC_ALL, 'sl_SI.UTF-8');
        use Carbon\Carbon;
        $startDate = Carbon::createFromDate($year, $month, 1);
        @endphp

        <title>Paycheck DH for {{ $startDate->format('F Y') }} </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            body {
                font-family: 'figtree', sans-serif;
            }
        </style>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <style>
            /* Light mode styles */
            body {
                background-color: #fff;
                color: #333;
            }

            /* Dark mode styles */
            .dark body {
                background-color: #333;
                color: lightgrey;
            }

            .dark body .navbar {

                background-color:rgb(104, 104, 104) !important;
                color: #fff !important;
            }

            .dark body .navbar-brand {
                background-color:rgb(104, 104, 104) !important;
                color: #fff !important;
            }

            .portlet-body {
                padding-left: 20px;
            }
            </style>
    </head>
    <body class="antialiased">

        <form method="GET" action="{{ route('placa.index') }}">
            <header class="pl-20">
                <nav class="navbar navbar-expand-lg navbar-light bg-light pl-5">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="#">Paycheck DH</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item" style="padding-left: 20px;">
                                    <select class="form-control pl-20" id="yearDropdown" name="year">
                                        <option value="" disabled selected>Year</option>
                                        @php
                                            $currentYear = date('Y');
                                        @endphp
                                        @foreach (range($currentYear - 2, $currentYear + 2) as $y)
                                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </li>
                                <li class="nav-item" style="padding-left: 20px;">
                                    <select class="form-control" id="monthDropdown" name="month" >
                                        <option value="" disabled selected>Month</option>
                                        @foreach ([
                                            1 => 'January',
                                            2 => 'February',
                                            3 => 'March',
                                            4 => 'April',
                                            5 => 'May',
                                            6 => 'June',
                                            7 => 'July',
                                            8 => 'August',
                                            9 => 'September',
                                            10 => 'October',
                                            11 => 'November',
                                            12 => 'December',
                                        ] as $number => $name)
                                            <option value="{{ $number }}" {{ $number == $month ? ' selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </li>
                                <li class="nav-item" style="padding-left: 20px;">
                                    <input type="submit" value="Submit" class="btn btn-primary">
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
        </form>

        <div class="portlet-body">

            <br>
            <p><span style="color: deepskyblue;"><b>PAYCHECK: {{ $payday->format('l, d.m.Y')  }} </b></span><p>

            In {{ $startDate->format('F Y') }} There are {{ $workdaysCount }} workdays and {{ $holidaysMonthCount }} holidays<br><br>
            <b>List of days in {{ $startDate->format('F Y') }}</b><br>

            @foreach ($days as $date => $type)
                @php
                $day = Carbon::createFromDate($date);

                $bold = true;
                $label = 'Workday';
                if ($type == 'F') {
                    $color = 'red';
                    $label = 'Weekend';
                } elseif ($type == 'P') {
                    $color = 'deepskyblue';
                    $label = 'Payday';
                } elseif ($type == 'H') {
                    $color = 'limegreen';
                    $label = 'Holiday';
                } else {
                    $color = 'lightgrey';
                    $bold = false;
                }
                @endphp

                <span style="color: {{ $color }};">
                    {{ $label }}:
                    @if ($bold)
                        <b>{{ $day->format('l, d.m.Y') }}</b>
                    @else
                        {{ $day->format('l, d.m.Y') }}
                    @endif
                </span>
                <br>
            @endforeach

            </br><b>Work free holidays in {{ $year }}</b><br>
            <?php
            if (count($holidays) > 0) {
                foreach ($holidays as $holiday) {
                    echo Carbon::createFromDate($holiday)->format('l, d.m.Y'). "<br>";
                }
            }
            ?>

            <?php
            ?>

        </div>
    </body>
</html>
