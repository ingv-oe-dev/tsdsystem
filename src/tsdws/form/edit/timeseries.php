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
        <p class='columns'>
            <div class='column col-md-12'>
                <button id='check' class='btn btn-success'>Validate</button>  
                <button id='restore' class='btn btn-secondary'>Restore to Default</button>
                <div id='valid_indicator' class='mt-1 alert alert-danger'></div>
                <button id='submit' class='btn btn-primary' disabled>Submit</button>  
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
        var ref = "../../json-schemas/timeseries.json";
        var route = "../../timeseries?showColDefs=true&showMapping=true";
        var method =  id ? "PATCH" : "POST";
        var mySchema = {};
        var selectedChannelIDs4Mapping = [];

        // Load schema
        $.ajax({
            "url": ref,
            "success": function(data) {
                mySchema = data;
                handleInputID();
                $.ajax({
                    "url": "../../channels",
                    "success": function(response) {
                        fillChannelList(response.data);
                        startEditor();
                    }
                });
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

        function startEditor() {
            if (id) {
                $.ajax({
                    "url": route,
                    "data": {
                        "id": id
                    },
                    "success": function(starting_value) {
                        initializeEditor(starting_value.data[0]);
                    }
                });
            } else {
                initializeEditor(default_starting_value);
            }
        }

        function initializeEditor(starting_value) {
        
            const container = document.getElementById('editor_holder')

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

                array_controls_top: true,
                prompt_before_delete: false,
                show_opt_in: true
            });

            editor.on('ready',() => {
                // Now the api methods will be available
                editor.validate();

                Array.prototype.unique = function() {
                    var a = this.concat();
                    for(var i=0; i<a.length; ++i) {
                        for(var j=i+1; j<a.length; ++j) {
                            if(a[i] === a[j])
                                a.splice(j--, 1);
                        }
                    }

                    return a;
                };

                // add functionalities: 
                $(document.getElementById("root[mapping][channel_list]")).on("change", function(event) {
                    let channel_list = $(event.currentTarget).val();
                    //console.log(channel_list);
                    if(editor.getEditor('root.mapping.add_channel_mode').getValue()) {
                        console.log(selectedChannelIDs4Mapping.concat(channel_list).unique());
                        selectedChannelIDs4Mapping = selectedChannelIDs4Mapping.concat(channel_list).unique();
                        // `getEditor` will return null if the path is invalid
                        let channel_id = editor.getEditor('root.mapping.channel_id');
                        channel_id.setValue(selectedChannelIDs4Mapping);
                    }
                });

                $(document.getElementById("root[mapping][add_channel_mode]")).on("change", function(event) {
                    let disabled = !editor.getEditor('root.mapping.add_channel_mode').getValue();
                    $(document.getElementById("root[mapping][channel_list]")).prop('disabled', disabled);
                });
            });
            
            // Hook up the submit button to log to the console
            $('#submit').on('click',function() {
                // Get the value from the editor
                console.log(editor.getValue());

                var toPost = editor.getValue();
                if (toPost["metadata"]) {
                    toPost.metadata = JSON.stringify(toPost.metadata);
                }
                
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

                // custom
                var i=0;
                selectedChannelIDs4Mapping = [];
                while (document.getElementById("root[mapping][channel_id][" + i + "]") !== null) {
                    let value = $(document.getElementById("root[mapping][channel_id][" + i + "]")).val();
                    selectedChannelIDs4Mapping.push(value);
                    i++;
                } 
                
            });
        }

        function fillChannelList(data) {
            var custom_enum = new Array();
            var custom_enum_titles = new Array();
            for (var i=0; i<data.length; i++) {
                custom_enum.push(data[i].id);
                custom_enum_titles.push(data[i].net_name + " -> " + data[i].sensor_name + " -> " + data[i].name + " [ID: " + data[i].id + "]");
            }
            //console.log(mySchema);
            mySchema.properties.mapping.properties.channel_list.items.enum = custom_enum;
            mySchema.properties.mapping.properties.channel_list.items.options.enum_titles = custom_enum_titles;
        }
    </script>
</body>
</html>