<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top" style="background-color: rgb(0, 149, 77);">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" style="color: white;"><i class=" fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
 
        <!-- Add this HTML code where you want to display the clock -->
        <li class="nav-item mr-3">
            <a class="nav-link" style="color: white; font-size: 18px;">
                <i class="ion-calendar"></i>
                <span id="clock-day"></span>, <span id="clock-date"></span> <span id="clock-time"></span>
            </a>
        </li>
        <li class="nav-item mr-3">
            <a class="nav-link" style="color: white; font-size: 18px;">
                <i class="icon-user icon-large"></i> USER:
                <strong><?php echo $_SESSION['SESS_FIRST_NAME'] ?></strong>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../router.php?page=logout" style="color: white; font-size: 18px;">
                <i class="ion-log-out" style="color: red;"></i> Log Out
            </a>
        </li>
    </ul>
</nav>
<br><br>
<!-- /.navbar -->

<!-- Include this script in your HTML file -->
<script>
    function updateClock() {
        var now = new Date();
        var day = now.toLocaleDateString('en-US', {
            weekday: 'long'
        });
        var date = now.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
        var time = now.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: true
        });

        document.getElementById('clock-day').textContent = day;
        document.getElementById('clock-date').textContent = date;
        document.getElementById('clock-time').textContent = time;
    }

    // Update the clock every second
    setInterval(updateClock, 1000);

    // Initial update
    updateClock();
</script>