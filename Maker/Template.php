<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BBC Maker</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <style>
        .code {
            height: auto;
            max-height: 10em;
            overflow: auto !important;
            word-break: normal !important;
            word-wrap: normal !important;
            width: 30em;
            margin-bottom: .5em;
        }
    </style>

</head>
<body>
<div class="container-fluid">
    <div id="top">
        <h1>BBC Maker</h1>
    </div>
    <form method="get" class="form-horizontal">
        <div class="form-group">
            <label for="type" class="col-md-4 control-label">Type</label>
            <div class="col-md-8">
                <select name="type" class="form-control">
                    <?php

                    foreach ($export->getTypes() as $value => $name)
                    {
                        echo '
                        <option value="', $value, '"', $selected['type'] === $value ? ' selected' : '', '>', $name, '</option>';
                    }

                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="a" class="col-sm-4 control-label">Tag</label>
            <div class="col-sm-8">
                <input type="text" name="tag" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="a" class="col-sm-4 control-label">Test</label>
            <div class="col-sm-8">
                <input type="text" name="test" class="form-control">
            </div>
        </div>
        <div class="form-group">
            only for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content
            <label for="content" class="col-sm-4 control-label">Content</label>
            <div class="col-sm-8">
                <input type="text" name="content" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="before" class="col-sm-4 control-label">Before</label>
            <div class="col-sm-8">
                <input type="text" name="before" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="after" class="col-sm-4 control-label">After</label>
            <div class="col-sm-8">
                <input type="text" name="after" class="form-control">
            </div>
        </div>
        <div class="form-group">
            only for unparsed_content, closed, unparsed_commas_content, and unparsed_equals_content
            <label for="disabled_content" class="col-sm-4 control-label">Disabled Content</label>
            <div class="col-sm-8">
                <input type="text" name="disabled_content" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="disabled_before" class="col-sm-4 control-label">Disabled Before</label>
            <div class="col-sm-8">
                <input type="text" name="disabled_before" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="disabled_after" class="col-sm-4 control-label">Disabled After</label>
            <div class="col-sm-8">
                <input type="text" name="disabled_after" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="block_level" class="col-sm-4 control-label">Block Level</label>
            <div class="col-sm-8">
                <input name="block_level" type="checkbox" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="trim" class="col-md-4 control-label">Trim</label>
            <div class="col-md-8">
                <select name="type" class="form-control">
                    <option value=""></option>
                    <option value="">Inside</option>
                    <option value="">Outside</option>
                    <option value="">Both</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="validate" class="col-sm-4 control-label">Validate</label>
            <div class="col-sm-8">
                <textarea name="validate" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="quoted" class="col-md-4 control-label">Quotes</label>
            <div class="col-md-8">
                <select name="quoted" class="form-control">
                    <option value=""></option>
                    <option value="">Required</option>
                    <option value="">Optional</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="require_parents" class="col-sm-4 control-label">Require Parents</label>
            <div class="col-sm-8">
                <input type="text" name="require_parents" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="require_children" class="col-sm-4 control-label">Require Children</label>
            <div class="col-sm-8">
                <input type="text" name="require_children" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_parents" class="col-sm-4 control-label">Disallow Parents</label>
            <div class="col-sm-8">
                <input type="text" name="disallow_parents" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_children" class="col-sm-4 control-label">Disallow Children</label>
            <div class="col-sm-8">
                <input type="text" name="disallow_children" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_before" class="col-sm-4 control-label">Disallow Before</label>
            <div class="col-sm-8">
                <input type="text" name="disallow_before" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="disallow_after" class="col-sm-4 control-label">Disallow After</label>
            <div class="col-sm-8">
                <input type="text" name="disallow_after" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="parsed_tags_allowed" class="col-sm-4 control-label">Parsed Tags Allowed</label>
            <div class="col-sm-8">
                <input type="text" name="parsed_tags_allowed" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="autolink" class="col-sm-4 control-label">Autolink</label>
            <div class="col-sm-8">
                <input name="autolink" type="checkbox" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="no_cache" class="col-sm-4 control-label">Not Cacheable</label>
            <div class="col-sm-8">
                <input name="no_cache" type="checkbox" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="no_cache" class="col-sm-4 control-label">Parameters</label>
            <div class="col-sm-8">
                <div class="input_fields_wrap">
                    <button class="add_field_button">Add More Fields</button>
                    <div><input type="text" name="mytext[]"></div>
                </div>
                <input name="no_cache" type="checkbox" class="form-control">
            </div>
        </div>
    </form>

    <div>
        <h2>Results</h2>
        <pre></pre>
    </div>
</div>
</body>

<div class="form-group params" style="display:none">
    <div class="col-md-offset-4 col-sm-8">
        <label for="no_cache" class="col-sm-2 control-label">Not Cacheable</label>
        <div class="col-sm-6">
            <input type="text" name="param_name[]" class="form-control">
            <input type="text" name="param_match[]" class="form-control">
            <select name="param_quoted" class="form-control">
                <option value=""></option>
                <option value="">Required</option>
                <option value="">Optional</option>
            </select>
            <textarea name="param_validate[]" class="form-control"></textarea>
            <input type="text" name="param_validate[]" class="form-control">
            <input type="text" name="param_value[]" class="form-control">
            <input type="checkbox" name="param_optional[]" class="form-control">
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper         = $(".input_fields_wrap"); //Fields wrapper
        var add_button      = $(".add_field_button"); //Add button ID
        var param_div       = $('.params');

        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                x++; //text box increment
                $(wrapper).append(param_div.clone().show()); //add input box
            }
        });

        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault();
            $(this).parent('div').remove();
            x--;
        })
    });
</script>

</html>