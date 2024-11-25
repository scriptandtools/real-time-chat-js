<?php
session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Logout</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="logout.scss"> <!-- Use CSS instead of SCSS directly -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script> <!-- AngularJS -->
</head>

<body ng-app="logoutApp" ng-controller="logoutController">
    <div class="background-photo">
        <div class="jumbotron">
            <div class="container">
                <h1>See you soon!</h1>
            </div>
        </div>
        <div class="middle-block">
            <div ng-if="loadingShowed">
                You will be redirected in {{seconds}} seconds.
            </div>
            <div ng-if="!loadingShowed">
                <a href="login.php">Click here to log in again.</a>
            </div>
            <div class="round-class">
                <i ng-class="{'fa fa-3x': true, 'fa-spinner fa-pulse fa-fw': loadingShowed, 'fa-sign-in': !loadingShowed}"></i>
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="second">
            <div class="container">
                <div class="col-xs-12 col-sm-6">
                    <div class="row">
                        <div class="round-class ball">
                            <i class="fa fa-thumbs-o-up fa-lg" aria-hidden="true"></i>
                        </div>
                        <div class="right-text">
                            Thanks for using our web client. We hope you liked it.
                        </div>
                    </div>
                    <div class="row">
                        <div class="round-class ball">
                            <i class="fa fa-mobile fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="right-text">
                            You can stay in contact with your phone client.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    var myApp = angular.module('logoutApp', []);

    myApp.controller('logoutController', ['$scope', '$interval', '$window', function ($scope, $interval, $window) {
        $scope.seconds = 5; // Countdown duration
        $scope.loadingShowed = true;

        // Countdown timer
        var countdown = $interval(function () {
            $scope.seconds--;
            if ($scope.seconds <= 0) {
                $scope.loadingShowed = false;
                $interval.cancel(countdown);
                $scope.redirect(); // Trigger redirect
            }
        }, 1000);

        // Redirect function
        $scope.redirect = function () {
            $window.location.href = "login.php"; // Redirect to the login page
        };
    }]);
</script>

</html>
