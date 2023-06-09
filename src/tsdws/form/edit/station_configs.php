<?php
    $id = isset($_GET["id"]) ? $_GET["id"] : null;
    $station_id = isset($_GET["station_id"]) ? $_GET["station_id"] : null;
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
        var default_starting_value = {};
        var id = "<?php echo $id; ?>";
        var station_id = "<?php echo $station_id; ?>";
        var ref = "../../json-schemas/station_configs.json";
        var route = "../../stations/configs";
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
                            jqXHR = Object.assign(jqXHR, {"messageText":"Remove station config [id=" + id + "]", "station_id": station_id}, settings);
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
                // get list of stations
                $.ajax({
                    "url": "../../stations",
                    "data": {
                        "sort_by": "name"
                    },
                    "success": function(response) {
                        fillEnum(response.data, "station_id");
                        // get list of sensors
                        $.ajax({
                            "url": "../../sensors",
                            "data": {
                                "sort_by": "name"
                            },
                            "success": function(response) {
                                fillEnum(response.data, "sensor_id");
                                // get list of digitizers
                                $.ajax({
                                    "url": "../../digitizers",
                                    "data": {
                                        "sort_by": "name"
                                    },
                                    "success": function(response) {
                                        fillEnum(response.data, "digitizer_id");
                                        // load station config data if id is defined
                                        if (id) {
                                            $.ajax({
                                                "url": route,
                                                "data": {
                                                    "id": id
                                                },
                                                "success": function(starting_value) {
                                                    // set the default starting value (JSON) with data of station config with selected id 
                                                    default_starting_value = starting_value.data[0];
                                                    // update schema and start editor
                                                    mySchema.properties.station_id.default = starting_value.data[0].station_id;
                                                    mySchema.properties.station_id.readOnly = true;
                                                    mySchema.properties.sensor_id.default = starting_value.data[0].sensor_id;
                                                    mySchema.properties.digitizer_id.default = starting_value.data[0].digitizer_id;
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
            if (station_id) default_starting_value["station_id"] = station_id;
            initializeEditor(default_starting_value);
        }

        // Custom validators must return an array of errors or an empty array if valid
		JSONEditor.defaults.custom_validators.push((schema, value, path) => {
		  const errors = [];
          if (!value) return errors;
		  if (schema.format==="date-time") {
			if (!/^\d{4}-(0\d|1[0-2])-([0-2]\d|3[0-2])([T|\s{1}](([01]\d|2[0-4]):([0-5]\d)(:[0-5]\d([\.,]\d+)?)?|([01]\d|2[0-4])(:[0-5]\d([\.,]\d+)?)?|([01]\d|2[0-4])([\.,]\d+)?))?([+-]\d\d(:[0-5]\d)?|Z)?$/.test(value)) {
			  // Errors must be an object with `path`, `property`, and `message`
			  errors.push({
				path: path,
				property: 'format',
				message: 'Dates must be in the ISO 8601 format (ex. 2022-01-01 00:00:00)'
			  });
			}
		  }
		  return errors;
		});

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
                        jqXHR = Object.assign(jqXHR, settings, {"station_id": station_id, "id": id});
                        if (method == 'POST') {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Add station config"});
                        }
                        if (method == 'PATCH') {
                            jqXHR = Object.assign(jqXHR, {"messageText":"Edit station config [id=" + id + "]"});
                        }
                    },
                    "success": function(response, textStatus, jqXHR) {
                        //console.log(jqXHR);
                        jqXHR = Object.assign(jqXHR, {"messageType":"success"});
                        if (method == 'POST') {
                            if (jqXHR.status == 207) {
                                jqXHR = Object.assign(jqXHR, {"messageType":"warning"});
                                console.log(jqXHR);
                                emitSignal(jqXHR);
                                $('#server_response span.mymessage').html(jqXHR.statusText);
                                $('#server_response').addClass("alert-warning show");
                            } else {
                                console.log(jqXHR);
                                emitSignal(jqXHR);
                                if (response.data.warning) {
                                    jqXHR = Object.assign(jqXHR, {"messageType":"warning", "messageText": response.data.warning});
                                    console.log(jqXHR);
                                    emitSignal(jqXHR);
                                }
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
           // console.log(data);
            var custom_enum = new Array();
            var custom_enum_titles = new Array();
            custom_enum.push(null);
            custom_enum_titles.push("--- Select one ---");
            for (var i=0; i<data.length; i++) {
                custom_enum.push(data[i].id);
                switch (propertyKey) {
                    case "sensor_id":
                        custom_enum_titles.push(data[i].name + (data[i].sensortype_components ? new String(" (" + data[i].sensortype_components.length) + " comp.)" : "") + (data[i].sensortype_model ? new String(" - Model: " + data[i].sensortype_model) : "") + (data[i].serial_number ? new String(" - SN: " + data[i].serial_number) : ""));
                        break;
                    case "digitizer_id":
                        custom_enum_titles.push(data[i].name + (data[i].digitizertype_model ? new String(" (" + data[i].digitizertype_model) + (data[i].dynamical_range ? (" - " + data[i].dynamical_range + "-VPP") : "") + (data[i].final_sample_rate ? (" - " + data[i].final_sample_rate) : "") + (data[i].final_sample_rate_measure_unit ? data[i].final_sample_rate_measure_unit : "") + " )" : "") + (data[i].serial_number ? new String(" - SN: " + data[i].serial_number) : ""));
                        break;
                    default:
                        custom_enum_titles.push(data[i].name);
                        break;
                }
            }
            mySchema.properties[propertyKey].enum = custom_enum;
            mySchema.properties[propertyKey].options.enum_titles = custom_enum_titles;
        }

        // dispatch event if loaded from a parent frame
        function emitSignal(xhr=null) {
            try {
                var event = new CustomEvent('stationConfigEdit', {"detail": xhr} );
                window.parent.document.dispatchEvent(event)
            } catch (e) {
                console.log(e);
            }
        }
    </script>
</body>
</html>