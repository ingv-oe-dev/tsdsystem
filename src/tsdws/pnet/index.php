<!DOCTYPE html>
<html lang="en">

<head>
    <title>PNet Map</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <!-- Vuejs 3 -->
    <script src="https://unpkg.com/vue@3"></script>
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin="" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
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
        <img class='m-1' src="img/logo.png" style="height:40px" />&nbsp;<span style="color:white">PNet <span v-if="!isSmallWidth" class="text-info fst-italic">manager</span></span>
        <div class='nav'>
            <div class="btn-group p-1" role="group" aria-label="Basic checkbox toggle button group">
                <button class="btn rounded" @click="openSettings(true)" title="App settings"><i class="text-light fa-solid fa-gear"></i></button>&nbsp;
                <button :class="{active: isActive['N']}" class="btn btn-outline-light rounded" @click="navBtnClick('N')">
                    <i class="fa fa-bell"></i> <span v-if="!isSmallWidth">Messages</span><span v-if="notificationSize && notifyAllMessages" title="Unread info messages">&nbsp;<span class="badge bg-secondary"><i class="fa fa-comment"></i> {{notificationSize}}</span></span>&nbsp;<span v-if="alertSize" title="Unread alert messages">&nbsp;<span class="badge bg-danger"><i class="fa fa-triangle-exclamation"></i> {{alertSize}}</span></span>
                </button>&nbsp;
                <button :class="{active: isActive['L']}" class="btn btn-outline-light rounded" @click="navBtnClick('L')"><i class="fa fa-list"></i> <span v-if="!isSmallWidth">List</span></button>&nbsp;
                <button :class="{active: isActive['R']}" class="btn btn-outline-light rounded" @click="navBtnClick('R')"><i class="fa-solid fa-table-columns"></i> <span v-if="!isSmallWidth">Panel</span></button>
            </div>
        </div>
        <div id='sideL' class="p-0 overflow-auto"></div>
        <leafmap id='map' ref="leafmap" @clicked-marker="onMapMarkerClick"></leafmap>
        <div id='sideR' class="p-0 overflow-auto">Sidebar Right</div>
        <div id='sideN' class="p-0 overflow-auto bg-dark">
            <notifications ref="notifications"></notifications>
        </div>
        <div aria-live="polite" aria-atomic="true" class="position-relative" style="z-index:100000000">
            <!-- Position it: -->
            <!-- - `.toast-container` for spacing between toasts -->
            <!-- - `.position-absolute`, `top-0` & `end-0` to position the toasts in the upper right corner -->
            <!-- - `.p-3` to prevent the toasts from sticking to the edge of the container  -->
            <div class="toast-container position-absolute top-0 end-0 p-3">

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