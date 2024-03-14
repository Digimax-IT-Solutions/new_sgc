<!-- Navbar -->
<style>
.hmm {
  text-decoration: none; /* Sets the default font color */
}
.hmm:hover {
  text-decoration: none; /* Change this to your desired color */
}
.hmm span:hover {
  text-decoration: none;
  color: #800000;/* Sets the default font color */
}
.user {
    color: white;
    font-size: 25px;
    }
.user:hover {
    text-decoration: none;
}
.user-footer {
    list-style: none; /* Removes the default list style */
    padding: 0; /* Removes any default padding */
    margin: 0; /* Removes any default margin */
    overflow: hidden; /* Ensures that content does not overflow */
}

.user-footer .pull-left {
    float: left; /* Aligns the left content to the left */
}

.user-footer .pull-right {
    float: right; /* Aligns the right content to the right */
}

.user-footer .btn {
    margin: 5px; /* Adds margin around the buttons */
}

.user-footer .btn-success {
    color: #fff; /* Text color */
    background-color: #5cb85c; /* Background color */
    border-color: #4cae4c; /* Border color */
}

.user-footer .btn-success:hover {
    color: #fff; /* Text color on hover */
    background-color: #449d44; /* Background color on hover */
    border-color: #398439; /* Border color on hover */
}

.user-footer .btn-danger {
    color: #fff; /* Text color */
    background-color: #d9534f; /* Background color */
    border-color: #d43f3a; /* Border color */
}

.user-footer .btn-danger:hover {
    color: #fff; /* Text color on hover */
    background-color: #c9302c; /* Background color on hover */
    border-color: #ac2925; /* Border color on hover */
}
</style>
<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top" style="background-color: rgba(128,21,20,0.8);">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" style="color: white;"><i class=" fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
 
        <!-- Add this HTML code where you want to display the clock -->
        <li class="nav-item mr-3">
            <a class="nav-link" style="color: white; font-size: 20px;">
                <i class="ion-calendar"></i>
                <span id="clock-day"></span>, <span id="clock-date"></span> <span id="clock-time"></span>
            </a>
        </li>

        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
            <a href="#" class="hmm" data-toggle="dropdown">
              <!-- <img src="../images/nopic.jpg" width="30px" height="30px"> -->
              <span class="user"><?php echo $_SESSION['SESS_FIRST_NAME'] ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header" style="background: rgb(128, 0, 0)">
              <img src="../images/nopic.jpg" width="30px" height="30px">
    
                <p style="color: white">
                  <?php echo $_SESSION['SESS_FIRST_NAME'] ?>
                  <small style="color: white" >Member since <?php echo $_SESSION['SESS_CREATED_AT'] ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#profile" data-toggle="modal" class="btn btn-success" id="admin_profile">Update</a>
                </div>
                <div class="pull-right">
                  <a href="../router.php?page=logout" class="btn btn-danger">Sign out</a>
                </div>
              </li>
            </ul>
        </li>

        <!-- <li class="nav-item">
            <a class="nav-link" href="../router.php?page=logout" style="color: white; font-size: 18px;">
                <i class="ion-log-out" style="color: red;">Log Out
            </a>
        </li> -->
    </ul>
</nav>
<br><br>
<!-- /.navbar -->
<!--profile modal-->
<!-- Add -->
<div class="modal fade" id="profile">
    <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(128,21,20); color: white;">
                    <h5 class="modal-title" id="updateProfileModalLabel"><b>EDIT PROFILE</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(128,21,20); color: white;">
                    <form id="updateProfileForm"
                        style="background-color: rgb(128,21,20); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="updateProfileID" name="updateProfileID" hidden>
                        <div class="form-group">
                            <label for="updateProfileName">NAME</label>
                            <input type="text" class="form-control" id="updateProfileName" name="updateProfileName" value="<?php echo $_SESSION['SESS_FIRST_NAME'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="updateProfileUsername">USERNAME</label>
                            <input type="text" class="form-control" id="updateProfileUsername" name="updateProfileUsername" value="<?php echo $_SESSION['SESS_FIRST_NAME'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="updateProfilePass">PASSWORD</label>
                            <input type="password" class="form-control" id="updateProfilePass" name="updateProfilePass" placeholder="Must have at least 5 characters" required>
                        </div>
                        <div class="form-group">
                            <label for="updateProfileConpass">CONFIRM PASSWORD</label>
                            <input type="password" class="form-control" id="updateProfileConpass" name="uupdateProfileConpass" placeholder="Confirm password to save changes" required>
                        </div>
                        <div class="form-group">
                            <label for="updateProfileCurpass">CURRENT PASSWORD</label>
                            <input type="password" class="form-control" id="updateProfileCurpass" name="uupdateProfileCurpass" placeholder="Current password to save changes" required>
                        </div>
                        <div class="form-group">
                            <label for="updateProfileImg">IMAGE</label><br>
                            <input type="file" id="updateProfileImg" name="updateProfileImg"
                                required>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveupdateProfileButton">Save Changes</button>
                    </div>
                </div>

            </div>
    </div>
</div>
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