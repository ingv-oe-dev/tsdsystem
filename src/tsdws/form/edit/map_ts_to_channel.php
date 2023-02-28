<?php
    $channel_id = isset($_GET["channel_id"]) ? $_GET["channel_id"] : null;
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Load jQuery -->
    <script src="../js-download/jquery-3.6.0.min.js"></script>
    <!-- Load Select2 -->
    <link type="text/css" rel="stylesheet" href="../js-download/select2.min.css" />
    <script src="../js-download/select2.min.js"></script>
    <!-- Load required Bootstrap and BootstrapVue CSS -->
    <link type="text/css" rel="stylesheet" href="../js-download/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="../js-download/bootstrap-vue.min.css" />
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "../../timeseries",
                data: {
                    "sort_by": "name"
                },
                success: function(response) {
                    $('#ts-select').select2({
                        placeholder: 'Select an option',
                        data: $.map(response.data, function(item) {
                            return {
                                "id": item.id,
                                "text": item.name
                            }
                        })
                    });
                },
                error: function(jqXHR) {
                    $('#ts-select').select2({
                        placeholder: 'Loading failed'
                    });
                }
            });
            // bind emit signal on change option
            $("#confirm").click(function() {
                emitSignal($('#ts-select').val());
            });
        });
        // dispatch event if loaded from a parent frame
        function emitSignal(selectedVal) {
            try {
                var event = new CustomEvent('mapTS2channel', { 
                    "detail": {
                        "timeseries_id": selectedVal,
                        "channel_id": <?php echo $channel_id ?>
                    }
                });
                console.log(event);
                window.parent.document.dispatchEvent(event)
            } catch (e) {
                console.log(e);
            }
        }
    </script>
</head>

<body>
    <div class="container" id="app">
        <h3 class="mt-3">Select timeseries</h3>
        <div class="input-group input-group-sm mt-3">
            <select id="ts-select" style="width: 50%"></select>
            <button class="btn-primary border" id='confirm'>Confirm</button>
        </div>
    </div>
</body>

</html>