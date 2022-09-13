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
                <?php 
                    if ($id) {
                        echo "<button id='delete' class='btn btn-danger'>Delete item [id=" . $id . "]</button>";
                    } 
                ?>
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
        var ref = "../../json-schemas/nets.json";
        var route = "../../nets";
        var method =  id ? "PATCH" : "POST";
        var mySchema = {};

        // Set action on delete button
        $(function(){
            $("button#delete").on("click", function(){
                if (confirm("This action will remove record with id=" + id + ". Continue?") == true) {
                    $.ajax({
                        "url": route + "?id=" + id,
                        "method": "DELETE",
                        "success": function(response) {
                            emitSignal();
                            alert("Record with id=" + id + " removed successfully!");
                            let new_location = window.location.href.split('?')[0];
                            window.location.href = new_location;
                        },
                        "error": function(xhr) {
                            $('#server_response').html(xhr.responseJSON.error)
                        }
                    });
                }
            });  
        });

        // Load schema
        $.ajax({
            "url": ref,
            "success": function(data) {
                mySchema = data;
                handleInputID();
                $.ajax({
                    "url": "../../owners",
                    "success": function(response) {
                        fillEnum(response.data, "owner_id");
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
                        mySchema.properties.owner_id.default = starting_value.data[0].owner_id;
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

                show_opt_in: true
            });

            editor.on('ready',() => {
                // Now the api methods will be available
                editor.validate();
            });
            
            // Hook up the submit button to log to the console
            $('#submit').on('click',function() {
                // Get the value from the editor
                console.log(editor.getValue());

                // PATCH if id is indicated, else POST
                $.ajax({
                    "url": route,
                    "data": JSON.stringify(editor.getValue()),
                    "method": method,
                    "success": function(response) {
                        emitSignal();
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
            });
        }

        function fillEnum(data, propertyKey) {
            console.log(data);
            var custom_enum = new Array();
            var custom_enum_titles = new Array();
            for (var i=0; i<data.length; i++) {
                custom_enum.push(data[i].id);
                custom_enum_titles.push(data[i].name);
            }
            mySchema.properties[propertyKey].enum = custom_enum;
            mySchema.properties[propertyKey].options.enum_titles = custom_enum_titles;
        }

        // dispatch event if loaded from a parent frame
        function emitSignal() {
            try {
                var event = new CustomEvent('toParentEvent');
                window.parent.document.dispatchEvent(event)
            } catch (e) {
                console.log(e);
            }
        }
    </script>
</body>
</html>