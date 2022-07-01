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
    <script src="https://cdn.jsdelivr.net/npm/@json-editor/json-editor@latest/dist/jsoneditor.min.js"></script>
</head>
<body>
    <div class='container-flex'>
        <div class='columns'>
            <div class='column col-md-12' id='editor_holder'></div>
        </div>
        <p class='columns'>
            <div class='column col-md-12'>
                <button id='check' class='btn btn-success'>Validate</button>  
                <button id='restore' class='btn btn-secondary'>Restore to Default</button>
                <div id='valid_indicator' class='mt-1 alert alert-danger'></div>
                <button id='submit' class='btn btn-primary' disabled>Submit</button>  
                <?php if ($id) { ?>
                <button id='historicize' class="btn btn-danger">Historicize sensor</button>
                <?php } ?>
            </div>
        </p>
        <div class='columns'>
            <div class='column col-md-12 text-danger' id='server_response'></div>
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
        var mySensortypeSchemas = {};

        // Load schema and data
        $.ajax({
            "url": ref,
            "success": function(data) {
                mySchema = data;
                handleInputID();
                // get list of nets
                $.ajax({
                    "url": "../../nets",
                    "success": function(response) {
                        fillEnum(response.data, "net_id");
                        // get list of sites
                        $.ajax({
                            "url": "../../sites",
                            "success": function(response) {
                                fillEnum(response.data, "site_id");
                                // get list of sensortypes
                                $.ajax({
                                    "url": "../../sensortypes",
                                    "success": function(response) {
                                        fillEnum(response.data, "sensortype_id");
                                        // save schemas for each sensortype
                                        for (let i=0; i < response.data.length; i++) {
                                            mySensortypeSchemas[response.data[i].id] = response.data[i].json_schema;
                                        }
                                        // load sensor data if sensor_id is defined
                                        if (id) {
                                            $.ajax({
                                                "url": route,
                                                "data": {
                                                    "id": id
                                                },
                                                "success": function(starting_value) {
                                                    // set the default starting value (JSON) with data of sensor with selected id 
                                                    default_starting_value = preprocessData(starting_value.data[0]);
                                                    // update schema and start editor
                                                    mySchema.properties.net_id.default = starting_value.data[0].net_id;
                                                    mySchema.properties.site_id.default = starting_value.data[0].site_id;
                                                    mySchema.properties.sensortype_id.default = starting_value.data[0].sensortype_id;
                                                    startEditor();
                                                }
                                            });
                                        } else {
                                            // start editor
                                            startEditor();
                                        }
                                        
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });

        // set historicize button action
        $("#historicize").click(function(){
            if (confirm("This action will copy the sensor data into 'Custom info > History' section and then reset the form") == true) {
                historicizeSensor();
            }
        });

        function historicizeSensor() {
            let currTime = new Date();
            // get current json data (without history)
            j = Object.assign({}, editor.getValue());
            delete j.custom_props.history;
            console.log(j);

            // select the target where current data will be copied
            selector = 'root.custom_props.history'
            target = editor.getEditor(selector)
            editor.getEditor(selector).activate();

            // get target content
            current_content = target.getValue();

            // add new record to current_content array
            curr_length = current_content.push(null);
            target.setValue(current_content);

            // copy json data into new target
            selector = 'root.custom_props.history.' + (curr_length - 1) + '.record'
            new_target = editor.getEditor(selector);
            editor.getEditor(selector).activate();
            new_target.setValue(j);

            // set endtime to current time by default
            selector = 'root.custom_props.history.' + (curr_length - 1) + '.endtime';
            endtime = editor.getEditor(selector);
            editor.getEditor(selector).activate();
            endtime.setValue(currTime.toISOString().substr(0,10));
        }

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

            // update schema by setting metadata schema with selected sensortype schema
            //console.log(default_starting_value);
            if (default_starting_value !== null && default_starting_value["sensortype_id"] !== undefined) {
                mySchema.properties.metadata = mySensortypeSchemas[default_starting_value["sensortype_id"]];
            }

            // initialize editor with the default starting JSON value
            initializeEditor(default_starting_value);
        }

        function preprocessData(initData) {
            initData["lon"] = initData.coords.coordinates[0];
            initData["lat"] = initData.coords.coordinates[1];
            delete initData.coords;
            return initData;
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

                // add functionalities: 
                // change metadata (sensortype properties) editor section when change sensortype_id selection
                $(document.getElementById("root[sensortype_id]")).on("change", function(event) {
                    // set the new default starting value to current editor value 
                    // (current JSON = current user edited data)
                    default_starting_value = editor.getValue();
                    // restart editor with new specific sensortype properties form 
                    // and fill the new editor with current data
                    startEditor();
                });
            });
            
            // Hook up the submit button to log to the console
            $('#submit').on('click',function() {
                // Get the value from the editor
                console.log(editor.getValue());

                // get JSON to post
                var toPost = editor.getValue();

                // preprocessing JSON data before post
                if (toPost["metadata"]) {
                    delete toPost.metadata.id;
                    //toPost.metadata = JSON.stringify(toPost.metadata);
                }
                /*
                if (toPost["custom_props"]) {
                    toPost.custom_props = JSON.stringify(toPost.custom_props);
                }
                */
                //console.log(toPost);
                
                // PATCH if id is indicated, else POST
                $.ajax({
                    "url": route,
                    "data": JSON.stringify(toPost),
                    "method": method,
                    "success": function(response) {
                        if (method == 'POST') {
                            window.location.href += "?id=" + response.data.id; 
                        }
                        if (method == 'PATCH') {
                            window.location.reload()
                        }
                    },
                    "error": function(xhr) {
                        $('#server_response').html(xhr.responseJSON.error)
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
                custom_enum_titles.push(data[i].name);
            }
            mySchema.properties[propertyKey].enum = custom_enum;
            mySchema.properties[propertyKey].options.enum_titles = custom_enum_titles;
        }
    </script>
</body>
</html>