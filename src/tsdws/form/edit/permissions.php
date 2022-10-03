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
        var default_starting_value = {
            "role_type": "role",
            "role_id": null
        };
        
        var ref = "../../json-schemas/resource_permissions_form.json";
        var route = "../../permissions/";
        var method =  "POST";
        var mySchema = {};

        // Load schema
        $.ajax({
            "url": ref,
            "success": function(data) {
                mySchema = data;
                startEditor();
            }
        });

        function resetEditor() {
            // make empty the JSON editor container
            $('#editor_holder').html('');

            // reset to null the variable representing the JSON editor11
            editor = null;
        }

        function startEditor() {

            console.log(default_starting_value);

            // reset editor
            resetEditor();

            $.ajax({
                "url": (default_starting_value.role_type == 'role' ? "../../roles" : "../../users"),
                "success": function(response) {
                    fillEnum(response.data, "role_id");
                    if (default_starting_value.role_id) {
                        $.ajax({
                            "url": route + default_starting_value.role_type,
                            "data": {
                                "role_id": default_starting_value.role_id
                            },
                            "success": function(response) {
                                if (response && response.data && response.data.length > 0) {
                                    let current_role_type = default_starting_value.role_type;
                                    default_starting_value = response.data[0];
                                    default_starting_value.role_type = current_role_type;
                                } else {
                                    default_starting_value.settings = {}
                                }
                                initializeEditor(default_starting_value);
                            },
                            "error": function(jqXHR) {
                                $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                                $('#server_response').addClass("alert-danger show");
                            }
                        });
                    } else {
                        initializeEditor(default_starting_value);
                    }
                },
                "error": function(jqXHR) {
                    $('#server_response span.mymessage').html(jqXHR.responseJSON.error);
                    $('#server_response').addClass("alert-danger show");
                }
            });
        }

        function initializeEditor(starting_value) {

            console.log(starting_value);
        
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

                show_opt_in: true
            });

            editor.on('ready',() => {
                // Now the api methods will be available
                editor.validate();

                // add functionalities: 
                // change role_type -> load roles or members list
                $(document.getElementById("root[role_type]")).on("change", function(event) {
                    // set the new default starting value to current editor value 
                    // (current JSON = current user edited data)
                    default_starting_value = editor.getValue();
                    default_starting_value.role_id = null;
                    // restart editor with new specific sensortype properties form 
                    // and fill the new editor with current data
                    startEditor();
                });

                // change role_id -> load settings if exists
                $(document.getElementById("root[role_id]")).on("change", function(event) {
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

                var toPost = editor.getValue();
                // POST
                $.ajax({
                    "url": route + toPost.role_type,
                    "data": JSON.stringify(toPost),
                    "method": method,
                    "success": function(response) {
                        window.location.href += "?role_type=" + role_type + "&role_id=" + editor.getEditor('root.role_id').getValue();
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