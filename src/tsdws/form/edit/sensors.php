<?php
    $id = isset($_GET["id"]) ? $_GET["id"] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Editor</title>
    <!-- Load required Bootstrap and BootstrapVue CSS -->
    <link type="text/css" rel="stylesheet" href="../js-download/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="../js-download/bootstrap-vue.min.css" />
    <script src="../js-download/jquery-3.6.0.min.js"></script>
    <script src="../js-download/jsoneditor-2.8.0.min.js"></script>
</head>
<body>
    <div class='container-flex'>
        <div class='columns'>
            <div class='column col-md-12' id='editor_holder'></div>
        </div>
        <div class='columns'>
            <div class='column col-md-12'>
                <button id='check' class='btn btn-success'>Validate</button>  
                <button id='restore' class='btn btn-secondary'>Restore to Default</button>
                <div id='valid_indicator' class='mt-1 alert alert-danger'></div>
                <button id='submit' class='btn btn-primary' disabled>Submit</button>  
                <?php 
                    if ($id) {
                        echo "<button id='delete' class='btn btn-danger'>Delete item [id=" . $id . "]</button>";
                    } 
                ?>
            </div>
        </div>
        <div class='columns p-3 mt-0'>
            <div id='server_response' class="alert alert-dismissible fade" role="alert">
            <span class='mymessage'></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="$(this).parent().removeClass('show')">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
        </div>
    </div>
    <script>
        var editor = null;

        // This is the starting value for the editor
        // We will use this to seed the initial editor 
        // and to provide a "Restore to Default" button.
        var default_starting_value = null;
        var id = "<?php echo $id; ?>";
        var ref = "../../json-schemas/sensors.json";
        var route = "../../sensors";
        var method =  id ? "PATCH" : "POST";
        var mySchema = {};

        // Set action on delete button
        $(function(){
            $("button#delete").on("click", function(){
                if (confirm("This action will remove record with id=" + id + ". Continue?") == true) {
                    $.ajax({
                        "url": route + "?id=" + id,
                        "method": "DELETE",
                        "beforeSend": function(jqXHR, settings) {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Remove sensor [id=" + id + "]"}, settings);
                        },
                        "success": function(response, textStatus, jqXHR) {
                            emitSignal(Object.assign(jqXHR, {"messageType":"success"}));
                            alert("Record with id=" + id + " removed successfully!");
                            let new_location = window.location.href.split('?')[0];
                            window.location.href = new_location;
                        },
                        "error": function(jqXHR) {
                            emitSignal(Object.assign(jqXHR, {"messageType":"danger"}));
                            $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                            $('#server_response').addClass("alert-danger show");
                        }
                    });
                }
            });  
        });

        // Load schema and data
        $.ajax({
            "url": ref,
            "success": function(data) {
                mySchema = data;
                handleInputID();
                // get list of sensortypes
                $.ajax({
                    "url": "../../sensortypes",
                    "success": function(response) {
                        fillEnum(response.data, "sensortype_id");
                        // load sensor data if sensor_id is defined
                        if (id) {
                            $.ajax({
                                "url": route,
                                "data": {
                                    "id": id
                                },
                                "success": function(starting_value) {
                                    // set the default starting value (JSON) with data of sensor with selected id 
                                    default_starting_value = starting_value.data[0];
                                    // update schema and start editor
                                    mySchema.properties.sensortype_id.default = starting_value.data[0].sensortype_id;
                                    startEditor();
                                },
                                "error": function(jqXHR) {
                                    $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                                    $('#server_response').addClass("alert-danger show");
                                }
                            });
                        } else {
                            // start editor
                            startEditor();
                        }
                    },
                    "error": function(jqXHR) {
                        $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                        $('#server_response').addClass("alert-danger show");
                    }
                });
            },
            "error": function(jqXHR) {
                $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                $('#server_response').addClass("alert-danger show");
            }
        });

        function handleInputID() {
            if (!id) {
                mySchema.properties.id.options = {
                    "hidden": true
                }
            } else {
                mySchema.required.push("id");
            }
        }

        function resetEditor() {
            // make empty the JSON editor container
            $('#editor_holder').html('');

            // reset to null the variable representing the JSON editor11
            editor = null;
        }

        function startEditor() {

            // reset editor
            resetEditor();

            // initialize editor with the default starting JSON value
            initializeEditor(default_starting_value);
        }

        function initializeEditor(starting_value) {
        
            const container = document.getElementById('editor_holder');

            // Initialize the editor
            editor = new JSONEditor(container,{
                // Enable fetching schemas via ajax
                ajax: true,
            
                // The schema for the editor
                schema: mySchema,
            
                // Seed the form with a starting value
                startval: starting_value,

                // Setting theme
                theme: 'bootstrap4',

                show_opt_in: true
            });

            editor.on('ready',() => {

                // Validate the editor on start
                editor.validate();
            });
            
            // Hook up the submit button to log to the console
            $('#submit').on('click',function() {
                // Get the value from the editor
                console.log(editor.getValue());

                // get JSON to post
                var toPost = editor.getValue();
                //console.log(toPost);
                
                // PATCH if id is indicated, else POST
                $.ajax({
                    "url": route,
                    "data": JSON.stringify(toPost),
                    "method": method,
                    "beforeSend": function(jqXHR, settings) {
                        jqXHR = Object.assign(jqXHR, settings);
                        if (method == 'POST') {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Add sensor"});
                        }
                        if (method == 'PATCH') {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Edit sensor [id=" + id + "]"});
                        }
                    },
                    "success": function(response, textStatus, jqXHR) {
                        //console.log(jqXHR);
                        jqXHR = Object.assign(jqXHR, {"messageType":"success"});
                        if (method == 'POST') {
                            if (jqXHR.status == 207) {
                                jqXHR = Object.assign(jqXHR, {"messageType":"warning"});
                                emitSignal(jqXHR);
                                $('#server_response span.mymessage').html(jqXHR.statusText);
                                $('#server_response').addClass("alert-warning show");
                            } else {
                                emitSignal(jqXHR);
                                let separator = window.location.href.includes('?') ? "&" : "?";
                                window.location.href += separator + "id=" + response.data.id; 
                            }
                        }
                        if (method == 'PATCH') {
                            if (jqXHR.status == 207) {
                                jqXHR = Object.assign(jqXHR, {"messageType":"warning"});
                                emitSignal(jqXHR);
                                $('#server_response span.mymessage').html(jqXHR.statusText);
                                $('#server_response').addClass("alert-warning show");
                            } else {
                                emitSignal(jqXHR);
                                window.location.reload()
                            }
                        }
                    },
                    "error": function(jqXHR) {
                        jqXHR = Object.assign(jqXHR, {"messageType":"danger"});
                        emitSignal(jqXHR);
                        $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                        $('#server_response').addClass("alert-danger show");
                    }
                });
                
            });
            
            // Hook up the Restore to Default button
            $('#restore').on('click',function() {
                editor.setValue(starting_value);
            });
            
            // Hook up the validation indicator to update its 
            // status whenever the editor changes
            editor.on('change',function() {

                if (editor) {
                    // Get an array of errors from the validator
                    var errors = editor.validate();
                    
                    // Not valid
                    if(errors.length) {
                        $("#valid_indicator").removeClass();
                        $("#valid_indicator").addClass('mt-1 alert alert-danger');
                        let html = '<b>Not valid</b>:<br><ul>';
                        for(var i=0; i<errors.length; i++) {
                            html += '<li>' + errors[i].path + ': ' + errors[i].message + '</li>';
                        }
                        html += '</ul>';
                        $("#valid_indicator").html(html);
                        $("#submit").attr("disabled", true);
                    }
                    // Valid
                    else {
                        $("#valid_indicator").removeClass();
                        $("#valid_indicator").addClass('mt-1 alert alert-success');
                        $("#valid_indicator").html('Valid');
                        $("#submit").attr("disabled", false);
                    }
                }
            });
        }

        // fill lists used for select form elements
        function fillEnum(data, propertyKey) {
            var custom_enum = new Array();
            var custom_enum_titles = new Array();
            custom_enum.push(null);
            custom_enum_titles.push("--- Select one ---");
            for (var i=0; i<data.length; i++) {
                custom_enum.push(data[i].id);
                custom_enum_titles.push(data[i].name);
            }
            mySchema.properties[propertyKey].enum = custom_enum;
            mySchema.properties[propertyKey].options.enum_titles = custom_enum_titles;
        }

        // dispatch event if loaded from a parent frame
        function emitSignal(xhr=null) {
            try {
                var event = new CustomEvent('sensorEdit', {"detail": xhr} );
                window.parent.document.dispatchEvent(event)
            } catch (e) {
                console.log(e);
            }
        }
    </script>
</body>
</html>