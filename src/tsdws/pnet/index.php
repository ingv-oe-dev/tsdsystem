<?php
    // Set GUEST user permissions
    session_start();
    $_SESSION["guest_permissions"] = array(
        "resources" => array (
            "nets" => array(
                "read" => array(
                    "enabled" => true
                )
            ),
            "stations" => array(
                "read" => array(
                    "enabled" => true
                )
            ),
            "sensortypes" => array(
                "read" => array(
                    "enabled" => true
                )
            ),
            "sensors" => array(
                "read" => array(
                    "enabled" => true
                )
            ),
            "sites" => array(
                "read" => array(
                    "enabled" => true
                )
            ),
            "channels" => array(
                "read" => array(
                    "enabled" => true
                )
            ),
            "digitizers" => array(
                "read" => array(
                    "enabled" => true
                )
            ),
            "digitizertypes" => array(
                "read" => array(
                    "enabled" => true
                )
            )
        )
    );
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>PNet Map</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- Vuejs 3 -->
    <script src="js/vue.global.js"></script>
    <!-- Leaflet -->
    <link rel="stylesheet" href="css/leaflet.css"/>
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="js/leaflet.js"></script>
    <!-- Add icon library -->
    <link rel="stylesheet" href="css/fontawesome-all.min.css">
    <!-- Custom style -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/media.css">
    <link rel="stylesheet" href="css/map.css">
    <!-- Custom scripts-->
    <script src="js/colors.js"></script>
    <script src="js/mapComponent.js"></script>
    <script src="js/notificationListComponent.js"></script>
</head>

<body class="m-0">
    <div id="app">
        <img class='m-1' src="img/logo.png" style="height:40px" />
        <div class="btn-group ms-0" role="group" aria-label="Basic checkbox toggle button group">
            <a class="btn btn-rounded text-light ps-0 pe-3" href=".">PNet <span v-if="!isSmallWidth" class="text-info fst-italic">manager</span></a>
            <button class="btn btn-rounded" @click="openSettings(true)" title="App settings"><i class="text-light fa-solid fa-gear"></i></button>
            <?php
                //session_start();
                $user_link_icon = isset($_SESSION ['email']) ? '<button class="btn btn-rounded" title="'.$_SESSION ['email'].'"><i class="text-info fa-solid fa-user"></i> <small v-if="!isSmallWidth" class="text-info" style="font-size:0.7em">'.$_SESSION ['email'].'</small></button>' : '<a class="btn btn-rounded" href="../login" role="button" title="No user identified. Please login"><i class="text-danger fa-solid fa-user-slash"></i></a>';
                echo $user_link_icon;
            ?>
        </div>
        <div class='nav'>
            <div class="btn-group btn-group-sm p-1" role="group" aria-label="Basic checkbox toggle button group">
                <button :class="{active: isActive['N']}" class="btn btn-outline-light " @click="navBtnClick('N')">
                    <i class="fa fa-bell"></i> <span v-if="!isSmallWidth">Messages</span>
                    <span class="ms-1 me-1" v-if="notificationSize && notifyAllMessages" title="Unread info messages">
                        <span class="badge bg-secondary"><i class="fa fa-comment"></i> {{notificationSize}}</span>
                    </span>
                    <span class="ms-1 me-1" v-if="alertSize" title="Unread alert messages">
                        <span class="badge bg-danger"><i class="fa fa-triangle-exclamation"></i> {{alertSize}}</span>
                    </span>
                </button>
                <button :class="{active: isActive['L']}" class="btn btn-outline-light " @click="navBtnClick('L')"><i class="fa fa-list"></i> <span v-if="!isSmallWidth">List</span></button>
                <button :class="{active: isActive['R']}" class="btn btn-outline-light " @click="navBtnClick('R')"><i class="fa-solid fa-table-columns"></i> <span v-if="!isSmallWidth">Panel</span></button>        
            </div>
            <div class="btn-group btn-group-sm p-2" role="group" aria-label="Basic checkbox toggle button group">
                <a class="btn btn-rounded btn-outline-danger" href=".." title="Return to TSDSystem main page"><i class="fa-solid fa-right-from-bracket"></i></a>    
            </div>
        </div>
        <div id='sideL' class="p-0 overflow-auto"></div>
        <leafmap id='map' ref="leafmap" @clicked-marker="onMapMarkerClick" @loading-from-map="onMapLoadingData"></leafmap>
        <div id='sideR' class="p-0 overflow-auto">Sidebar Right</div>
        <div id='sideN' class="p-0 overflow-auto bg-dark">
            <notifications ref="notifications"></notifications>
        </div>
        <div aria-live="polite" aria-atomic="true" class="position-relative" style="z-index:1002">
            <!-- Position it: -->
            <!-- - `.toast-container` for spacing between toasts -->
            <!-- - `.position-absolute`, `top-0` & `end-0` to position the toasts in the upper right corner -->
            <!-- - `.p-3` to prevent the toasts from sticking to the edge of the container  -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3">

                <!-- Then put toasts within -->
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" :data-bs-delay="toastDelay">
                    <div class="toast-header">
                        <img src="" class="rounded me-2" alt="">
                        <strong class="me-auto">{{lastNotify.messageText}}</strong>
                        <small class="text-muted">{{(new Date()).toUTCString()}}</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <div style="font-size:0.9em" class='font-monospace overflow:auto' :class="'text-'+lastNotify.messageType"> <i v-if="lastNotify.messageType=='danger'" class="fa fa-triangle-exclamation"></i> <i v-if="lastNotify.messageType=='warning'" class="fa fa-circle-exclamation"></i> <i v-if="lastNotify.messageType=='success'" class="fa fa-circle-check"></i>                            <i v-if="lastNotify.messageType=='info'" class="fa fa-circle-info"></i> <span class="fst-italic">{{lastNotify.statusText}}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="js/index.js"></script>


</html>